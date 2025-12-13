<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\BookingRoom as BR;
use App\Models\Room as RM;
use App\Models\Department as DP;
use App\Models\Requirement;

#[Layout('layouts.superadmin')]
#[Title('Booking Room Details')] 
class Bookroomdetails extends Component
{
    public int $bookingId;

    public ?BR $booking = null;
    public array $roomLookup = [];
    public array $deptLookup = [];
    public array $requirements = [];
    
    public bool $modal = false;
    public ?int $editingId = null;

    public $room_id;
    public $department_id;
    public $meeting_title;
    public $date;
    public $number_of_attendees;
    public $start_time;
    public $end_time;
    public $special_notes;
    public $status;
    public $booking_type;

    public array $selectedRequirements = [];
    public $allRequirements = [];

    public function mount(int $id): void
    {
        $this->bookingId = $id;
        $companyId = Auth::user()->company_id;

        $this->loadLookups($companyId);
        $this->loadBookingData($companyId);
        $this->loadRequirements($this->bookingId);

        $this->allRequirements = Requirement::where('company_id', $companyId)
            ->orderBy('name')
            ->get(['requirement_id', 'name']);
    }

    private function loadLookups(int $companyId): void
    {
        $roomNameCol = Schema::hasColumn('rooms', 'name') ? 'name'
            : (Schema::hasColumn('rooms', 'room_name') ? 'room_name'
                : (Schema::hasColumn('rooms', 'room_number') ? 'room_number' : null));
        if (!$roomNameCol) {
             $this->roomLookup = [];
        } else {
            $rooms = RM::where('company_id', $companyId)->get(['room_id', "$roomNameCol as name"]);
            $this->roomLookup = $rooms->pluck('name', 'room_id')->toArray();
        }

        $deptNameCol = Schema::hasColumn('departments', 'name') ? 'name'
            : (Schema::hasColumn('departments', 'department_name') ? 'department_name' : null);
        if (!$deptNameCol) {
            $this->deptLookup = [];
        } else {
            $departments = DP::where('company_id', $companyId)->get(['department_id', "$deptNameCol as name"]);
            $this->deptLookup = $departments->pluck('name', 'department_id')->toArray();
        }
    }

    private function loadBookingData(int $companyId): void
    {
        $this->booking = BR::query()
            ->select([
                'booking_rooms.*',
                DB::raw("COALESCE(u.full_name, '') as user_full_name"),
            ])
            ->leftJoin('users as u', 'u.user_id', '=', 'booking_rooms.user_id')
            ->where('booking_rooms.company_id', $companyId)
            ->where('booking_rooms.bookingroom_id', $this->bookingId)
            ->withTrashed()
            ->first();

        if (!$this->booking) {
            $this->dispatch('toast', type: 'error', title: 'Kesalahan', message: 'Booking Room ID ' . $this->bookingId . ' tidak ditemukan.', duration: 5000);
            return;
        }

        $this->editingId = $this->booking->bookingroom_id;
        $this->room_id = $this->booking->room_id;
        $this->department_id = $this->booking->department_id;
        $this->meeting_title = $this->booking->meeting_title;
        $this->date = $this->booking->date;
        $this->number_of_attendees = $this->booking->number_of_attendees;
        $this->start_time = Carbon::parse($this->booking->start_time)->format('Y-m-d\TH:i'); 
        $this->end_time = Carbon::parse($this->booking->end_time)->format('Y-m-d\TH:i');
        $this->special_notes = $this->booking->special_notes;
        $this->status = $this->booking->status;
        $this->booking_type = $this->booking->booking_type;

        $this->selectedRequirements = DB::table('booking_requirements')
            ->where('bookingroom_id', $this->bookingId)
            ->pluck('requirement_id')
            ->toArray();
    }

    private function loadRequirements(int $id): void
    {
        $rows = DB::table('booking_requirements as br')
            ->join('requirements as r', 'r.requirement_id', '=', 'br.requirement_id')
            ->where('br.bookingroom_id', $id)
            ->orderBy('r.name')
            ->get(['r.name']);

        $this->requirements = $rows->pluck('name')->toArray();
    }

