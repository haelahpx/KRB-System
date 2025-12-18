<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Component;
use App\Models\VehicleBooking;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.receptionist')]
#[Title('Detail Kendaraan & Pemesanan')]
class VehicleBookingDetail extends Component
{
    public $booking;

    public function mount($id)
    {
        $this->booking = VehicleBooking::with(['vehicle', 'photos.user'])
            ->when(request()->has('withTrashed'), fn($q) => $q->withTrashed())
            ->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.pages.receptionist.vehicle-booking-detail');
    }
}