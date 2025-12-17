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
use App\Models\Wifi;
use Throwable;

class ChatModal extends Component
{
    public $isOpen = false;
    public $currentMessage = '';
    public $messages = [];

    public string $ticketPriority = 'medium';
    public string $aiReasoning = '';

    private const FAQ_FILE_PATH = 'faq_data.json';

    public bool $needsConfirmation = false;
    public ?string $ticketSummary = null;
    public ?string $ticketQuestion = null;
    public ?int $selectedDepartmentId = null;
    public array $availableDepartments = [];
    public string $ticketPhase = 'idle';

    public function mount()
    {
        $user = Auth::user();

        if ($user) {
            $this->availableDepartments = Department::query()
                ->when($user->company_id, fn($q) => $q->where('company_id', $user->company_id))
                ->orderBy('department_name', 'asc')
                ->get(['department_id', 'department_name'])
                ->toArray();

            if ($user->department_id) {
                $this->selectedDepartmentId = $user->department_id;
            } else if (!empty($this->availableDepartments)) {
                $this->selectedDepartmentId = $this->availableDepartments[0]['department_id'];
            }
        }

        $userName = optional($user)->name ?? '';
        $welcomeMessage = 'Halo' . ($userName ? ', ' . $userName : '') . '! Saya adalah Asisten Chat. Apa yang ingin Anda ketahui?';

        if (empty($this->messages)) {
            $this->messages[] = ['role' => 'system', 'text' => $this->getFaqContext()];
            $this->messages[] = ['role' => 'model', 'text' => $welcomeMessage];
        }
    }

    private function getFaqContext()
    {
        $faqText = $this->loadFaqData();
        $wifiText = $this->loadWifiData();
        $deptNames = collect($this->availableDepartments)->pluck('department_name')->implode(', ');

        return "ANDA ADALAH SISTEN TIKETING CERDAS.
    DAFTAR DEPARTEMEN: [ $deptNames ]
    
    PROSEDUR ANALISIS:
    1. KLASIFIKASI DEPARTEMEN:
       - Kata kunci (gaji, uang, reimburse, bonus, slip gaji, finance) -> WAJIB FINANCE.
       - Kata kunci (internet, komputer, aplikasi, login, password, error, printer) -> WAJIB IT.
       - Kata kunci (cuti, kontrak, asuransi, absensi) -> WAJIB HR.
    
    2. ANALISIS PRIORITAS:
       - HIGH: Masalah mendesak, sistem mati, atau kerugian finansial langsung.
       - MEDIUM: Kendala operasional harian (seperti gaji belum masuk).
       - LOW: Pertanyaan atau permintaan informasi.

    3. OUTPUT:
       Jika user ingin membuat tiket atau mengeluh tentang masalah, Anda WAJIB membalas dengan JSON ini:
       {
         \"action\": \"confirm_ticket\",
         \"summary\": \"Ringkasan masalah user\",
         \"department\": \"Nama departemen yang tepat dari daftar di atas\",
         \"priority\": \"low/medium/high\",
         \"reason\": \"Alasan pemilihan departemen & prioritas\"
       }

    ATURAN:
    - JANGAN tampilkan teks JSON ke user.
    - Gunakan bahasa Indonesia yang sopan.
    
    DATA:
    \n" . $wifiText . "\n" . $faqText;
    }

    private function loadFaqData()
    {
        $path = storage_path('app/' . self::FAQ_FILE_PATH);
        if (!File::exists($path)) return "FAQ tidak tersedia.";

        $faqData = json_decode(File::get($path), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($faqData)) return "FAQ tidak valid.";

        $faqText = "\n### Daftar FAQ:\n";
        foreach ($faqData as $item) {
            $faqText .= "Q: " . ($item['pertanyaan'] ?? '') . "\nA: " . ($item['jawaban'] ?? '') . "\n";
        }
        return $faqText;
    }

    private function loadWifiData()
    {
        $user = Auth::user();
        $wifiData = Wifi::query()
            ->when(optional($user)->company_id, fn($q) => $q->where('company_id', $user->company_id))
            ->where('is_active', true)
            ->get(['ssid', 'password', 'location'])
            ->toArray();

        $wifiText = "Daftar WiFi Aktif:\n";
        foreach ($wifiData as $item) {
            $wifiText .= "- SSID: {$item['ssid']}, Pass: {$item['password']}, Lokasi: {$item['location']}\n";
        }
        return $wifiText;
    }