    protected function rules(): array
    {
        return [
            'room_id' => [
                // KOREKSI: Room required jika booking_type adalah 'meeting' (Booking Room Offline)
                Rule::requiredIf(fn() => strtolower($this->booking_type) === 'meeting'), 
                'nullable', 
                'integer', 
                Rule::exists('rooms', 'room_id')
            ],
            'department_id' => ['required', 'integer', Rule::exists('departments', 'department_id')],
            'meeting_title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'number_of_attendees' => ['required', 'integer', 'min:1'],
            'start_time' => ['required', 'date'], 
            'end_time' => ['required', 'date', 'after:start_time'],
            'special_notes' => ['nullable', 'string'],
            'selectedRequirements' => ['array'],
            'status' => ['required', 'string', Rule::in(['active', 'completed', 'rejected'])],
            'booking_type' => ['required', 'string', Rule::in(['meeting', 'online_meeting'])],
        ];
    }
    
    public function redirectToBookingDetails(int $id): \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
    {
        return redirect()->route('superadmin.bookroomdetails', ['id' => $id]);
    }

    public function openEdit(int $id): void
    {
        if ($this->booking && $this->booking->bookingroom_id === $id) {
            $this->loadBookingData(Auth::user()->company_id);
            $this->modal = true;
            $this->resetErrorBag();
        } else {
             $this->dispatch('toast', type: 'error', title: 'Kesalahan', message: 'Tidak dapat membuka form edit. Data booking hilang.', duration: 5000);
        }
    }

    public function closeModal(): void
    {
        $this->modal = false;
        $this->resetErrorBag();
    }

    public function update(): void
    {
        $this->validate();

        $companyId = Auth::user()->company_id;
        $row = BR::where('company_id', $companyId)->withTrashed()->findOrFail($this->editingId); 

        // Tentukan room_id yang akan disimpan: null jika online_meeting
        $roomIdToSave = strtolower($this->booking_type) === 'online_meeting' ? null : $this->room_id;

        $row->update([
            'room_id' => $roomIdToSave, 
            'department_id' => $this->department_id,
            'meeting_title' => $this->meeting_title,
            'date' => $this->date,
            'number_of_attendees' => $this->number_of_attendees,
            'start_time' => Carbon::parse($this->start_time)->toDateTimeString(), 
            'end_time' => Carbon::parse($this->end_time)->toDateTimeString(),
            'special_notes' => $this->special_notes,
            'status' => $this->status,
            'booking_type' => strval(strtolower($this->booking_type)), 
        ]);

        DB::table('booking_requirements')
            ->where('bookingroom_id', $row->bookingroom_id)
            ->delete();

        $now = now();
        if(!empty($this->selectedRequirements)){
            $inserts = [];
            foreach ($this->selectedRequirements as $reqId) {
                $inserts[] = [
                    'bookingroom_id' => $row->bookingroom_id,
                    'requirement_id' => $reqId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('booking_requirements')->insert($inserts);
        }

        $this->closeModal();
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Booking berhasil diperbarui.', duration: 3000);
        $this->loadBookingData($companyId);
        $this->loadRequirements($row->bookingroom_id);
    }

    public function delete(int $id): void
    {
        $companyId = Auth::user()->company_id;
        $row = BR::where('company_id', $companyId)->findOrFail($id);
        $row->delete(); 
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Booking dipindahkan ke sampah (soft deleted).', duration: 3000);
        $this->redirect(route('superadmin.bookingroom'), navigate: true); 
    }

    public function restore(int $id): void
    {
        $companyId = Auth::user()->company_id;
        $row = BR::where('company_id', $companyId)->onlyTrashed()->findOrFail($id);
        $row->restore(); 
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Booking dipulihkan dari sampah.', duration: 3000);
        $this->loadBookingData($companyId);
        $this->loadRequirements($id);
    }

    public function completeBooking(int $id): void
    {
        $companyId = Auth::user()->company_id;
        $row = BR::where('company_id', $companyId)->findOrFail($id);
        $row->update(['status' => 'completed']);
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Booking ditandai sebagai Selesai.', duration: 3000);
        $this->loadBookingData($companyId);
    }

    public function render()
    {
        return view('livewire.pages.superadmin.bookroomdetails');
    }
}