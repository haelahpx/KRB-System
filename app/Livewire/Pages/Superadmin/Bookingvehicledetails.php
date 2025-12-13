<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\VehicleBooking;
use App\Models\VehicleBookingPhoto;
use Carbon\Carbon;

#[Layout('layouts.superadmin')]
#[Title('Booking Details')]
class Bookingvehicledetails extends Component
{
    public $bookingId;
    public $booking;
    public $beforePhotos;
    public $afterPhotos;
    public $vehicleMap = [];

    public bool $showPreviewModal = false;
    public ?string $previewUrl = null;

    public function mount($bookingId)
    {
        $this->bookingId = $bookingId;
        $this->loadBookingDetails();
    }

    private function loadBookingDetails()
    {
        $companyId = (int) (Auth::user()?->company_id ?? 0);
        
        $this->booking = VehicleBooking::withTrashed()
            ->with('vehicle:vehicle_id,name,plate_number')
            ->where('company_id', $companyId)
            ->findOrFail($this->bookingId);

        $this->beforePhotos = VehicleBookingPhoto::withTrashed()
            ->where('vehiclebooking_id', $this->bookingId)
            ->where('photo_type', 'before')
            ->orderBy('deleted_at')
            ->latest('id')
            ->get();
            
        $this->afterPhotos = VehicleBookingPhoto::withTrashed()
            ->where('vehiclebooking_id', $this->bookingId)
            ->where('photo_type', 'after')
            ->orderBy('deleted_at')
            ->latest('id')
            ->get();

        $vehicle = $this->booking->vehicle;
        if ($vehicle) {
            $this->vehicleMap[$vehicle->vehicle_id] = $vehicle->name ?? $vehicle->plate_number ?? ('#'.$vehicle->vehicle_id);
        }
    }

    public function openPhotoPreview(string $url): void
    {
        $this->previewUrl = $url;
        $this->showPreviewModal = true;
    }

    public function closePhotoPreview(): void
    {
        $this->previewUrl = null;
        $this->showPreviewModal = false;
    }

    public function render()
    {
        $booking = $this->booking;
        $vehicleMap = $this->vehicleMap;

        // Custom function to format date/time and photo URL (as per your existing logic)
        $fmtDate = function ($v) {
            try { return $v ? Carbon::parse($v)->format('d M Y') : '—'; }
            catch (\Throwable) { return '—'; }
        };
        $fmtTime = function ($v) {
            try { return $v ? Carbon::parse($v)->format('H.i') : '—'; }
            catch (\Throwable $e) { 
                if (is_string($v) && preg_match('/^\d{2}:\d{2}/', $v)) return str_replace(':','.', substr($v,0,5));
                return '—'; 
            }
        };
        $photoUrl = function ($path) {
            if (!$path) return null;
            if (preg_match('#^https?://#', $path)) return $path;
            return Storage::url($path);
        };

        $statusMap = [
            'rejected' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-800', 'label' => 'Rejected'],
            'completed' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'label' => 'Completed'],
            'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'label' => 'Pending'],
            'approved' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Approved'],
        ];

        return view('livewire.pages.superadmin.bookingvehicledetails', [
            'booking' => $booking,
            'beforePhotos' => $this->beforePhotos,
            'afterPhotos' => $this->afterPhotos,
            'vehicleMap' => $vehicleMap,
            'fmtDate' => $fmtDate,
            'fmtTime' => $fmtTime,
            'photoUrl' => $photoUrl,
            'statusMap' => $statusMap,
        ]);
    }
}