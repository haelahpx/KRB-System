<?php

namespace App\Livewire\Pages\User;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Vehicle;
use App\Models\Department;
use App\Models\VehicleBooking;
use App\Models\VehicleBookingPhoto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
#[Title('Book Vehicle')]
class Bookvehicle extends Component
{
    use WithFileUploads;

    // Form Properties
    public $name;
    public $department_id;
    public $start_time;
    public $end_time;
    public $date_from;
    public $date_to;
    public $purpose;
    public $destination;
    public $odd_even_area = 'tidak';
    public $purpose_type = 'dinas';
    public $vehicle_id = null;
    public $has_sim_a = false;
    public $agree_terms = false;

    // Booking Mode: 'perday', '24hours', 'custom'
    public $booking_mode = 'perday';

    // --- FILE UPLOAD QUEUE SYSTEM ---
    public $collected_photos = []; 
    public $temp_photos = [];      

    // UI Data
    public $departments;
    public $vehicles;
    public $hasVehicles = false;
    public $availability = [];
    public $unavailableVehicleIds = [];
    public $recentBookings = [];

    public $booking = null;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->full_name ?? $user->name ?? null;
        $this->department_id = $user->department_id ?? null;
        $this_company_id = $user->company_id ?? 1;

        $this->departments = Department::where('company_id', $this_company_id)->orderBy('department_name')->get();
        $this->vehicles = Vehicle::where('company_id', $this_company_id)->where('is_active', true)->get();
        $this->hasVehicles = $this->vehicles->count() > 0;

        // Default: Today
        $this->date_from = Carbon::today()->format('Y-m-d');

        // Calculate initial dates/times
        $this->calculateDates();

        $this->loadAvailability();
        $this->loadRecentBookings();

        $bookingId = request()->query('id');
        if ($bookingId) {
            $this->loadBooking($bookingId, $user);
        }
    }

    // --- LOGIC "ANTRIAN" FOTO ---

    /**
     * Dijalankan otomatis saat user memilih file di input
     */
    public function updatedTempPhotos()
    {
        $this->validate([
            'temp_photos.*' => 'image|max:20480', // Max 20MB
        ]);

        // Jika user upload banyak sekaligus atau satu per satu lewat kamera
        if (is_array($this->temp_photos)) {
            foreach ($this->temp_photos as $photo) {
                $this->collected_photos[] = $photo;
            }
        } else {
            // Untuk satu file (biasanya webcam atau single capture)
            $this->collected_photos[] = $this->temp_photos;
        }

        // Reset input agar bisa pilih file lagi
        $this->reset('temp_photos');
    }

    /**
     * Menghapus satu foto dari antrian
     */
    public function removePhoto($index)
    {
        if (isset($this->collected_photos[$index])) {
            array_splice($this->collected_photos, $index, 1);
        }
    }

    // ------------------------------

    public function setBookingMode($mode)
    {
        $this->booking_mode = $mode;
        $this->calculateDates();
        $this->loadAvailability();
    }

    public function updatedDateFrom()
    {
        $this->calculateDates();
        $this->loadAvailability();
    }

    private function calculateDates()
    {
        if (!$this->date_from)
            return;

        if ($this->booking_mode === 'perday') {
            $this->start_time = '08:00';
            $this->end_time = '17:00';
            $this->date_to = $this->date_from;
        } elseif ($this->booking_mode === '24hours') {
            $this->start_time = '08:00';
            $this->end_time = '08:00';
            try {
                $this->date_to = Carbon::parse($this->date_from)->addDay()->format('Y-m-d');
            } catch (\Exception $e) {
                $this->date_to = $this->date_from;
            }
        }
    }

    public function loadBooking($id, $user)
    {
        $booking = VehicleBooking::where('vehiclebooking_id', $id)
            ->where('company_id', $user->company_id ?? 1)
            ->first();

        if (!$booking) return;

        $this->booking = $booking;
    }

    public function submitBooking()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'start_time' => 'required',
            'end_time' => 'required',
            'purpose' => 'required|string|max:500',
            'has_sim_a' => 'accepted',
            'agree_terms' => 'accepted',
        ]);

        $user = Auth::user();
        $startAt = $this->combineDateTime($this->date_from, $this->start_time);
        $endAt = $this->combineDateTime($this->date_to, $this->end_time);

        VehicleBooking::create([
            'company_id' => $user->company_id ?? 1,
            'user_id' => $user->user_id ?? Auth::id(),
            'vehicle_id' => $this->vehicle_id,
            'borrower_name' => $this->name,
            'department_id' => $this->department_id,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'purpose' => $this->purpose,
            'destination' => $this->destination,
            'odd_even_area' => $this->odd_even_area,
            'purpose_type' => $this->purpose_type,
            'terms_agreed' => true,
            'has_sim_a' => true,
            'status' => 'pending',
        ]);

        session()->flash('success', 'Pemesanan berhasil dikirim.');
        return redirect()->route('vehiclestatus');
    }

    public function handlePhotoUpload()
    {
        if (!$this->booking || empty($this->collected_photos)) {
            $this->addError('collected_photos', 'Mohon upload minimal 1 foto.');
            return;
        }

        try {
            $type = ($this->booking->status === 'approved') ? 'before' : 'after';
            $userId = Auth::user()->user_id ?? Auth::id();

            foreach ($this->collected_photos as $photo) {
                $filename = "booking_{$this->booking->vehiclebooking_id}_{$type}_" . time() . '_' . Str::random(6) . '.' . $photo->extension();
                $photo->storeAs('vehicle_photos', $filename, 'public');

                VehicleBookingPhoto::create([
                    'vehiclebooking_id' => $this->booking->vehiclebooking_id,
                    'user_id' => $userId,
                    'photo_type' => $type,
                    'photo_path' => "vehicle_photos/$filename",
                ]);
            }

            if ($this->booking->status === 'approved') {
                $this->booking->update(['status' => 'on_progress']);
            }

            $this->reset(['collected_photos', 'temp_photos']);
            session()->flash('success', 'Foto berhasil diunggah.');
            return redirect()->route('vehiclestatus');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal: ' . $e->getMessage());
        }
    }

    private function combineDateTime($date, $time)
    {
        return Carbon::parse("{$date} {$time}")->toDateTimeString();
    }

    public function loadAvailability()
    {
        if (!$this->date_from || !$this->start_time || !$this->date_to || !$this->end_time) {
            $this->availability = [];
            return;
        }

        $start = $this->combineDateTime($this->date_from, $this->start_time);
        $end = $this->combineDateTime($this->date_to, $this->end_time);

        $this->availability = $this->vehicles->map(function ($v) use ($start, $end) {
            $isAvailable = !VehicleBooking::where('vehicle_id', $v->vehicle_id)
                ->whereIn('status', ['pending', 'approved', 'on_progress', 'returned'])
                ->where(function ($q) use ($start, $end) {
                    $q->where('start_at', '<', $end)->where('end_at', '>', $start);
                })->exists();

            return [
                'vehicle_id' => $v->vehicle_id,
                'label' => ($v->vehicle_name ?? $v->name) . ($v->plate_number ? " — " . $v->plate_number : ''),
                'status' => $isAvailable ? 'available' : 'unavailable',
            ];
        })->toArray();

        $this->unavailableVehicleIds = collect($this->availability)
            ->where('status', 'unavailable')
            ->pluck('vehicle_id')
            ->toArray();
    }

    public function loadRecentBookings()
    {
        $user = Auth::user();
        $this->recentBookings = VehicleBooking::with('vehicle')
            ->where('company_id', $user->company_id ?? 1)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.pages.user.bookvehicle');
    }
}