    public function sendMessage()
    {
        $text = trim($this->currentMessage);
        if (empty($text)) return;

        $this->currentMessage = '';
        $this->messages[] = ['role' => 'user', 'text' => $text];
        $this->ticketQuestion = $text;
        $this->dispatch('chatUpdated');

        if ($this->needsConfirmation) {
            $this->messages[] = ['role' => 'model', 'text' => 'Mohon konfirmasi tiket atau batalkan terlebih dahulu.'];
            $this->dispatch('chatUpdated');
            return;
        }

        // ORIGINAL FEATURE: ticketPhase handling
        if ($this->ticketPhase === 'waiting_for_summary') {
            $this->ticketSummary = substr(trim($text), 0, 250);
            $this->needsConfirmation = true;
            $this->ticketPhase = 'idle';
            $this->messages[] = ['role' => 'model', 'text' => "Ringkasan tiket: {$this->ticketSummary}. Silakan klik Konfirmasi & Buat Tiket."];
            $this->dispatch('chatUpdated');
            return;
        }

        // ORIGINAL FEATURE: Keyword matching
        $lowerText = strtolower($text);
        if (preg_match('/(buat tiket|open ticket|bikin tiket)/', $lowerText)) {
            $this->ticketPhase = 'waiting_for_summary';
            $this->messages[] = ['role' => 'model', 'text' => 'Boleh, mohon berikan ringkasan masalah Anda.'];
            $this->dispatch('chatUpdated');
            return;
        }

        $aiResponse = $this->callOllama();

        if (!$aiResponse) {
            $aiResponse = "Maaf, asisten AI sedang tidak dapat dihubungi.";
        }

        $this->handleAiOutput($aiResponse);
    }

    private function callOllama()
    {
        try {
            $history = collect($this->messages)->map(function ($msg) {
                return [
                    'role' => ($msg['role'] === 'model') ? 'assistant' : $msg['role'],
                    'content' => $msg['text']
                ];
            })->toArray();

            $response = Http::timeout(45)->post(env('OLLAMA_API_URL', 'http://localhost:11434/api/chat'), [
                'model' => env('OLLAMA_MODEL', 'mistral'),
                'messages' => $history,
                'stream' => false,
                'options' => ['temperature' => 0.1]
            ]);

            return $response->successful() ? $response->json()['message']['content'] : null;
        } catch (Throwable $e) {
            Log::warning("Ollama connection failed: " . $e->getMessage());
        }
        return null;
    }

    private function handleAiOutput($responseText)
{
    $jsonPattern = '/\{.*\}/s';
    if (preg_match($jsonPattern, $responseText, $matches)) {
        $cleanResponse = $matches[0];
        $action = json_decode($cleanResponse, true);

        if (isset($action['action']) && $action['action'] === 'confirm_ticket') {
            
            if (strlen($this->ticketQuestion) < 10 && $this->ticketPhase === 'idle') {
                $this->messages[] = ['role' => 'model', 'text' => "Tentu, bisa tolong jelaskan detail kendalanya agar saya bisa menentukan departemen yang tepat?"];
                $this->dispatch('chatUpdated');
                return;
            }

            $this->ticketSummary = $action['summary'] ?? $this->ticketQuestion;
            $this->ticketPriority = strtolower($action['priority'] ?? 'medium');
            $this->aiReasoning = $action['reason'] ?? 'Analisis otomatis.';

            $aiDeptName = strtolower($action['department'] ?? '');
            foreach ($this->availableDepartments as $dept) {
                $currentDeptName = strtolower($dept['department_name']);
                // Cek apakah nama departemen dari AI ada di dalam daftar departemen sistem
                if (str_contains($aiDeptName, $currentDeptName) || str_contains($currentDeptName, $aiDeptName)) {
                    $this->selectedDepartmentId = $dept['department_id'];
                    break;
                }
            }

            $this->needsConfirmation = true;
            $this->dispatch('chatUpdated');
            return;
        }
    }

    $this->messages[] = ['role' => 'model', 'text' => $responseText];
    $this->dispatch('chatUpdated');
}
    public function confirmTicket()
    {
        if (!$this->selectedDepartmentId || !$this->ticketSummary) return;

        try {
            $ticket = Ticket::create([
                'company_id' => Auth::user()->company_id,
                'requestdept_id' => Auth::user()->department_id,
                'department_id' => $this->selectedDepartmentId,
                'user_id' => Auth::id(),
                'subject' => "Chatbot: " . $this->ticketSummary,
                'description' => "Original issue: " . $this->ticketQuestion,
                'priority' => strtoupper($this->ticketPriority),
                'status' => 'OPEN',
            ]);

            $this->messages[] = ['role' => 'model', 'text' => "✅ Tiket #{$ticket->ticket_id} berhasil dibuat."];
        } catch (Throwable $e) {
            Log::error('Ticket Create Error: ' . $e->getMessage());
            $this->messages[] = ['role' => 'model', 'text' => 'Gagal membuat tiket.'];
        }

        $this->resetConfirmationState();
        $this->dispatch('chatUpdated');
    }

    public function cancelTicket()
    {
        $this->messages[] = ['role' => 'model', 'text' => 'Dibatalkan. Ada lagi yang bisa saya bantu?'];
        $this->resetConfirmationState();
        $this->dispatch('chatUpdated');
    }

    private function resetConfirmationState()
    {
        $this->needsConfirmation = false;
        $this->ticketSummary = null;
        $this->aiReasoning = '';
        $this->ticketPhase = 'idle';
    }

    public function updatedIsOpen($value)
    {
        $this->dispatch('chat-modal-status', isOpen: $value);
    }

    #[On('openChatModal')]
    public function openModal()
    {
        $this->isOpen = true;
    }

    #[On('toggleChatModal')]
    public function toggleModal()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.components.ui.chat-modal');
    }
}
