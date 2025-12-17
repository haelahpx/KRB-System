<?php

namespace App\Livewire\Components\Ui;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\Department;
use Throwable;

class ChatModal extends Component
{
    /* =========================
     | UI STATE
     ========================= */
    public bool $isOpen = false;
    public string $currentMessage = '';
    public array $messages = [];

    /* =========================
     | FLOW STATE
     ========================= */
    public bool $waitingForProblemDescription = false;
    public bool $needsConfirmation = false;

    public ?string $ticketQuestion = null;
    public ?string $ticketSummary = null;
    public ?int $selectedDepartmentId = null;
    public string $ticketPriority = 'medium';
    public string $aiReasoning = '';

    public array $availableDepartments = [];

    private const FAQ_FILE_PATH = 'faq_data.json';

    /* =========================
     | MOUNT
     ========================= */
    public function mount()
    {
        $this->loadDepartments();

        $this->messages[] = [
            'role' => 'system',
            'text' => $this->systemPrompt()
        ];

        $this->messages[] = [
            'role' => 'model',
            'text' => 'Halo! Saya Asisten Chat Kebun Raya. Ada yang bisa saya bantu?'
        ];
    }

    /* =========================
     | SYSTEM PROMPT
     ========================= */
    private function systemPrompt(): string
    {
        return <<<PROMPT
Anda adalah Asisten AI internal Kebun Raya (PT Mitra Natura Raya).

ATURAN WAJIB:
- KRS = Kebun Raya System
- Jangan gunakan konteks kampus, mahasiswa, atau SKS
- Jangan membuat tiket tanpa masalah yang jelas
- Jika user minta tiket → minta penjelasan masalah dulu
- Jawaban singkat dan profesional

FORMAT JSON (HANYA UNTUK SISTEM):
{
  "action": "create_ticket",
  "summary": "...",
  "department": "...",
  "priority": "low|medium|high",
  "reason": "..."
}
PROMPT;
    }

    /* =========================
     | SEND MESSAGE
     ========================= */
    public function sendMessage()
    {
        $text = trim($this->currentMessage);
        if ($text === '') return;

        $this->messages[] = ['role' => 'user', 'text' => $text];
        $this->currentMessage = '';

        $lower = strtolower($text);

        /* BLOCK INPUT DURING CONFIRMATION */
        if ($this->needsConfirmation) {
            $this->messages[] = [
                'role' => 'model',
                'text' => 'Silakan konfirmasi atau batalkan tiket terlebih dahulu.'
            ];
            return;
        }

        /* GREETING */
        if (in_array($lower, ['halo', 'hai', 'hi', 'hello'])) {
            $this->messages[] = [
                'role' => 'model',
                'text' => 'Halo! Ada yang bisa saya bantu?'
            ];
            return;
        }

        /* USER WANTS TO CREATE TICKET */
        if (
            !$this->waitingForProblemDescription &&
            preg_match('/\b(bantu|buat|bikin|tolong)\b/i', $lower) &&
            str_contains($lower, 'tiket')
        ) {
            $this->waitingForProblemDescription = true;
            $this->messages[] = [
                'role' => 'model',
                'text' => 'Baik. Bisa jelaskan masalah yang Anda alami?'
            ];
            return;
        }

        /* USER DESCRIBES THE PROBLEM */
        if ($this->waitingForProblemDescription) {
            $this->waitingForProblemDescription = false;
            $this->ticketQuestion = $text;
            $this->dispatch('startAiResponse');
            return;
        }

        /* FAQ FIRST */
        $faq = $this->matchFaq($text);
        if ($faq) {
            $this->messages[] = [
                'role' => 'model',
                'text' => $faq['jawaban']
            ];
            return;
        }

        /* NORMAL AI RESPONSE */
        $this->ticketQuestion = $text;
        $this->dispatch('startAiResponse');
    }

    /* =========================
     | FAQ
     ========================= */
    private function loadFaq(): array
    {
        $path = storage_path('app/' . self::FAQ_FILE_PATH);
        if (!File::exists($path)) return [];
        return json_decode(File::get($path), true) ?? [];
    }

    private function matchFaq(string $input): ?array
    {
        $input = strtolower($input);

        foreach ($this->loadFaq() as $faq) {
            similar_text($input, strtolower($faq['pertanyaan']), $p);
            if ($p > 45) return $faq;
        }
        return null;
    }

    /* =========================
     | AI CALL
     ========================= */
    #[On('startAiResponse')]
    public function generateAiResponse()
    {
        $response = $this->callAi();
        if ($response) {
            $this->handleAiOutput($response);
        }
    }

