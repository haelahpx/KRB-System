<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BookingRoom;
use App\Models\Department;
use App\Models\User;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('History Room Booking')]
class RoomMonitoring extends Component
{
    use WithPagination;

    // List limits
    public int $perPage = 15;
    public ?string $search = null;
    public ?string $statusFilter = '';
    public ?string $bookingTypeFilter = ''; // NEW: Filter for booking type

    public string $sortDirection = 'desc';

    // Header + switcher
    public string $company_name = '-';
    public string $department_name = '-';
    public array  $deptOptions = [];
    public ?int   $selected_department_id = null;
    public ?int   $primary_department_id  = null;
    public bool $showSwitcher = false;

    public bool $is_superadmin = false;

    // --- MODAL PROPERTIES ---
    public bool $showDetailModal = false;
    public ?int $selectedBookingId = null;
    public ?BookingRoom $selectedBookingDetail = null;

    /**
     * Retrieves the selected BookingRoom model instance with necessary relationships.
     */
    public function getBookingDetailProperty(): ?BookingRoom
    {
        if ($this->selectedBookingId) {
            return BookingRoom::withTrashed()
                ->with(['room', 'requirements'])
                ->find($this->selectedBookingId);
        }
        return null;
    }

    /**
     * Open the detail modal and fetch the selected booking detail.
     */
    public function openDetailModal(string $bookingId): void
    {
        $id = (int) $bookingId;
        Log::info('Attempting to open detail modal for ID: ' . $id);

        $this->selectedBookingId = $id;
        $this->selectedBookingDetail = $this->getBookingDetailProperty();

        if ($this->selectedBookingDetail) {
            $this->showDetailModal = true;
            Log::info('Detail modal opened successfully for ID: ' . $id);
        } else {
            Log::error('Failed to find booking detail for ID: ' . $id);
            $this->dispatch(
                'toast',
                type: 'error',
                title: 'Error',
                message: 'Gagal memuat detail booking (ID: ' . $id . ' tidak ditemukan).',
                duration: 5000
            );
            $this->showDetailModal = false;
        }
    }

    /**
     * Close the detail modal.
     */
    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedBookingId = null;
        $this->selectedBookingDetail = null;
    }

    // --- SORTING METHOD ---
    public function toggleSortDirection(): void
    {
        $this->sortDirection = $this->sortDirection === 'desc' ? 'asc' : 'desc';
        $this->resetPage();
    }

    /**
     * Checks if the user has the 'Superadmin' role.
     */
    private function isSuperAdmin(User $user): bool
    {
        return optional($user->role)->name === 'Superadmin';
    }

    public function mount(): void
    {
        $user = Auth::user()->loadMissing(['company', 'department', 'role']);
        $this->is_superadmin = $this->isSuperAdmin($user);

        $this->company_name = optional($user->company)->company_name ?? '-';
        $this->primary_department_id = $user->department_id ?: null;

        $this->loadUserDepartments();

        if (!$this->selected_department_id) {
            $this->selected_department_id = $this->primary_department_id
                ?: ($this->deptOptions[0]['id'] ?? null);
        }

        if ($this->is_superadmin) {
            $this->department_name = 'SEMUA DEPARTEMEN';
            $this->showSwitcher = true;
            if (!in_array(null, array_column($this->deptOptions, 'id'))) {
                array_unshift($this->deptOptions, ['id' => null, 'name' => 'SEMUA DEPARTEMEN']);
            }
            $this->selected_department_id = null;
        } else {
            $this->department_name = $this->resolveDeptName($this->selected_department_id);
        }
    }

    protected function loadUserDepartments(): void
    {
        $user = Auth::user();

        if ($this->is_superadmin) {
            $rows = Department::where('company_id', $user->company_id)
                ->orderBy('department_name')
                ->get(['department_id as id', 'department_name as name']);
        } else {
            $rows = DB::table('user_departments as ud')
                ->join('departments as d', 'd.department_id', '=', 'ud.department_id')
                ->where('ud.user_id', $user->user_id)
                ->orderBy('d.department_name')
                ->get(['d.department_id as id', 'd.department_name as name']);
        }

        $this->deptOptions = $rows->map(fn($r) => ['id' => (int)$r->id, 'name' => (string)$r->name])->values()->all();

        $primaryId = $user->department_id;
        $primaryName = $this->resolveDeptName($primaryId);
        $isPrimaryInList = collect($this->deptOptions)->contains('id', $primaryId);

        if ($primaryId && !$isPrimaryInList) {
             array_unshift($this->deptOptions, ['id' => (int)$primaryId, 'name' => (string)$primaryName]);
        }

        $this->showSwitcher = count($this->deptOptions) > 1 || $this->is_superadmin;

        if (!$this->is_superadmin) {
            if (empty($this->deptOptions) && $this->primary_department_id) {
                $name = Department::where('department_id', $this->primary_department_id)->value('department_name') ?? 'Unknown';
                $this->deptOptions = [['id' => (int)$this->primary_department_id, 'name' => (string)$name]];
                $this->showSwitcher = false;
            }
        }
    }

    protected function resolveDeptName(?int $deptId): string
    {
        if (!$deptId) return 'SEMUA DEPARTEMEN';
        foreach ($this->deptOptions as $opt) {
            if ($opt['id'] === (int)$deptId) return $opt['name'];
        }
        return Department::where('department_id', $deptId)->value('department_name') ?? '-';
    }

    public function updatedSelectedDepartment_id(): void
    {
        $this->department_name = $this->resolveDeptName($this->selected_department_id);
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedBookingTypeFilter(): void
    {
        $this->resetPage();
    }

    protected function baseHistoryQuery()
    {
        $companyId = Auth::user()?->company_id;
        $deptId    = $this->selected_department_id; 
        
        $query = BookingRoom::query()
            ->with(['room'])
            ->where('company_id', $companyId)
            ->when($deptId, fn($q) => $q->where('department_id', $deptId));

        // Handle Status Filter
        if ($this->statusFilter === 'DELETED') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();

            if ($this->statusFilter) {
                $query->where('status', $this->statusFilter);
            }
        }

        // Handle Booking Type Filter
        if ($this->bookingTypeFilter) {
            $query->where('booking_type', $this->bookingTypeFilter);
        }

        // Handle Search Filter
        $query->when($this->search, function ($q, $s) {
            $q->where(function ($qq) use ($s) {
                $qq->where('meeting_title', 'like', "%{$s}%")
                    ->orWhere('special_notes', 'like', "%{$s}%");
            });
        });

        $query->orderBy('end_time', $this->sortDirection);

        return $query;
    }

    public function render()
    {
        $bookings = $this->baseHistoryQuery()->paginate($this->perPage);

        return view('livewire.pages.admin.roommonitoring', [
            'bookings' => $bookings,
        ]);
    }
}