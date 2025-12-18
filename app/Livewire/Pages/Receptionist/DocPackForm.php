<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Department;
use App\Models\Storage;
use App\Models\Delivery;
use Carbon\Carbon;

#[Layout('layouts.receptionist')]
#[Title('Doc/Pack Form')]
class DocPackForm extends Component
{
    use WithFileUploads;

    public string $direction = 'taken';
    public string $itemType  = 'package';

    public $departmentId = null;
    public $userId       = null;
    public $storageId    = null;

    public string $senderText   = '';
    public string $receiverText = '';
    public string $itemName     = '';

    public $photo = null;

    public array $departments = [];
    public array $users = [];
    public array $storages = [];

    protected function rules(): array
    {
        $base = [
            'direction'    => ['required', 'in:taken,deliver'],
            'itemType'     => ['required', 'in:package,document'],
            'storageId'    => ['required', 'integer', 'exists:storages,storage_id'],
            'itemName'     => ['required', 'string', 'max:255'],
            'departmentId' => ['required', 'integer', 'exists:departments,department_id'],
            'userId'       => ['required', 'integer', 'exists:users,user_id'],
            'photo'        => ['nullable', 'image', 'max:2048'],
        ];

        if ($this->direction === 'taken') {
            $base['senderText'] = ['required', 'string', 'max:255'];
        } else {
            $base['receiverText'] = ['required', 'string', 'max:255'];
        }

        return $base;
    }

    public function mount(): void
    {
        $companyId = Auth::user()->company_id;

        $this->departments = Department::where('company_id', $companyId)
            ->orderBy('department_name')
            ->get(['department_id as id', 'department_name as name'])
            ->toArray();

        $this->storages = Storage::where('company_id', $companyId)
            ->orderBy('name')
            ->get(['storage_id as id', 'name'])
            ->toArray();

        if ($this->departmentId) {
            $this->loadUsers();
        }
    }

    public function updatedDepartmentId(): void
    {
        $this->userId = null;
        $this->loadUsers();
    }

    private function loadUsers(): void
    {
        $companyId = Auth::user()->company_id;

        if (!$this->departmentId) {
            $this->users = [];
            return;
        }

        $this->users = User::where('company_id', $companyId)
            ->where('department_id', (int) $this->departmentId)
            ->orderBy('full_name')
            ->pluck('full_name', 'user_id')
            ->toArray();
    }

    public function updatedDirection(): void
    {
        $this->departmentId = null;
        $this->userId       = null;
        $this->senderText   = '';
        $this->receiverText = '';
        $this->users        = [];
    }

    public function save(): void
    {
        $this->validate();

        $now = Carbon::now('Asia/Jakarta');

        $selectedUserName = User::where('user_id', (int) $this->userId)->value('full_name') ?? '—';

        if ($this->direction === 'taken') {
            $receiver = $selectedUserName;
            $sender   = $this->senderText;
        } else {
            $sender   = $selectedUserName;
            $receiver = $this->receiverText;
        }

        $imagePath = null;

        if ($this->photo) {
            $filename = 'delivery_' . $now->format('Ymd_His') . '_' . uniqid() . '.' . $this->photo->getClientOriginalExtension();
            
            $path = $this->photo->storeAs('deliveries', $filename, 'public');
            
            $imagePath = 'storage/' . $path;
        }

        Delivery::create([
            'company_id'      => Auth::user()->company_id,
            'department_id'   => (int) $this->departmentId,
            'receptionist_id' => Auth::id(),
            'item_name'       => $this->itemName,
            'type'            => $this->itemType,
            'nama_pengirim'   => $sender,
            'nama_penerima'   => $receiver,
            'storage_id'      => (int) $this->storageId,
            'pengambilan'     => null,
            'pengiriman'      => null,
            'status'          => 'pending',
            'direction'       => $this->direction,
            'image'           => $imagePath,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        $this->reset([
            'departmentId',
            'userId',
            'senderText',
            'receiverText',
            'storageId',
            'itemName',
            'photo',
        ]);

        $this->direction = 'taken';
        $this->itemType  = 'package';
        $this->users     = [];

        $this->dispatch(
            'toast',
            type: 'success',
            title: 'Tersimpan',
            message: 'Data berhasil disimpan.',
            duration: 3000
        );
    }

    public function render()
    {
        return view('livewire.pages.receptionist.docpackform');
    }
}