    private function callAi(): ?string
    {
        try {
            $history = collect($this->messages)->map(fn ($m) => [
                'role' => $m['role'] === 'model' ? 'assistant' : $m['role'],
                'content' => $m['text']
            ])->toArray();

            $res = Http::post(
                env('OLLAMA_API_URL', 'http://localhost:11434/api/chat'),
                [
                    'model' => env('OLLAMA_MODEL', 'mistral'),
                    'messages' => $history,
                    'stream' => false,
                    'options' => ['temperature' => 0.1]
                ]
            );

            return $res->json()['message']['content'] ?? null;
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    /* =========================
     | AI OUTPUT (JSON HIDDEN)
     ========================= */
    private function handleAiOutput(string $responseText)
    {
        // If no JSON → normal chat
        if (!preg_match('/\{.*"action".*\}/s', $responseText, $matches)) {
            $this->messages[] = [
                'role' => 'model',
                'text' => trim($responseText)
            ];
            return;
        }

        // Parse JSON silently
        $data = json_decode($matches[0], true);
        if (!$data || ($data['action'] ?? '') !== 'create_ticket') return;

        // Safety check
        if (!$this->ticketQuestion || strlen($this->ticketQuestion) < 10) {
            $this->messages[] = [
                'role' => 'model',
                'text' => 'Mohon jelaskan masalah secara lebih detail.'
            ];
            return;
        }

        /* STORE DATA */
        $this->ticketSummary  = $data['summary'] ?? $this->ticketQuestion;
        $this->ticketPriority = $data['priority'] ?? 'medium';
        $this->aiReasoning    = $data['reason'] ?? '';

        // Match department
        $this->selectedDepartmentId = null;
        $targetDept = strtoupper(trim($data['department'] ?? ''));

        foreach ($this->availableDepartments as $dept) {
            if (strtoupper($dept['department_name']) === $targetDept) {
                $this->selectedDepartmentId = $dept['department_id'];
                break;
            }
        }

        /* SHOW CONFIRMATION (NO JSON) */
        $this->messages[] = [
            'role' => 'model',
            'text' =>
                "Saya bisa membantu membuat tiket untuk masalah berikut:\n\n" .
                "📝 {$this->ticketSummary}\n" .
                "📌 Prioritas: " . strtoupper($this->ticketPriority) . "\n\n" .
                "Silakan konfirmasi untuk melanjutkan."
        ];

        $this->needsConfirmation = true;
    }

    /* =========================
     | TICKET ACTIONS
     ========================= */
    public function confirmTicket()
    {
        if (!$this->selectedDepartmentId) return;

        $ticket = Ticket::create([
            'company_id' => Auth::user()->company_id,
            'requestdept_id' => Auth::user()->department_id,
            'department_id' => $this->selectedDepartmentId,
            'user_id' => Auth::id(),
            'subject' => 'AI Ticket: ' . $this->ticketSummary,
            'description' => $this->ticketQuestion . "\n\nAI Reason: " . $this->aiReasoning,
            'priority' => strtoupper($this->ticketPriority),
            'status' => 'OPEN'
        ]);

        $this->messages[] = [
            'role' => 'model',
            'text' => "✅ Tiket #{$ticket->ticket_id} berhasil dibuat."
        ];

        $this->resetFlow();
    }

    public function cancelTicket()
    {
        $this->messages[] = [
            'role' => 'model',
            'text' => 'Pembuatan tiket dibatalkan.'
        ];

        $this->resetFlow();
    }

    private function resetFlow()
    {
        $this->waitingForProblemDescription = false;
        $this->needsConfirmation = false;
        $this->ticketQuestion = null;
        $this->ticketSummary = null;
        $this->aiReasoning = '';
        $this->selectedDepartmentId = null;
    }

    /* =========================
     | DEPARTMENTS
     ========================= */
    private function loadDepartments()
    {
        if (!Auth::check()) return;

        $this->availableDepartments = Department::where(
            'company_id',
            Auth::user()->company_id
        )->get(['department_id', 'department_name'])->toArray();
    }

    #[On('toggleChatModal')]
    public function toggleChatModal()
    {
        $this->isOpen = !$this->isOpen;
        $this->dispatch('chat-modal-status', isOpen: $this->isOpen);
    }

    /* =========================
     | RENDER
     ========================= */
    public function render()
    {
        return view('livewire.components.ui.chat-modal');
    }
}
