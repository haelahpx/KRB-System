<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Guestbook as GuestbookModel;
use App\Models\Department; 
use App\Models\User;

#[Layout('layouts.receptionist')]
#[Title('GuestBook')]
class Guestbook extends Component
{
    // Form fields
    public $name;
    public $phone_number;
    public $instansi;
    public $keperluan;
    public $department_id;
    public $user_id;

    // Data Lists
    public $departments_list = [];
    public $users_list = [];

    // Internal fields
    public $date;
    public $jam_in;
    public $petugas_penjaga;

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');
        $compId = Auth::user()?->company_id;

        // Load departments
        $this->departments_list = Department::where('company_id', $compId)->get();
        
        if ($this->department_id) {
            $this->loadUsers();
        }
    }

    public function updatedDepartmentId($value)
    {
        $this->user_id = null; 
        $this->loadUsers($value);
    }
    
    private function loadUsers($deptId = null): void
    {
        $targetId = $deptId ?? $this->department_id;
        
        if ($targetId) {
            // FIX: Ensure we use the correct foreign key column
            $this->users_list = User::where('department_id', $targetId)->get();
        } else {
            $this->users_list = [];
        }
    }

    protected function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'phone_number'  => ['nullable', 'string', 'max:50'],
            'instansi'      => ['nullable', 'string', 'max:255'],
            'keperluan'     => ['nullable', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,department_id'], 
            'user_id'       => ['nullable', 'exists:users,user_id'],      
        ];
    }

    public function save(): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $user = Auth::user();

        // Convert empty strings from select to null
        $this->department_id = $this->department_id === '' ? null : $this->department_id;
        $this->user_id       = $this->user_id === '' ? null : $this->user_id;

        $validatedData = $this->validate();

        GuestbookModel::create(array_merge($validatedData, [
            'date'              => $now->toDateString(),
            'jam_in'            => $now->format('H:i'),
            'petugas_penjaga'   => $user?->full_name ?? $user?->name ?? 'Petugas Receptionist',
            'company_id'        => $user?->company_id,
            'jam_out'           => null,
        ]));

        $this->reset(['name', 'phone_number', 'instansi', 'keperluan', 'department_id', 'user_id']);
        $this->users_list = []; 

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Data tamu berhasil disimpan.');
        session()->flash('saved', true);
    }

    public function render()
    {
        return view('livewire.pages.receptionist.guestbook');
    }
}