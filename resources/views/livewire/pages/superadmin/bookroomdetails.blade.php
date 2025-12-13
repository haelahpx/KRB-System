<div class="bg-gray-50 min-h-screen">
    @php
    use Carbon\Carbon;

    if (!function_exists('fmtDate')) {
        function fmtDate($v) {
            try { return $v ? Carbon::parse($v)->format('d M Y') : '—'; }
            catch (\Throwable) { return '—'; }
        }
    }
    if (!function_exists('fmtTime')) {
        function fmtTime($v) {
            try { return $v ? Carbon::parse($v)->format('H:i') : '—'; }
            catch (\Throwable) {
                if (is_string($v)) {
                    if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $v)) {
                        return Carbon::parse($v)->format('H:i');
                    }
                    if (preg_match('/^\d{2}:\d{2}/', $v)) return substr($v,0,5);
                }
                return '—';
            }
        }
    }
    if (!function_exists('isOnlineBooking')) {
        function isOnlineBooking($booking) {
            return in_array(strtolower($booking->booking_type ?? ''), ['online_meeting', 'onlinemeeting']);
        }
    }
    if (!function_exists('getInitials')) {
        function getInitials($name) {
            $words = explode(' ', trim($name));
            $initials = '';
            foreach ($words as $word) {
                if (!empty($word)) {
                    $initials .= strtoupper(substr($word, 0, 1));
                    if (strlen($initials) >= 2) break;
                }
            }
            return $initials ?: 'U';
        }
    }

    $card      = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label     = 'block text-sm font-medium text-gray-700 mb-2';
    $input     = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed';
    $btnBlk    = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    $btnRed    = 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition';
    $btnLt     = 'px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900/10 disabled:opacity-60 transition';
    $chip      = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
    $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';

    $bookingStatusColors = [
        'active' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'rejected' => 'bg-red-100 text-red-800 border-red-300',
        'completed' => 'bg-green-100 text-green-800 border-green-300',
    ];

    $b = $booking ?? null; 
    
    $isOnline = $b ? isOnlineBooking($b) : false;
    $status = $b ? strtolower($b->status ?? 'active') : 'active';
    $statusBadgeClass = $bookingStatusColors[$status] ?? 'bg-gray-100 text-gray-600 border-gray-300';
    $statusLabel = ucfirst(str_replace('_',' ', $status));

    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-8">

        @if(!$b)
            <div class="py-14 text-center text-gray-500 text-sm">
                <h3 class="text-lg font-medium text-gray-700">Booking Room Not Found</h3>
                <p class="mt-1">The requested booking detail could not be loaded.</p>
                <div class="mt-5">
                    <a href="{{ route('superadmin.bookingroom') }}" class="{{ $btnBlk }}">
                        <x-heroicon-o-arrow-left class="w-4 h-4 inline-block mr-1 -mt-0.5" />
                        Back to List
                    </a>
                </div>
            </div>
        @else

            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
                <div class="relative p-6 sm:p-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center border border-white/20">
                                <x-heroicon-o-calendar-days class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h2 class="text-lg sm:text-xl font-semibold">Booking Details #{{ $b->bookingroom_id }}</h2>
                                <p class="text-sm text-white/80">
                                    Title: <span class="font-semibold">{{ $b->meeting_title }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 ml-auto mt-4 md:mt-0">
                            <a href="{{ route('superadmin.bookingroom') }}" class="{{ $btnLt }} border-white/30 text-white/90 hover:bg-white/10">
                                <x-heroicon-o-arrow-left class="w-4 h-4 inline-block mr-1 -mt-0.5" />
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 lg:order-1 space-y-6">

                    <div class="{{ $card }}">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-gray-900">Booking Information</h2>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-[11px] px-2 py-0.5 rounded-md font-medium {{ $isOnline ? 'bg-emerald-500 text-white' : 'bg-blue-500 text-white' }}">
                                        {{ $isOnline ? 'Online' : 'Offline' }}
                                    </span>
                                    <span class="text-[11px] px-2 py-0.5 rounded-md font-medium border {{ $statusBadgeClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                    @if($b->deleted_at)
                                        <span class="text-[11px] px-2 py-0.5 rounded-md bg-rose-100 text-rose-800">Deleted</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="p-5 space-y-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Meeting Title</p>
                                    <p class="text-sm font-medium text-gray-800 mt-1">{{ $b->meeting_title }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Department</p>
                                    <p class="text-sm font-medium text-gray-800 mt-1">{{ $deptLookup[$b->department_id] ?? '—' }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-3 border-t border-gray-100">
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Date</p>
                                    <p class="text-sm font-medium text-gray-800 mt-1">{{ fmtDate($b->date) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Start Time</p>
                                    <p class="text-sm font-medium text-gray-800 mt-1">{{ fmtTime($b->start_time) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">End Time</p>
                                    <p class="text-sm font-medium text-gray-800 mt-1">{{ fmtTime($b->end_time) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Attendees</p>
                                    <p class="text-sm font-medium text-gray-800 mt-1">{{ $b->number_of_attendees }}</p>
                                </div>
                            </div>

                            @if(!$isOnline)
                            <div class="border-t border-gray-100 pt-5">
                                <p class="text-xs font-medium text-gray-500">Room</p>
                                <p class="text-sm font-medium text-gray-800 mt-1">{{ $roomLookup[$b->room_id] ?? '—' }}</p>
                            </div>
                            @endif

                            <div class="border-t border-gray-100 pt-5">
                                <p class="text-xs font-medium text-gray-500">Special Notes / Agenda</p>
                                <div class="bg-gray-50 rounded-lg p-3 mt-1 min-h-[60px] border border-gray-200">
                                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $b->special_notes ?: 'No special notes provided.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="{{ $card }}">
                        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">Requirements / Equipment</h2>
                            <p class="text-sm font-medium text-gray-600">{{ count($requirements) }} items</p>
                        </div>
                        <div class="p-5">
                            @if(count($requirements) > 0)
                                <ul class="space-y-3">
                                    @foreach ($requirements as $req)
                                        <li class="flex items-start gap-3 text-sm text-gray-700 p-3 rounded-lg border border-gray-200 bg-gray-50">
                                            <x-heroicon-s-check-circle class="w-5 h-5 text-gray-900 shrink-0 mt-0.5" />
                                            <span class="font-medium text-gray-800">{{ $req }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="rounded-lg border border-dashed border-gray-300 p-8 text-center text-gray-600">
                                    <x-heroicon-o-clipboard-document-check class="w-6 h-6 text-gray-400 mx-auto mb-2" />
                                    <p class="text-sm text-gray-500">No additional requirements were selected for this booking.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                <div class="lg:col-span-1 lg:order-2 space-y-6">

                    <div class="{{ $card }}">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Booked By</h2>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-3">
                                @php $userInitials = getInitials($b->user_full_name ?? 'U'); @endphp
                                <div class="w-12 h-12 bg-gray-900 rounded-full flex items-center justify-center border border-gray-900 text-lg font-medium text-white shrink-0">
                                    {{ $userInitials }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $b->user_full_name ?: 'Unknown User' }}</p>
                                    <p class="text-xs text-gray-500">Employee ID: {{ $b->user_id ?? '—' }}</p>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-100 space-y-3">
                                <div class="flex justify-between items-center">
                                    <p class="text-xs font-medium text-gray-500">Booking Created</p>
                                    <p class="text-sm font-medium text-gray-800">{{ Carbon::parse($b->created_at)->format('d M Y, H:i') }}</p>
                                </div>
                                <div class="flex justify-between items-center">
                                    <p class="text-xs font-medium text-gray-500">Last Updated</p>
                                    <p class="text-sm font-medium text-gray-800">{{ Carbon::parse($b->updated_at)->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="{{ $card }}">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Admin Actions</h2>
                        </div>
                        <div class="p-5 space-y-3">
                            <button class="{{ $btnBlk }} w-full !text-sm !py-2"
                                wire:click="openEdit({{ (int) $b->bookingroom_id }})"
                                wire:loading.attr="disabled"
                                wire:target="openEdit({{ (int) $b->bookingroom_id }})">
                                <x-heroicon-o-pencil-square class="w-4 h-4 inline-block mr-1 -mt-0.5" />
                                <span wire:loading.remove wire:target="openEdit({{ (int) $b->bookingroom_id }})">Edit Booking</span>
                                <span class="inline-flex items-center gap-2" wire:loading wire:target="openEdit({{ (int) $b->bookingroom_id }})">
                                    <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" /> Loading...
                                </span>
                            </button>

                            @if($b->deleted_at)
                                <button class="{{ $btnBlk }} !bg-emerald-600 hover:!bg-emerald-700 w-full !text-sm !py-2"
                                    wire:click="restore({{ (int) $b->bookingroom_id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="restore({{ (int) $b->bookingroom_id }})">
                                    <x-heroicon-o-arrow-path class="w-4 h-4 inline-block mr-1 -mt-0.5" />
                                    <span wire:loading.remove wire:target="restore({{ (int) $b->bookingroom_id }})">Restore Booking</span>
                                    <span class="inline-flex items-center gap-2" wire:loading wire:target="restore({{ (int) $b->bookingroom_id }})">
                                        <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" /> Processing...
                                    </span>
                                </button>
                            @else
                                <button class="{{ $btnRed }} w-full !text-sm !py-2"
                                    wire:click="delete({{ (int) $b->bookingroom_id }})"
                                    onclick="return confirm('Soft delete this booking (move to trash)?')"
                                    wire:loading.attr="disabled"
                                    wire:target="delete({{ (int) $b->bookingroom_id }})">
                                    <x-heroicon-o-trash class="w-4 h-4 inline-block mr-1 -mt-0.5" />
                                    <span wire:loading.remove wire:target="delete({{ (int) $b->bookingroom_id }})">Move to Trash</span>
                                    <span class="inline-flex items-center gap-2" wire:loading wire:target="delete({{ (int) $b->bookingroom_id }})">
                                        <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" /> Processing...
                                    </span>
                                </button>

                                @if(strtolower($b->status) !== 'completed')
                                <button class="{{ $btnLt }} w-full !border-green-300 !text-green-700 hover:!bg-green-50 !text-sm !py-2"
                                    wire:click="completeBooking({{ (int) $b->bookingroom_id }})"
                                    onclick="return confirm('Are you sure you want to mark this booking as COMPLETED?')"
                                    wire:loading.attr="disabled"
                                    wire:target="completeBooking({{ (int) $b->bookingroom_id }})">
                                    <x-heroicon-o-check-badge class="w-4 h-4 inline-block mr-1 -mt-0.5" />
                                    <span wire:loading.remove wire:target="completeBooking({{ (int) $b->bookingroom_id }})">Mark as Completed</span>
                                    <span class="inline-flex items-center gap-2" wire:loading wire:target="completeBooking({{ (int) $b->bookingroom_id }})">
                                        <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" /> Processing...
                                    </span>
                                </button>
                                @endif
                            @endif
                        </div>
                    </div>

                </div>

            </div>

        @endif

        @if($modal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true" wire:key="modal-br" wire:keydown.escape.window="closeModal">
            <button type="button" class="absolute inset-0 bg-black/50" aria-label="Close overlay" wire:click="closeModal"></button>

            <div class="relative w-full max-w-3xl mx-4 {{ $card }} focus:outline-none" tabindex="-1">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Booking #{{ $editingId }}</h3>
                    <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeModal" aria-label="Close">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>

                <form class="p-5" wire:submit.prevent="update">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="md:col-span-1">
                            <label class="{{ $label }}">Room</label>
                            <select class="{{ $input }}" wire:model.defer="room_id" @if(strtolower($booking_type) === 'online_meeting') disabled @endif>
                                <option value="">Choose room</option>
                                @foreach ($roomLookup as $rid => $rname)
                                <option value="{{ $rid }}">{{ $rname }}</option>
                                @endforeach
                            </select>
                            @error('room_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-1">
                            <label class="{{ $label }}">Department</label>
                            <select class="{{ $input }}" wire:model.defer="department_id">
                                <option value="">Choose department</option>
                                @foreach ($deptLookup as $did => $dname)
                                <option value="{{ $did }}">{{ $dname }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-1">
                            <label class="{{ $label }}">Booking Type</label>
                            <select class="{{ $input }}" wire:model.live="booking_type">
                                <option value="meeting">Booking Room (Offline)</option>
                                <option value="online_meeting">Online Meeting</option>
                            </select>
                            @error('booking_type') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-3">
                            <label class="{{ $label }}">Meeting Title</label>
                            <input type="text" class="{{ $input }}" wire:model.defer="meeting_title" placeholder="Weekly Sync / Project Kickoff">
                            @error('meeting_title') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-3 grid grid-cols-2 sm:grid-cols-4 gap-4 border-t border-gray-100 pt-5">
                            <div>
                                <label class="{{ $label }}">Date</label>
                                <input type="date" class="{{ $input }}" wire:model.defer="date">
                                @error('date') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $label }}">Start Time</label>
                                <input type="datetime-local" class="{{ $input }}" wire:model.defer="start_time">
                                @error('start_time') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $label }}">End Time</label>
                                <input type="datetime-local" class="{{ $input }}" wire:model.defer="end_time">
                                @error('end_time') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $label }}">Attendees</label>
                                <input type="number" min="1" class="{{ $input }}" wire:model.defer="number_of_attendees" placeholder="e.g. 10">
                                @error('number_of_attendees') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                         <div class="md:col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-4 border-t border-gray-100 pt-5">
                            <div class="sm:col-span-1">
                                <label class="{{ $label }}">Status</label>
                                <select class="{{ $input }}" wire:model.defer="status">
                                    <option value="active">Active</option>
                                    <option value="completed">Completed</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                                @error('status') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>


                        <div class="md:col-span-3">
                            <label class="{{ $label }}">Special Notes (optional)</label>
                            <textarea class="{{ $input }} h-24" wire:model.defer="special_notes" placeholder="Agenda, equipment needs, etc."></textarea>
                            @error('special_notes') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-3 border-t border-gray-100 pt-5">
                            <label class="{{ $label }}">Requirements</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mt-1 p-3 rounded-lg border border-gray-200 bg-gray-50">
                                @foreach ($allRequirements as $req)
                                <label class="flex items-center space-x-2 text-sm text-gray-700 hover:text-gray-900 cursor-pointer">
                                    <input type="checkbox"
                                        wire:model.defer="selectedRequirements"
                                        value="{{ $req->requirement_id }}"
                                        class="rounded border-gray-300 text-gray-900 focus:ring-gray-900/30">
                                    <span class="font-medium">{{ $req->name }}</span>
                                </label>
                                @endforeach
                            </div>
                            @error('selectedRequirements') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                        <button type="button" class="{{ $btnLt }}" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="{{ $btnBlk }} !text-sm !py-2" wire:loading.attr="disabled" wire:target="update">
                            <span wire:loading.remove wire:target="update">Update Booking</span>
                            <span class="inline-flex items-center gap-2" wire:loading wire:target="update">
                                <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" />
                                Processing…
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </main>
</div>