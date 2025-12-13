<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Ticket;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

#[Layout('layouts.superadmin')]
#[Title('Ticket Support')]
class Ticketsupport extends Component
{
    use WithPagination;

    public $search = '';
    public $departmentFilter = '';
    public $priorityFilter = '';
    public $perPage = 20;
    public bool $showDeleted = false;
    public bool $showFilterModal = false;

    public $modal = false;
    public $editingTicketId = null;
    public $subject, $description, $priority, $department_id, $status;

    public $deptLookup = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'departmentFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
        'perPage' => ['except' => 20],
        'showDeleted' => ['except' => false],
    ];

    protected $rules = [
        'subject' => 'required|string|max:255',
        'description' => 'nullable|string',
        'priority' => 'required|in:low,medium,high,urgent',
        'department_id' => 'nullable|exists:departments,department_id',
        'status' => 'required|string|in:OPEN,IN_PROGRESS,RESOLVED,CLOSED',
    ];

    public function mount()
    {
        $companyId = Auth::user()->company_id;
        $deptNameCol = Schema::hasColumn('departments', 'name') ? 'name' : 'department_name';

        $this->deptLookup = Department::where('company_id', $companyId)
            ->pluck($deptNameCol, 'department_id')
            ->toArray();

        if (!$this->priority) {
            $this->priority = 'low';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingDepartmentFilter()
    {
        $this->resetPage();
        $this->closeFilterModal();
    }
    public function updatingPriorityFilter()
    {
        $this->resetPage();
        $this->closeFilterModal();
    }
    public function updatedShowDeleted()
    {
        $this->resetPage();
    }

    public function redirectToTicketDetails(string|int $id): \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
    {
        return redirect()->route('superadmin.ticketdetail', ['ticketId' => $id]);
    }

    public function openFilterModal(): void
    {
        $this->showFilterModal = true;
    }

    public function closeFilterModal(): void
    {
        $this->showFilterModal = false;
    }

    public function openEdit($id)
    {
        $t = Ticket::withTrashed()->findOrFail($id);

        if ($t->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $this->editingTicketId = $t->ticket_id;
        $this->subject = $t->subject;
        $this->description = $t->description;
        $this->priority = $t->priority ?? 'low';
        $this->department_id = $t->department_id;
        $this->status = $t->status ?? 'OPEN';
        $this->modal = true;

        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->modal = false;
        $this->reset(['editingTicketId', 'subject', 'description', 'priority', 'department_id', 'status']);
        $this->resetValidation();
    }

    public function update()
    {
        $this->validate();

        $t = Ticket::withTrashed()->findOrFail($this->editingTicketId);
        $t->update([
            'subject' => $this->subject,
            'description' => $this->description,
            'priority' => $this->priority,
            'department_id' => $this->department_id,
            'status' => $this->status,
        ]);

        session()->flash('success', 'Ticket updated successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        $t = Ticket::findOrFail($id);
        $t->delete();
        session()->flash('success', 'Ticket moved to trash (soft deleted).');
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->departmentFilter = '';
        $this->priorityFilter = '';
        $this->resetPage();
    }

    public function restore($id)
    {
        $t = Ticket::onlyTrashed()->findOrFail($id);
        $t->restore();
        session()->flash('success', 'Ticket restored successfully.');
        $this->resetPage();
    }

    public function render()
    {
        $companyId = Auth::user()->company_id;

        $query = Ticket::with(['user', 'attachments', 'department'])
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc');

        if ($this->showDeleted) {
            $query->onlyTrashed();
        }

        if ($this->search) {
            $s = '%' . $this->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('subject', 'like', $s)
                    ->orWhere('description', 'like', $s)
                    ->orWhereHas('user', function ($qu) use ($s) {
                        $qu->where('full_name', 'like', $s)
                            ->orWhere('email', 'like', $s);
                    });
            });
        }

        if ($this->departmentFilter) {
            $query->where('department_id', $this->departmentFilter);
        }

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        $tickets = $query->paginate($this->perPage);

        return view('livewire.pages.superadmin.ticketsupport', [
            'tickets' => $tickets,
            'deptLookup' => $this->deptLookup,
        ]);
    }
}
