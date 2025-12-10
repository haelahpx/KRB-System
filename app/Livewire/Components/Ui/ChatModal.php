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
use App\Models\Wifi; // IMPORT MODEL WIFI BARU
use Throwable;

class ChatModal extends Component
{
    public $isOpen = false;
    public $currentMessage = '';
    public $messages = [];

    private const GEMINI_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    private const FAQ_FILE_PATH = 'faq_data.json';

    // --- PROPERTI UNTUK KONFIRMASI TIKET ---
    public bool $needsConfirmation = false;
    public ?string $ticketSummary = null;
    public ?string $ticketQuestion = null;
    public ?int $selectedDepartmentId = null;
    public array $availableDepartments = [];

    // PROPERTI UNTUK STATE PERANTARA
    public string $ticketPhase = 'idle';

    public function mount()
    {
        $user = Auth::user();

        if ($user) {
            // Ambil semua departemen yang tersedia
            $this->availableDepartments = Department::query()
                ->when($user->company_id, fn($q) => $q->where('company_id', $user->company_id))
                ->orderBy('department_name', 'asc')
                ->get(['department_id', 'department_name'])
                ->toArray();

            // Set default department
            if ($user->department_id) {
                $this->selectedDepartmentId = $user->department_id;
            } else if (!empty($this->availableDepartments)) {
                $this->selectedDepartmentId = $this->availableDepartments[0]['department_id'];
            }
        }

        $userName = optional($user)->name ?? '';
        $welcomeMessage = 'Halo' . ($userName ? ', ' . $userName : '') . '! Saya adalah Asisten Chat. Apa yang ingin Anda ketahui?';

        // Hanya tambahkan instruksi Gemini jika belum ada riwayat obrolan
        if (empty($this->messages) || $this->messages[0]['role'] !== 'user') {
            $this->messages[] = ['role' => 'user', 'text' => $this->getFaqContext()];
            $this->messages[] = ['role' => 'model', 'text' => $welcomeMessage];
        }
    }

    private function getFaqContext()
    {
        $faqText = $this->loadFaqData(); // Memuat data FAQ dari JSON
        $wifiText = $this->loadWifiData(); // MEMUAT DATA DARI TABEL WIFI

        // --- INSTRUKSI UTAMA BARU ---
        $instruction = "ANDA ADALAH ASISTEN CHAT. Tugas Anda memiliki tiga prioritas:
        
        1. PRIORITAS TINGGI (KONTEKS): Selalu coba jawab pertanyaan pengguna berdasarkan 'Daftar FAQ' dan 'Daftar Jaringan WiFi' berikut. 
        
        2. PRIORITAS RENDAH (GENERAL KNOWLEDGE): Jika pertanyaan pengguna adalah sapaan sederhana atau perhitungan, Anda DIPERBOLEHKAN menjawabnya menggunakan pengetahuan umum Anda.
        
        3. PRIORITAS TINDAKAN (TICKET SUPPORT): Jika pertanyaan pengguna terkait bisnis ATAU topik lain yang TIDAK ada dalam KONTEKS DAN TIDAK ADA KATA KUNCI 'BUAT TIKET' DI DALAMNYA, Anda harus merespons HANYA dengan JSON:
        
        {\"action\": \"confirm_ticket\", \"summary\": \"[RINGKASAN PERTANYAAN PENGGUNA]\"}
        
        PENTING: Jika pengguna sudah menggunakan frasa seperti 'buat tiket', 'open ticket', atau 'bikin tiket', JANGAN berikan respons JSON. Biarkan logika aplikasi menangani alurnya.
        
        \n" . $wifiText . "\n" . $faqText; // Gabungkan Konteks WiFi dan FAQ

        return $instruction;
    }

    private function loadFaqData()
    {
        $path = storage_path('app/' . self::FAQ_FILE_PATH);
        $faqData = [];

        if (File::exists($path)) {
            $jsonContent = File::get($path);
            $faqData = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($faqData)) {
                Log::error('FAQ JSON PARSING FAILED: ' . json_last_error_msg());
                $faqData = [];
            }
        } else {
            Log::error('FAQ FILE NOT FOUND: Expected path: ' . $path);
        }

        $faqText = "\n### Daftar FAQ dan Konteks Terkait:\n";

        if (empty($faqData)) {
            $faqText .= "Tidak ada data FAQ tersedia.";
        } else {
            $faqItems = array_map(function ($item) {
                $q = $item['pertanyaan'] ?? 'Pertanyaan tidak valid';
                $a = $item['jawaban'] ?? 'Jawaban tidak tersedia';
                $c = $item['konteks'] ?? 'Tidak ada konteks spesifik.';
                return "--- Item FAQ ---\nQ: " . $q . "\nA: " . $a . "\nKONTEKS PENTING: " . $c . "\n";
            }, $faqData);
            $faqText .= implode("\n", $faqItems);
        }

        return $faqText;
    }

