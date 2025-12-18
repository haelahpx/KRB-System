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
     | FLOW STATE (TICKET)
     ========================= */
    public bool $waitingForProblemDescription = false;
    public bool $needsConfirmation = false;

    public ?string $ticketQuestion = null;
    public ?string $ticketSummary = null;
    public ?int $selectedDepartmentId = null;
    public string $ticketPriority = 'medium';
    public string $aiReasoning = '';

    public array $availableDepartments = [];

    /* =========================
     | NLU STATE
     ========================= */
    public string $detectedIntent = 'unknown';

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
- KRS / KRBS = Sistem internal Kebun Raya
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

        $this->detectedIntent = $this->detectIntent($text);

        /* BLOCK DURING CONFIRM */
        if ($this->needsConfirmation) {
            $this->messages[] = [
                'role' => 'model',
                'text' => 'Silakan konfirmasi atau batalkan tiket terlebih dahulu.'
            ];
            return;
        }

        /* GREETING */
        if ($this->detectedIntent === 'greeting') {
            $this->messages[] = [
                'role' => 'model',
                'text' => 'Halo! Ada yang bisa saya bantu?'
            ];
            return;
        }

        /* START TICKET FLOW (UNCHANGED) */
        if (
            !$this->waitingForProblemDescription &&
            $this->detectedIntent === 'create_ticket'
        ) {
            $this->waitingForProblemDescription = true;
            $this->messages[] = [
                'role' => 'model',
                'text' => 'Baik. Bisa jelaskan masalah yang Anda alami?'
            ];
            return;
        }

        /* USER DESCRIBES PROBLEM */
        if ($this->waitingForProblemDescription) {
            $this->waitingForProblemDescription = false;
            $this->ticketQuestion = $text;
            $this->dispatch('startAiResponse');
            return;
        }

        /* FAQ FIRST (SMART) */
        $faq = $this->matchFaqAdvanced($text);

        if ($faq && in_array($this->detectedIntent, ['ask_faq', 'ask_definition'])) {
            $this->messages[] = [
                'role' => 'model',
                'text' => $faq['answer']
            ];
            return;
        }

        /* AI FALLBACK */
        $this->ticketQuestion = $text;
        $this->dispatch('startAiResponse');
    }

    /* =========================
     | INTENT + NORMALIZATION
     ========================= */
    private function detectIntent(string $text): string
    {
        $text = $this->normalizeText($text);

        if (preg_match('/\b(halo|hai|hi|hello)\b/', $text)) return 'greeting';
        if (preg_match('/\b(apa itu|apakah|jelaskan|arti|maksud)\b/', $text)) return 'ask_definition';
        if (preg_match('/\b(tiket|lapor|bantu|error|masalah)\b/', $text)) return 'create_ticket';

        return 'ask_faq';
    }

    private function normalizeText(string $text): string
    {
        $text = strtolower($text);

        $map = [
            'apasi' => 'apa sih',
            'apasih' => 'apa sih',
            'apaan' => 'apa',
            'dong' => '',
            '?' => '',
        ];

        $text = str_replace(array_keys($map), array_values($map), $text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    /* =========================
     | FAQ LOADER (NEW STRUCTURE)
     ========================= */
    private function loadFaqQuestions(): array
    {
        $path = storage_path('app/' . self::FAQ_FILE_PATH);
        if (!File::exists($path)) return [];

        $json = json_decode(File::get($path), true);
        if (!$json || !isset($json['categories'])) return [];

        $items = [];

        foreach ($json['categories'] as $category) {
            foreach ($category['questions'] ?? [] as $q) {
                $items[] = [
                    'id' => $q['id'] ?? null,
                    'question' => $q['question'] ?? '',
                    'answer' => $q['answer'] ?? '',
                    'keywords' => $q['keywords'] ?? [],
                    'category' => $category['category'] ?? 'Umum',
                ];
            }
        }

        return $items;
    }

    private function matchFaqAdvanced(string $input): ?array
    {
        $inputNorm = $this->normalizeText($input);
        $bestScore = 0;
        $best = null;

        foreach ($this->loadFaqQuestions() as $faq) {
            $score = 0;

            /* KEYWORD MATCH (STRONG) */
            foreach ($faq['keywords'] as $kw) {
                $kwNorm = $this->normalizeText($kw);
                if ($kwNorm && str_contains($inputNorm, $kwNorm)) {
                    $score += 5;
                }
            }

            /* SEMANTIC MATCH */
            $qNorm = $this->normalizeText($faq['question']);
            similar_text($inputNorm, $qNorm, $sim);
            $score += ($sim / 10);

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $faq;
            }
        }

        return $bestScore >= 6 ? $best : null;
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

            /* Inject FAQ context (compact) */
            $faqContext = collect($this->loadFaqQuestions())
                ->take(30)
                ->map(fn ($f) => "- {$f['question']}: {$f['answer']}")
                ->implode("\n");

            array_unshift($history, [
                'role' => 'system',
                'content' => "FAQ INTERNAL KRBS:\n" . $faqContext
            ]);

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
     | AI OUTPUT + TICKET FLOW
     ========================= */
    private function handleAiOutput(string $responseText)
    {
        if (!preg_match('/\{.*"action".*\}/s', $responseText, $matches)) {
            $this->messages[] = [
                'role' => 'model',
                'text' => trim($responseText)
            ];
            return;
        }

        $data = json_decode($matches[0], true);
        if (!$data || ($data['action'] ?? '') !== 'create_ticket') return;

        if (!$this->ticketQuestion || strlen($this->ticketQuestion) < 10) {
            $this->messages[] = [
                'role' => 'model',
                'text' => 'Mohon jelaskan masalah secara lebih detail.'
            ];
            return;
        }

        $this->ticketSummary = $data['summary'] ?? $this->ticketQuestion;
        $this->ticketPriority = $data['priority'] ?? 'medium';
        $this->aiReasoning = $data['reason'] ?? '';

        foreach ($this->availableDepartments as $dept) {
            if (strtoupper($dept['department_name']) === strtoupper($data['department'] ?? '')) {
                $this->selectedDepartmentId = $dept['department_id'];
                break;
            }
        }

        $this->messages[] = [
            'role' => 'model',
            'text' =>
                "Saya bisa membantu membuat tiket:\n\n" .
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

    public function render()
    {
        return view('livewire.components.ui.chat-modal');
    }
}
