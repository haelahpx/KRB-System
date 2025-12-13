<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\VehicleBooking;
use App\Models\Vehicle;
use App\Models\VehicleBookingPhoto;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

#[Layout('layouts.superadmin')]
#[Title('Vehicle History')]
class Bookingvehicle extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';
    protected string $tz = 'Asia/Jakarta';

    public string $q = '';
    public ?int $vehicleFilter = null;
    public string $statusTab = 'done';
    public bool $includeDeleted = false;
    public ?string $selectedDate = null;
    public string $sortFilter = 'recent';
    public int $perPage = 5;

    public bool $showFilterModal = false;
    public ?string $previewUrl = null;
    public bool $showPreviewModal = false;

    public bool $showEditModal = false;
    public ?int $editingBookingId = null;
    public ?int $editVehicleId = null;
    public ?string $editBorrowerName = '';
    public ?string $editPurpose = '';
    public ?string $editDestination = '';
    public ?string $editStartAt = null;
    public ?string $editEndAt = null;
    public ?string $editNotes = '';

    public array $photosByBooking = [];

    protected $queryString = [
        'q'              => ['except' => ''],
        'vehicleFilter'  => ['except' => null],
        'statusTab'      => ['except' => 'done'],
        'includeDeleted' => ['except' => false],
        'selectedDate'   => ['except' => null],
        'sortFilter'     => ['except' => 'recent'],
        'page'           => ['except' => 1],
    ];

    protected function rules(): array
    {
        return [
            'editVehicleId'      => ['required', 'integer', Rule::exists('vehicles', 'vehicle_id')->where('company_id', Auth::user()->company_id)],
            'editBorrowerName'   => 'required|string|max:255',
            'editPurpose'        => 'required|string|max:255',
            'editDestination'    => 'nullable|string|max:255',
            'editStartAt'        => 'required|date|before:editEndAt',
            'editEndAt'          => 'required|date|after:editStartAt',
            'editNotes'          => 'nullable|string|max:1000',
        ];
    }

    public function updatingQ()
    {
        $this->resetPage();
    }
    public function updatingVehicleFilter()
    {
        $this->resetPage();
    }
    public function updatingStatusTab()
    {
        $this->resetPage();
    }
    public function updatingIncludeDeleted()
    {
        $this->resetPage();
    }
    public function updatingSelectedDate()
    {
        $this->resetPage();
    }
    public function updatingSortFilter()
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        if (!in_array($this->statusTab, ['done', 'rejected'], true))  $this->statusTab = 'done';
        if (!in_array($this->sortFilter, ['recent', 'oldest', 'nearest'], true)) $this->sortFilter = 'recent';
    }

    public function clearFilters(): void
    {
        $this->reset(['q', 'selectedDate', 'vehicleFilter']);
        $this->resetPage();
        $this->dispatch('toast', type: 'info', title: 'Filters Cleared', message: 'Search and specific filters have been reset.', duration: 2500);
    }

    public function clearAllFilters(): void
    {
        $this->reset([
            'q',
            'selectedDate',
            'vehicleFilter',
            'sortFilter',
        ]);
        $this->sortFilter = 'recent';
        $this->statusTab = 'done';
        $this->resetPage();
        $this->dispatch('toast', type: 'info', title: 'All Filters Cleared', message: 'All filters have been reset.', duration: 2500);
    }

    public function openFilterModal(): void
    {
        $this->showFilterModal = true;
    }
    public function closeFilterModal(): void
    {
        $this->showFilterModal = false;
        $this->resetPage();
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

    public function deletePhoto(int $photoId): void
    {
        VehicleBookingPhoto::findOrFail($photoId)->delete();
        $this->dispatch('toast', type: 'success', title: 'Deleted', message: 'Photo soft-deleted.', duration: 2500);
        $this->refreshPhotosForCurrentPage();
    }
    public function restorePhoto(int $photoId): void
    {
        VehicleBookingPhoto::withTrashed()->findOrFail($photoId)->restore();
        $this->dispatch('toast', type: 'success', title: 'Restored', message: 'Photo restored.', duration: 2500);
        $this->refreshPhotosForCurrentPage();
    }

    public function forceDeletePhoto(int $photoId): void
    {
        $row = VehicleBookingPhoto::withTrashed()->findOrFail($photoId);
        if ($row->photo_path && !preg_match('#^https?://#', $row->photo_path)) {
            Storage::disk('public')->delete($row->photo_path);
        }
        $row->forceDelete();
        $this->dispatch('toast', type: 'success', title: 'Removed', message: 'Photo permanently deleted.', duration: 2500);
        $this->refreshPhotosForCurrentPage();
    }
    private function refreshPhotosForCurrentPage(): void
    {
        $this->q = $this->q;
    }

    public function editBooking(int $bookingId): void
    {
        $booking = VehicleBooking::withTrashed()->findOrFail($bookingId);

        $this->editingBookingId = $booking->vehiclebooking_id;
        $this->editVehicleId = $booking->vehicle_id;
        $this->editBorrowerName = $booking->borrower_name;
        $this->editPurpose = $booking->purpose;
        $this->editDestination = $booking->destination;

        $this->editStartAt = Carbon::parse($booking->start_at)->format('Y-m-d\TH:i');
        $this->editEndAt = Carbon::parse($booking->end_at)->format('Y-m-d\TH:i');

        $this->editNotes = $booking->notes;
        $this->showEditModal = true;

        $this->dispatch('toast', type: 'info', title: 'Edit Action', message: "Loading data for Booking #{$bookingId}.", duration: 3000);
    }

    public function closeEditModal(): void
    {
        $this->reset(['showEditModal', 'editingBookingId', 'editVehicleId', 'editBorrowerName', 'editPurpose', 'editDestination', 'editStartAt', 'editEndAt', 'editNotes']);
        $this->resetValidation();
    }

    public function saveBooking(): void
    {
        $this->validate();

        $booking = VehicleBooking::withTrashed()->findOrFail($this->editingBookingId);

        $booking->vehicle_id    = $this->editVehicleId;
        $booking->borrower_name = $this->editBorrowerName;
        $booking->purpose       = $this->editPurpose;
        $booking->destination   = $this->editDestination;
        $booking->start_at      = Carbon::parse($this->editStartAt);
        $booking->end_at        = Carbon::parse($this->editEndAt);
        $booking->notes         = $this->editNotes;

        $booking->save();

        $this->closeEditModal();
        $this->dispatch('toast', type: 'success', title: 'Updated', message: "Booking #{$this->editingBookingId} has been updated.", duration: 3000);
        $this->resetPage();
    }

    public function openDetails(int $bookingId): void
    {
        // PERUBAHAN DI SINI:
        // Tambahkan 'navigate: false' untuk memaksa Livewire melakukan
        // redirect HTTP penuh (standard) daripada menggunakan Alpine.js navigation.
        $this->redirect(route('superadmin.bookingdetails', ['bookingId' => $bookingId]), navigate: false);
    }

    public function deleteBooking(int $bookingId): void
    {
        VehicleBooking::findOrFail($bookingId)->delete();
        $this->dispatch('toast', type: 'success', title: 'Deleted', message: "Booking #{$bookingId} soft-deleted.", duration: 2500);
        $this->resetPage();
    }

    public function restoreBooking(int $bookingId): void
    {
        VehicleBooking::withTrashed()->findOrFail($bookingId)->restore();
        $this->dispatch('toast', type: 'success', title: 'Restored', message: "Booking #{$bookingId} restored.", duration: 2500);
        $this->resetPage();
    }

    public function forceDeleteBooking(int $bookingId): void
    {
        $booking = VehicleBooking::withTrashed()->findOrFail($bookingId);

        $photos = VehicleBookingPhoto::withTrashed()->where('vehiclebooking_id', $bookingId)->get();
        foreach ($photos as $photo) {
            if ($photo->photo_path && !preg_match('#^https?://#', $photo->photo_path)) {
                Storage::disk('public')->delete($photo->photo_path);
            }
            $photo->forceDelete();
        }

        $booking->forceDelete();

        $this->dispatch('toast', type: 'success', title: 'Removed', message: "Booking #{$bookingId} permanently deleted.", duration: 2500);
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $companyId = (int) ($user?->company_id ?? 0);

        $query = VehicleBooking::where('company_id', $companyId);

        if ($this->includeDeleted) $query->withTrashed();

        $query->where('status', $this->statusTab === 'rejected' ? 'rejected' : 'completed');

        if (trim($this->q) !== '') {
            $q = trim($this->q);
            $query->where(function ($qq) use ($q) {
                $qq->where('purpose', 'like', "%{$q}%")
                    ->orWhere('destination', 'like', "%{$q}%")
                    ->orWhere('borrower_name', 'like', "%{$q}%");
            });
        }

        if ($this->vehicleFilter) $query->where('vehicle_id', $this->vehicleFilter);
        if (!empty($this->selectedDate)) $query->whereDate('start_at', $this->selectedDate);

        $now = Carbon::now($this->tz);
        match ($this->sortFilter) {
            'oldest'  => $query->orderBy('start_at', 'asc'),
            'nearest' => $query->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, start_at, ?))', [$now]),
            default   => $query->orderBy('start_at', 'desc'),
        };

        $bookings = $query->paginate($this->perPage);

        $vehicles = Vehicle::where('company_id', $companyId)->get(['vehicle_id', 'name', 'plate_number']);
        $vehicleMap = $vehicles->mapWithKeys(fn($v) => [
            $v->vehicle_id => ($v->name ?? $v->plate_number ?? ('#' . $v->vehicle_id))
        ])->toArray();

        $ids = method_exists($bookings, 'pluck')
            ? $bookings->pluck('vehiclebooking_id')->filter()->values()->all()
            : [];

        return view('livewire.pages.superadmin.bookingvehicle', [
            'bookings'       => $bookings,
            'vehicleMap'     => $vehicleMap,
            'vehicles'       => $vehicles,
            'statusTab'      => $this->statusTab,
            'includeDeleted' => $this->includeDeleted,
            'selectedDate'   => $this->selectedDate,
            'sortFilter'     => $this->sortFilter,
            'vehicleFilter'  => $this->vehicleFilter,
        ]);
    }
}
