<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket as TicketModel;
use App\Models\Department;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.admin')]
#[Title('Admin - Ticket')]
class Ticket extends Component
{
    use WithPagination;

    public ?string $search   = null;
    public ?string $priority = null;
    public ?string $status   = null;
    public ?string $assignment = null;

    protected string $paginationTheme = 'tailwind';

    private const ADMIN_ROLE_NAMES = ['Superadmin', 'Admin'];

    private const UI_TO_DB_STATUS_MAP = [
        'open'        => 'OPEN',
        'in_progress' => 'IN_PROGRESS',
        'resolved'    => 'RESOLVED',
        'closed'      => 'CLOSED',
    ];

    public string $company_name = '-';
    public string $department_name = '-';
    public array  $deptOptions = [];
    public ?int   $selected_department_id = null;
    public ?int   $primary_department_id  = null;
    public bool $showSwitcher = false;
    public bool $is_superadmin_user = false;

    public function mount(): void
    {
        $user = Auth::user()->loadMissing(['company', 'department', 'role']);
        
        $this->is_superadmin_user = $this->isSuperadmin();

        $this->company_name        = optional($user->company)->company_name ?? '-';
        $this->primary_department_id = $user->department_id ?: null;

        $this->loadUserDepartments();

        if ($this->is_superadmin_user) {
            $this->selected_department_id = null;
            $this->department_name = 'SEMUA DEPARTEMEN';
        } else {
            if (!$this->selected_department_id) {
                $this->selected_department_id = $this->primary_department_id
                    ?: ($this->deptOptions[0]['id'] ?? null);
            }
            $this->department_name = $this->resolveDeptName($this->selected_department_id);
        }
    }

    protected function loadUserDepartments(): void
    {
        $user = Auth::user();

        if ($this->is_superadmin_user) {
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

        $this->deptOptions = $rows
            ->map(fn($r) => ['id' => (int) $r->id, 'name' => (string) $r->name])
            ->values()
            ->all();

        $primaryId = $user->department_id;
        $isPrimaryInList = collect($this->deptOptions)->contains('id', $primaryId);

        if ($primaryId && !$isPrimaryInList) {
             $primaryName = Department::where('department_id', $primaryId)->value('department_name') ?? 'Unknown';
             array_unshift($this->deptOptions, ['id' => (int)$primaryId, 'name' => (string)$primaryName]);
        }
        
        if ($this->is_superadmin_user) {
            array_unshift($this->deptOptions, ['id' => null, 'name' => 'SEMUA DEPARTEMEN']);
        }

        $this->showSwitcher = count($this->deptOptions) > 1;

        if (!$this->is_superadmin_user && empty($this->deptOptions) && $this->primary_department_id) {
            $name = Department::where('department_id', $this->primary_department_id)->value('department_name') ?? 'Unknown';
            $this->deptOptions = [
                [
                    'id'   => (int) $this->primary_department_id,
                    'name' => (string) $name,
                ],
            ];
            $this->showSwitcher = false;
        }
    }

    protected function resolveDeptName(?int $deptId): string
    {
        if (!$deptId) {
            return 'SEMUA DEPARTEMEN';
        }

        foreach ($this->deptOptions as $opt) {
            if ($opt['id'] === (int) $deptId) {
                return $opt['name'];
            }
        }

        return Department::where('department_id', $deptId)->value('department_name') ?? '-';
    }

    public function resetToPrimaryDepartment(): void
    {
        if ($this->primary_department_id) {
            $this->selected_department_id = $this->primary_department_id;
            $this->department_name        = $this->resolveDeptName($this->selected_department_id);
            $this->resetPage();
        }
    }

    public function updatedSelectedDepartment_id(): void
    {
        $this->updatedSelectedDepartmentId();
    }

    public function updatedSelectedDepartmentId(): void
    {
        $id = $this->selected_department_id;

        if (!$this->is_superadmin_user) {
            $allowed = collect($this->deptOptions)->pluck('id')->all();
            $id = (int) $id;

            if (!in_array($id, $allowed, true)) {
                $this->selected_department_id = $this->primary_department_id
                    ?: ($this->deptOptions[0]['id'] ?? null);
                $id = $this->selected_department_id;
            }
        }
        
        $this->department_name = $this->resolveDeptName($id);
        $this->resetPage();
    }

    public function tick(): void
    {
        // used for wire:poll
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPriority(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingAssignment(): void
    {
        $this->resetPage();
    }

    protected function currentAdmin()
    {
        $user = Auth::user();
        if ($user && !$user->relationLoaded('role')) {
            $user->load('role');
        }

        return $user;
    }

    protected function ensureAdmin(): bool
    {
        $u = $this->currentAdmin();

        return $u && $u->role && \in_array($u->role->name, self::ADMIN_ROLE_NAMES, true);
    }

    protected function isSuperadmin(): bool
    {
        $u = $this->currentAdmin();

        return $u && $u->role && $u->role->name === 'Superadmin';
    }

    public function resetFilters(): void
    {
        $this->search = $this->priority = $this->status = $this->assignment = null;
        $this->resetPage();
    }

    public function render()
    {
        $query = TicketModel::query()
            ->with([
                'user:user_id,full_name,department_id',
                'department:department_id,department_name',
                'assignment.agent:user_id,full_name',
                'attachments:attachment_id,ticket_id,file_url,file_type,original_filename,bytes',
            ])
            ->withCount('attachments')
            ->orderByDesc('ticket_id');

        $user = auth()->user();

        if (Schema::hasColumn('tickets', 'company_id') && isset($user->company_id)) {
            $query->where('company_id', $user->company_id);
        }

        $deptId = $this->selected_department_id;

        if (Schema::hasColumn('tickets', 'department_id')) {
            if ($deptId !== null) {
                $query->where('department_id', $deptId);
            } elseif (!$this->is_superadmin_user) {
                $query->where('department_id', $user->department_id);
            }
        }

        if ($this->search) {
            $s = '%' . $this->search . '%';
            $query->where(fn($q) => $q
                ->where('subject', 'like', $s)
                ->orWhere('description', 'like', $s));
        }

        if ($this->priority) {
            $query->where('priority', $this->priority);
        }

        if ($this->status) {
            $dbStatus = self::UI_TO_DB_STATUS_MAP[$this->status] ?? null;
            if ($dbStatus) {
                $query->where('status', $dbStatus);
            }
        }

        if ($this->assignment) {
            if ($this->assignment === 'unassigned') {
                $query->whereDoesntHave('assignment');
            } elseif ($this->assignment === 'assigned') {
                $query->whereHas('assignment');
            }
        }

        $tickets = $query->paginate(12);

        return view('livewire.pages.admin.ticket', compact('tickets'));
    }
}