    /*
     * FUNGSI BARU: Mengambil data WiFi dari database dan memformatnya untuk Gemini.
     */
    private function loadWifiData()
    {
        $user = Auth::user();
        $companyId = optional($user)->company_id;

        $wifiData = Wifi::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->where('is_active', true) // Hanya tampilkan yang aktif
            ->get(['ssid', 'password', 'location'])
            ->toArray();

        $wifiText = "Daftar Jaringan WiFi yang Aktif:\n";

        if (empty($wifiData)) {
            $wifiText .= "Tidak ada data WiFi yang terdaftar untuk perusahaan ini.";
        } else {
            $wifiItems = array_map(function ($item) {
                $ssid = $item['ssid'] ?? 'N/A';
                $password = $item['password'] ?? 'N/A';
                $location = $item['location'] ?? 'Lokasi tidak spesifik';

                // Format data untuk Gemini
                return "- SSID: " . $ssid . ", Password: " . $password . ", Lokasi: " . $location;
            }, $wifiData);

            $wifiText .= implode("\n", $wifiItems);
            $wifiText .= "\n\nPastikan untuk selalu menjawab pertanyaan tentang koneksi WiFi, SSID, atau Password menggunakan data ini.";
        }

        return $wifiText;
    }

    // --- FUNGSI createSupportTicket, confirmTicket, cancelTicket, resetConfirmationState ---
    // (Tidak diubah secara signifikan dari perbaikan sebelumnya, tetapi disertakan untuk kelengkapan)

    public function createSupportTicket(string $summary, string $fullQuestion, int $departmentId): bool
    {
        if (!Auth::check()) {
            Log::warning('Ticket creation attempted by unauthenticated user.');
            return false;
        }

        $user = Auth::user();

        if (!Department::where('department_id', $departmentId)->exists()) {
            Log::warning('Ticket creation failed: Invalid department ID provided.', ['department_id' => $departmentId]);
            return false;
        }

        try {
            $ticket = Ticket::create([
                'company_id' => $user->company_id,
                'requestdept_id' => $user->department_id,
                'department_id' => $departmentId,
                'user_id' => $user->getKey(),
                'subject' => "Chatbot Ticket: " . substr(strip_tags($summary), 0, 250),
                'description' => "Pengajuan via Chatbot:\n\nRingkasan dari User: {$summary}\n\nPertanyaan Asli (pemicu tiket): {$fullQuestion}",
                'priority' => 'medium',
                'status' => 'OPEN',
            ]);

            $this->dispatch('ticketCreated', ticketId: $ticket->ticket_id);
            return true;
        } catch (Throwable $e) {
            Log::error('Chatbot failed to create Ticket', ['user_id' => $user->getKey(), 'error' => $e->getMessage()]);
            return false;
        }
    }

    public function confirmTicket()
    {
        if (empty($this->selectedDepartmentId) || empty($this->ticketSummary) || empty($this->ticketQuestion)) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Validasi gagal. Silakan ulangi proses tiket.']);
            $this->resetConfirmationState();
            return;
        }

        $ticketCreated = $this->createSupportTicket(
            $this->ticketSummary,
            $this->ticketQuestion,
            $this->selectedDepartmentId
        );

        $summary = $this->ticketSummary;
        $this->resetConfirmationState();

        if ($ticketCreated) {
            $this->messages[] = ['role' => 'model', 'text' => "Tiket support telah berhasil dibuat dengan ringkasan: '$summary'. Tim kami akan segera menghubungi Anda."];
        } else {
            $this->messages[] = ['role' => 'model', 'text' => 'Gagal membuat tiket support. Silakan coba lagi atau hubungi administrator.'];
        }

