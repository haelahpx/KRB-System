<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.superadmin')]
#[Title('Ticket Detail')]
class Ticketdetail extends Component
{
    public $ticketId;
    public ?Ticket $ticket = null;

    public $status;
    public $agent_id;
    public $newComment = '';

    public $agents = [];
    public $previewUrl = null;

    protected $rules = [
        'status' => 'required|string|in:OPEN,IN_PROGRESS,RESOLVED,CLOSED',
        'agent_id' => 'nullable|exists:users,user_id',
        'newComment' => 'required|string|max:1000',
    ];

    public function initials(?string $fullName): string {
        $fullName = trim($fullName ?? '');
        if ($fullName === '') return 'US';
        $parts = preg_split('/\s+/', $fullName);
        $first = strtoupper(mb_substr($parts[0] ?? 'U', 0, 1));
        $last = strtoupper(mb_substr($parts[count($parts)-1] ?? $parts[0] ?? 'S', 0, 1));
        return $first.$last;
    }

    public function mount($ticketId)
    {
        $this->ticketId = $ticketId;
        $companyId = Auth::user()->company_id;

        $this->loadTicket($companyId);
        
        $this->status = strtoupper($this->ticket->status ?? 'OPEN');
        $this->agent_id = $this->ticket->assignments->first()->user_id ?? null;
        
        $this->loadAgents($companyId, $this->ticket->department_id);
    }

    private function loadTicket(int $companyId): void
    {
        $this->ticket = Ticket::withTrashed()
            ->with([
                'user',
                'department',
                'attachments',
                'requesterDepartment',
                'comments.user',
                'assignments.user'
            ])
            ->where('company_id', $companyId)
            ->findOrFail($this->ticketId);
    }
    
    private function loadAgents(int $companyId, $departmentId): void
    {
        $this->agents = User::where('company_id', $companyId)
                            ->where('department_id', $departmentId) // Filter berdasarkan department_id tiket
                            // ->where('role', 'agent') // Hapus atau tambahkan filter role jika diperlukan
                            ->get(['user_id', 'full_name']);
    }

    public function save()
    {
        $this->validate([
            'status' => $this->rules['status'],
            'agent_id' => $this->rules['agent_id'],
        ]);

        $this->ticket->status = $this->status;
        $this->ticket->save();

        if ($this->agent_id) {
            $this->ticket->assignments()->delete(); 
            $this->ticket->assignments()->create([
                'user_id' => $this->agent_id,
                'assigned_by_user_id' => Auth::id(), 
            ]);
        } elseif (!$this->agent_id) {
            $this->ticket->assignments()->delete();
        }

        session()->flash('success', 'Ticket updated successfully.');
        $this->loadTicket(Auth::user()->company_id);
    }

    public function addComment()
    {
        $this->validate(['newComment' => $this->rules['newComment']]);

        $this->ticket->comments()->create([
            'user_id' => Auth::id(),
            'comment_text' => $this->newComment,
        ]);

        $this->reset('newComment');
        session()->flash('comment_success', 'Comment posted successfully.');
        $this->loadTicket(Auth::user()->company_id);
    }

    public function openPreview(string $url)
    {
        $this->previewUrl = $url;
    }

    public function closePreview()
    {
        $this->previewUrl = null;
    }

    public function render()
    {
        if (!$this->ticket) {
            return view('livewire.pages.superadmin.ticket-not-found');
        }

        return view('livewire.pages.superadmin.ticketdetail', [
            'ticket' => $this->ticket,
            'initials' => fn($name) => $this->initials($name),
            'statusColors' => [
                'OPEN' => 'bg-emerald-50 text-emerald-700 border-emerald-300',
                'IN_PROGRESS' => 'bg-yellow-50 text-yellow-700 border-yellow-300',
                'RESOLVED' => 'bg-blue-50 text-blue-700 border-blue-300',
                'CLOSED' => 'bg-gray-100 text-gray-600 border-gray-300',
            ],
        ]);
    }
}