        $this->dispatch('chatUpdated');
    }

    public function cancelTicket()
    {
        $this->messages[] = ['role' => 'model', 'text' => 'Permintaan tiket dibatalkan. Ada lagi yang bisa saya bantu?'];
        $this->resetConfirmationState();
        $this->dispatch('chatUpdated');
    }

    private function resetConfirmationState()
    {
        $this->needsConfirmation = false;
        $this->ticketSummary = null;
        $this->ticketQuestion = null;
        $this->ticketPhase = 'idle';
    }


    public function sendMessage()
    {
        $text = trim($this->currentMessage);

        if (empty($text)) {
            return;
        }

        $this->currentMessage = '';
        $this->messages[] = ['role' => 'user', 'text' => $text];
        $this->ticketQuestion = $text;
        $this->dispatch('chatUpdated');

        if ($this->needsConfirmation) {
            $this->messages[] = ['role' => 'model', 'text' => 'Saat ini Anda sedang dalam proses konfirmasi tiket. Mohon klik tombol Konfirmasi & Buat Tiket atau Batalkan.'];
            $this->dispatch('chatUpdated');
            return;
        }

        // HANDLE: PHASE MENUNGGU RINGKASAN TIKET
        if ($this->ticketPhase === 'waiting_for_summary') {

            $this->ticketSummary = substr(trim($text), 0, 250);
            $this->needsConfirmation = true;
            $this->ticketPhase = 'idle';

            $this->messages[] = ['role' => 'model', 'text' => "Terima kasih, ringkasan tiket Anda adalah: {$this->ticketSummary}. Silakan periksa kembali detail di atas dan klik Konfirmasi & Buat Tiket."];
            $this->dispatch('chatUpdated');
            return;
        }

        // HANDLE: DETEKSI KATA KUNCI TIKET (Memulai Alur)
        $lowerText = strtolower($text);
        if (str_contains($lowerText, 'buatkan saya tiket') || str_contains($lowerText, 'buat tiket') || str_contains($lowerText, 'bikin tiket') || str_contains($lowerText, 'open ticket')) {

            if ($this->ticketPhase !== 'waiting_for_summary') {
                $this->ticketPhase = 'waiting_for_summary';

                $this->messages[] = ['role' => 'model', 'text' => 'Baik, saya akan bantu buatkan tiket support. Mohon berikan judul atau ringkasan singkat mengenai masalah Anda (Contoh: Printer di lantai 2 error, Gagal Login KRBS).'];
                $this->dispatch('chatUpdated');
            }
            return;
        }

        // PANGGILAN GEMINI
        $apiKey = trim(env('GEMINI_API_KEY'));
        if (empty($apiKey)) {
            $this->messages[] = ['role' => 'model', 'text' => 'ERROR: Kunci API Gemini tidak ditemukan. Harap atur variabel GEMINI_API_KEY.'];
            $this->dispatch('chatUpdated');
            return;
        }

        $contents = collect($this->messages)->map(function ($msg) {
            $role = ($msg['role'] === 'model') ? 'model' : 'user';
            return [
                'role' => $role,
                'parts' => [['text' => $msg['text']]]
            ];
        })->toArray();

        if (empty($contents)) {
            $this->messages[] = ['role' => 'model', 'text' => 'ERROR: Gagal membangun riwayat obrolan untuk API.'];
            $this->dispatch('chatUpdated');
            return;
        }

        $payload = ['contents' => $contents];
        $modelResponse = null;

        try {
            $response = Http::timeout(30)->post(self::GEMINI_API_URL . '?key=' . $apiKey, $payload);
            $data = $response->json();

            if ($response->successful()) {
                $rawResponseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $action = json_decode($rawResponseText, true);

                if (isset($action['action']) && $action['action'] === 'confirm_ticket') {

                    $this->ticketSummary = substr($action['summary'] ?? $this->ticketQuestion, 0, 250);
                    $this->needsConfirmation = true;
                    $this->ticketPhase = 'idle';

                    $this->messages[] = ['role' => 'model', 'text' => 'Pertanyaan Anda tidak ada dalam FAQ. Apakah Anda ingin mengajukan tiket support? Ringkasan masalah: ' . $this->ticketSummary . '. Silakan lanjutkan dengan mengklik Konfirmasi Tiket dan memilih departemen yang sesuai.'];
                    $this->dispatch('chatUpdated');
                    return;
                } else {
                    $modelResponse = $rawResponseText;
                }
            } else {
                $errorBody = $data;
                $errorMessage = $errorBody['error']['message'] ?? 'Kesalahan saat mengirim data.';
                Log::error("Gemini API Error (HTTP {$response->status()}): " . $errorMessage, ['response' => $errorBody]);
                $modelResponse = 'Kesalahan API: ' . $errorMessage . ' (HTTP ' . $response->status() . ').';
            }
        } catch (Throwable $e) {
            Log::error("Gemini Connection Error: " . $e->getMessage());
            $modelResponse = 'Terjadi kesalahan koneksi yang tidak terduga: ' . $e->getMessage();
        }

        $this->messages[] = ['role' => 'model', 'text' => $modelResponse];
        $this->dispatch('chatUpdated');
    }

    // --- LOGIKA MODAL (TIDAK BERUBAH) ---
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
