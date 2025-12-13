<div class="bg-gray-50 min-h-screen">
    @php
    // Variabel dan Helpers dari konteks Booking Room (Dipertahankan)
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

    // Variabel Styling (Diambil dari desain Ticket Support untuk konsistensi di halaman)
    $card      = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label     = 'block text-sm font-medium text-gray-700 mb-2';
    $input     = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk    = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    $btnRed    = 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition';
    $btnLt     = 'px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900/10 disabled:opacity-60 transition';
    $chip      = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-[11px]';
    $ico       = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
    $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';

    // Status mapping untuk Booking Room
    $bookingStatusColors = [
        'active' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'rejected' => 'bg-red-100 text-red-800 border-red-300',
        'completed' => 'bg-green-100 text-green-800 border-green-300',
    ];

    // Dummy data for filter rendering (assuming they are passed from Livewire component)
    $deptOptionsFormatted = collect($deptLookup ?? [])->map(fn($name, $id) => ['id' => $id, 'label' => $name])->values()->all();
    $roomOptionsFormatted = collect($roomLookup ?? [])->map(fn($name, $id) => ['id' => $id, 'label' => $name])->values()->all();
    $bookingTypes = ['all' => 'All Types', 'offline' => 'Offline', 'online' => 'Online'];

    // Filter variables (using dateFilter/roomFilter/deptFilter as placeholders for the filter state)
    $selectedDate = $selectedDate ?? '';
    $roomFilterId = $roomFilterId ?? null;
    $departmentFilterId = $departmentFilterId ?? null;
    $typeScope = $typeScope ?? 'all';
    $search = $search ?? '';
    $dateMode = $dateMode ?? 'terbaru';
    $perPage = $perPage ?? 10;

    // View variables
    $bookings = $bookings ?? collect();
    $requirementsMap = $requirementsMap ?? [];
    $showFilterModal = $showFilterModal ?? false;
    $modal = $modal ?? false;
    $editingId = $editingId ?? null;
    $withTrashed = $withTrashed ?? false;
    $activeTab = $activeTab ?? 'all';
    $roomLookup = $roomLookup ?? [];
    $deptLookup = $deptLookup ?? [];
    $allRequirements = $allRequirements ?? collect();
    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-8">

        {{-- HERO --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center border border-white/20">
                            <x-heroicon-o-calendar-days class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Booking Room Management</h2>
                            <p class="text-sm text-white/80">
                                Cabang: <span class="font-semibold">{{ optional(Auth::user()->company)->company_name ?? '-' }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 ml-auto">
                        <label class="inline-flex items-center gap-2 text-sm text-white/90">
                            <input type="checkbox"
                                wire:model.live="withTrashed"
                                class="rounded border-white/30 bg-white/10 focus:ring-white/40">
                            <span>Show Deleted</span>
                        </label>
                        <a href="{{ route('superadmin.manageroom') }}" class="{{ $btnLt }} border-white/30 text-white/90 hover:bg-white/10">Go to Rooms</a>
                    </div>
                </div>
            </div>
        </div>

        @if (session()->has('success'))
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl px-4 py-3 text-sm text-gray-800">
            {{ session('success') }}
        </div>
        @endif

        {{-- MOBILE FILTER SUMMARY & BUTTON --}}
        <div class="lg:hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="flex-1">
                    <p class="text-xs text-gray-600 font-medium">
                        @if(!is_null($roomFilterId))
                        Room: {{ $roomLookup[$roomFilterId] ?? '—' }}
                        @elseif(!is_null($departmentFilterId))
                        Dept: {{ $deptLookup[$departmentFilterId] ?? '—' }}
                        @else
                        Type: {{ $bookingTypes[$typeScope] ?? 'All Types' }}
                        @endif
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $search ? 'Searching for: ' . $search : 'Tap filter to adjust settings.' }}
                    </p>
                </div>
                <button type="button" class="{{ $btnBlk }} !h-8 !px-4 !py-0 shrink-0" wire:click="openFilterModal">
                    Filter
                </button>
            </div>
        </div>

        {{-- MAIN LAYOUT (Full Width) --}}
        <div class="grid grid-cols-1 gap-6">

            <section class="space-y-4">
                <div class="{{ $card }}">
                    {{-- DESKTOP FILTERS (4 Columns with Select Options) --}}
                    <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200 hidden lg:block">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                            {{-- Search --}}
                            <div>
                                <label class="{{ $label }}">Search Title/User</label>
                                <div class="relative">
                                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search title, notes or user..."
                                        class="{{ $input }} pl-10 w-full placeholder:text-gray-400">
                                    <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                </div>
                            </div>

                            {{-- Filter by Room --}}
                            <div>
                                <label class="{{ $label }}">Filter by Room</label>
                                <select wire:model.live="roomFilterId" class="{{ $input }}">
                                    <option value="">— All Rooms —</option>
                                    @foreach ($roomLookup as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter by Department --}}
                            <div>
                                <label class="{{ $label }}">Filter by Department</label>
                                <select wire:model.live="departmentFilterId" class="{{ $input }}">
                                    <option value="">— All Departments —</option>
                                    @foreach ($deptLookup as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Date Filter & Clear --}}
                            <div>
                                <label class="{{ $label }}">Date Filter & Clear</label>
                                <div class="flex items-center gap-2 h-10">
                                    <div class="relative flex-1">
                                        <input type="date" class="{{ $input }} !mb-0" wire:model.live="selectedDate">
                                    </div>
                                    @if(!is_null($departmentFilterId) || !is_null($roomFilterId) || $typeScope !== 'all' || $selectedDate || $search)
                                    <button type="button"
                                        title="Clear All Filters"
                                        wire:click="clearAllFilters"
                                        class="w-10 h-10 flex items-center justify-center rounded-lg border border-rose-300 text-rose-600 hover:bg-rose-50 transition shrink-0">
                                        <x-heroicon-o-x-mark class="w-5 h-5" />
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- DESKTOP TABS (Status & Type Scope) --}}
                        <div class="flex items-center justify-between pt-4 mt-3 border-t border-gray-100">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">All Bookings</h3>
                                <p class="text-xs text-gray-500">
                                    Manage active and completed bookings.
                                </p>
                            </div>
                            <div class="flex items-center gap-4">
                                {{-- Type Scope --}}
                                <div class="inline-flex items-center bg-gray-100 rounded-full p-1 text-xs font-medium">
                                    @foreach($bookingTypes as $key => $label)
                                    <button type="button"
                                        wire:click="setTypeScope('{{ $key }}')"
                                        class="px-3 py-1 rounded-full transition {{ $typeScope === $key ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-200' }}">
                                        {{ $label }}
                                    </button>
                                    @endforeach
                                </div>
                                {{-- Status Tabs --}}
                                <div class="inline-flex items-center bg-gray-100 rounded-full p-1 text-xs font-medium">
                                    <button type="button"
                                            wire:click="setTab('all')"
                                            class="px-3 py-1 rounded-full transition {{ $activeTab === 'all' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-200' }}">
                                        All
                                    </button>
                                    <button type="button" wire:click="setTab('done')" class="px-3 py-1 rounded-full transition {{ $activeTab === 'done' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-200' }}">Done</button>
                                    <button type="button" wire:click="setTab('rejected')" class="px-3 py-1 rounded-full transition {{ $activeTab === 'rejected' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-200' }}">Rejected</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- MOBILE TABS (Status Filters) --}}
                    <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200 lg:hidden">
                         <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">All Bookings</h3>
                                <p class="text-xs text-gray-500">
                                    Manage active and completed bookings.
                                </p>
                            </div>
                            <div class="inline-flex items-center bg-gray-100 rounded-full p-1 text-xs font-medium">
                                <button type="button"
                                        wire:click="setTab('all')"
                                        class="px-3 py-1 rounded-full transition {{ $activeTab === 'all' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-200' }}">
                                    All
                                </button>
                                <button type="button" wire:click="setTab('done')" class="px-3 py-1 rounded-full transition {{ $activeTab === 'done' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-200' }}">Done</button>
                                <button type="button" wire:click="setTab('rejected')" class="px-3 py-1 rounded-full transition {{ $activeTab === 'rejected' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-200' }}">Rejected</button>
                            </div>
                        </div>
                    </div>

                    {{-- LIST AREA (2 cards per row) --}}
                    <div class="px-4 sm:px-6 py-5">
                        @if($bookings->isEmpty())
                        <div class="py-14 text-center text-gray-500 text-sm">No bookings found matching your criteria.</div>
                        @else
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            @foreach ($bookings as $b)
                            @php
                            $rowNo = (($bookings->firstItem() ?? 1) + $loop->index);
                            $avatarChar = strtoupper(substr($b->meeting_title ?? '—', 0, 1));
                            $reqs = $requirementsMap[$b->bookingroom_id] ?? [];
                            $isOnline = isOnlineBooking($b);
                            $status = strtolower($b->status ?? 'active');

                            $statusBadgeClass = $bookingStatusColors[$status] ?? 'bg-gray-100 text-gray-600 border-gray-300';
                            $statusLabel = ucfirst(str_replace('_',' ', $status));

                            $metaChips = [];
                            if (!$isOnline) {
                                $metaChips[] = '<span class="'.$chip.'"><span class="text-gray-500">Room:</span><span class="font-medium text-gray-700">'. ($roomLookup[$b->room_id] ?? '—') .'</span></span>';
                            }
                            $metaChips[] = '<span class="'.$chip.'"><span class="text-gray-500">Dept:</span><span class="font-medium text-gray-700">'. ($deptLookup[$b->department_id] ?? '—') .'</span></span>';
                            $metaChips[] = '<span class="'.$chip.'"><span class="text-gray-500">Att:</span><span class="font-medium text-gray-700">'. $b->number_of_attendees .'</span></span>';

                            @endphp
                            <div class="bg-white border border-gray-200 rounded-xl p-4 sm:px-5 sm:py-4 hover:shadow-sm hover:border-gray-300 transition {{ $b->deleted_at ? 'opacity-50' : '' }}" wire:key="br-{{ $b->bookingroom_id }}">
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <div class="flex items-start gap-3 flex-1 min-w-0">
                                        <div class="{{ $ico }} hidden sm:flex">
                                            {{ $avatarChar }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-2 mb-1">
                                                <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate flex-1">
                                                    {{ $b->meeting_title }}
                                                </h4>
                                                <div class="{{ $mono }} sm:hidden shrink-0">No. {{ $rowNo }}</div>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                {{-- Type Badge (Matching Ticket Priority style) --}}
                                                <span class="text-[11px] px-2 py-0.5 rounded-md font-medium {{ $isOnline ? 'bg-emerald-500 text-white' : 'bg-blue-500 text-white' }}">
                                                    {{ $isOnline ? 'Online' : 'Offline' }}
                                                </span>

                                                {{-- Status Badge --}}
                                                <span class="text-[11px] px-2 py-0.5 rounded-md font-medium border {{ $statusBadgeClass }}">
                                                    {{ $statusLabel }}
                                                </span>

                                                @if($b->deleted_at)
                                                <span class="text-[11px] px-2 py-0.5 rounded-md bg-rose-100 text-rose-800">Deleted</span>
                                                @endif
                                            </div>

                                            <p class="text-[12px] text-gray-500">
                                                By: <span class="font-medium text-gray-700 truncate">{{ $b->user_full_name ?: '—' }}</span>
                                            </p>

                                            <p class="text-sm text-gray-600 mt-2 line-clamp-2">
                                                <span class="font-medium">Notes:</span> {{ \Illuminate\Support\Str::limit($b->special_notes, 100) ?: 'No special notes.' }}
                                            </p>

                                            <div class="flex flex-wrap items-center gap-2 mt-3 text-xs">
                                                {{-- Date & Time Chip --}}
                                                <span class="{{ $chip }}"><span class="text-gray-500">Date:</span><span class="font-medium text-gray-700">{{ fmtDate($b->date) }}</span></span>
                                                <span class="{{ $chip }}"><span class="text-gray-500">Time:</span><span class="font-medium text-gray-700">{{ fmtTime($b->start_time) }}–{{ fmtTime($b->end_time) }}</span></span>

                                                {{-- Room, Dept, Att Chips --}}
                                                {!! implode('', $metaChips) !!}

                                                {{-- Requirements chips --}}
                                                @if(count($reqs))
                                                    <span class="{{ $chip }} bg-gray-200 text-gray-600">
                                                        <x-heroicon-o-wrench-screwdriver class="w-3 h-3" />
                                                        {{ count($reqs) }} Req
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col justify-end items-end shrink-0 pt-1">

                                        <div class="hidden sm:flex justify-end w-full mb-2">
                                            <div class="h-8 {{ $mono }} flex items-center justify-center !py-0 !px-2.5 w-full">No. {{ $rowNo }}</div>
                                        </div>

                                        <div class="flex flex-col gap-2 pt-1 w-full">

                                            <button class="{{ $btnLt }} w-full"
                                                wire:click="redirectToBookingDetails({{ (int) $b->bookingroom_id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="redirectToBookingDetails({{ (int) $b->bookingroom_id }})">
                                                <span wire:loading.remove wire:target="redirectToBookingDetails({{ (int) $b->bookingroom_id }})">Open</span>
                                                <span wire:loading wire:target="redirectToBookingDetails({{ (int) $b->bookingroom_id }})">...</span>
                                            </button>

                                            <button class="{{ $btnBlk }} w-full"
                                                wire:click="openEdit({{ (int) $b->bookingroom_id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="openEdit({{ (int) $b->bookingroom_id }})">
                                                <span wire:loading.remove wire:target="openEdit({{ (int) $b->bookingroom_id }})">Edit</span>
                                                <span wire:loading wire:target="openEdit({{ (int) $b->bookingroom_id }})">...</span>
                                            </button>

                                            @if(!$b->deleted_at)
                                            <button class="{{ $btnRed }} w-full"
                                                wire:click="delete({{ (int) $b->bookingroom_id }})"
                                                onclick="return confirm('Soft delete this booking (move to trash)?')"
                                                wire:loading.attr="disabled"
                                                wire:target="delete({{ (int) $b->bookingroom_id }})">
                                                <span wire:loading.remove wire:target="delete({{ (int) $b->bookingroom_id }})">Delete</span>
                                                <span wire:loading wire:target="delete({{ (int) $b->bookingroom_id }})">...</span>
                                            </button>
                                            @else
                                            <button class="{{ $btnBlk }} !bg-emerald-600 hover:!bg-emerald-700 w-full"
                                                wire:click="restore({{ (int) $b->bookingroom_id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="restore({{ (int) $b->bookingroom_id }})">
                                                <span wire:loading.remove wire:target="restore({{ (int) $b->bookingroom_id }})">Restore</span>
                                                <span wire:loading wire:target="restore({{ (int) $b->bookingroom_id }})">...</span>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    @if($bookings->hasPages())
                    <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-center">
                            {{ $bookings->withQueryString()->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </section>
        </div>

        {{-- FILTER MODAL (BOTTOM SHEET HANYA UNTUK MOBILE) --}}
        @if($showFilterModal)
            <div class="fixed inset-0 z-[60] lg:hidden"> {{-- Tampilkan hanya di LG ke bawah --}}
                <div class="absolute inset-0 bg-black/50" wire:click="closeFilterModal"></div>
                <div class="absolute inset-x-0 bottom-0 bg-white rounded-t-2xl shadow-xl max-h-[80vh] overflow-hidden">
                    
                    <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Filter Settings</h3>
                            <p class="text-[11px] text-gray-500">Filter daftar booking berdasarkan kriteria tertentu.</p>
                        </div>
                        <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeFilterModal" aria-label="Close">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-4 space-y-5 max-h-[70vh] overflow-y-auto">

                        {{-- Search (Mobile only) --}}
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-2 border-b pb-1">Search Title/Notes</h4>
                            <div class="relative">
                                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search title, notes or user..."
                                    class="{{ $input }} pl-10 w-full placeholder:text-gray-400">
                                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                            </div>
                        </div>

                        {{-- Filter by Room --}}
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-2 border-b pb-1">Filter by Room</h4>
                            <button type="button"
                                wire:click="clearRoomFilter"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs font-medium
                                            {{ is_null($roomFilterId) ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                                <span>All Rooms</span>
                                @if(is_null($roomFilterId))
                                <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                                @endif
                            </button>

                            <div class="mt-2 space-y-1.5 max-h-40 overflow-y-auto">
                                @forelse($roomOptionsFormatted as $r)
                                @php $active = (int) $roomFilterId === (int) $r['id']; @endphp
                                <button type="button"
                                    wire:click="selectRoom({{ $r['id'] }})"
                                    class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs
                                                    {{ $active ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                                    <span class="truncate">{{ $r['label'] }}</span>
                                    @if($active)
                                    <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                                    @endif
                                </button>
                                @empty
                                    <p class="text-xs text-gray-500 p-2">No room data found.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Filter by Department --}}
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-2 border-b pb-1">Filter by Department</h4>
                            <button type="button"
                                wire:click="clearDepartmentFilter"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs font-medium
                                            {{ is_null($departmentFilterId) ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                                <span>All Departments</span>
                                @if(is_null($departmentFilterId))
                                <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                                @endif
                            </button>

                            <div class="mt-2 space-y-1.5 max-h-40 overflow-y-auto">
                                @forelse($deptOptionsFormatted as $d)
                                @php $active = (int) $departmentFilterId === (int) $d['id']; @endphp
                                <button type="button"
                                    wire:click="selectDepartment({{ $d['id'] }})"
                                    class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs
                                                    {{ $active ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                                    <span class="truncate">{{ $d['label'] }}</span>
                                    @if($active)
                                    <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                                    @endif
                                </button>
                                @empty
                                    <p class="text-xs text-gray-500 p-2">No department data found.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Filter by Type --}}
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-2 border-b pb-1">Filter by Type</h4>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($bookingTypes as $key => $label)
                                @php $active = $typeScope === $key; @endphp
                                <button type="button"
                                    wire:click="setTypeScope('{{ $key }}')"
                                    class="w-full flex items-center justify-center text-center gap-2 px-3 py-2 rounded-lg text-xs
                                                    {{ $active ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100 border border-gray-200' }}">
                                    <span class="truncate">{{ $label }}</span>
                                </button>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    <div class="px-4 py-3 border-t border-gray-200 shrink-0">
                        <button type="button"
                            class="w-full h-10 rounded-xl bg-gray-900 text-white text-sm font-medium"
                            wire:click="closeFilterModal">
                            Apply & Close
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- MODAL: EDIT ONLY (MENGGUNAKAN DESAIN LAMA/LEBAR) --}}
        @if($modal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true" wire:key="modal-br" wire:keydown.escape.window="closeModal">
            <button type="button" class="absolute inset-0 bg-black/50" aria-label="Close overlay" wire:click="closeModal"></button>

            <div class="relative w-full max-w-2xl mx-4 {{ $card }} focus:outline-none" tabindex="-1">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Edit Booking #{{ $editingId }}</h3>
                    <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeModal" aria-label="Close">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <form class="p-5" wire:submit.prevent="update">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="{{ $label }}">Room</label>
                            <select class="{{ $input }}" wire:model.defer="room_id">
                                <option value="">Choose room</option>
                                @foreach ($roomLookup as $rid => $rname)
                                <option value="{{ $rid }}">{{ $rname }}</option>
                                @endforeach
                            </select>
                            @error('room_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Department</label>
                            <select class="{{ $input }}" wire:model.defer="department_id">
                                <option value="">Choose department</option>
                                @foreach ($deptLookup as $did => $dname)
                                <option value="{{ $did }}">{{ $dname }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="{{ $label }}">Meeting Title</label>
                            <input type="text" class="{{ $input }}" wire:model.defer="meeting_title" placeholder="Weekly Sync / Project Kickoff">
                            @error('meeting_title') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Date</label>
                            <input type="date" class="{{ $input }}" wire:model.defer="date">
                            @error('date') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Attendees</label>
                            <input type="number" min="1" class="{{ $input }}" wire:model.defer="number_of_attendees" placeholder="e.g. 10">
                            @error('number_of_attendees') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
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
                        <div class="md:col-span-2">
                            <label class="{{ $label }}">Special Notes (optional)</label>
                            <textarea class="{{ $input }} h-24" wire:model.defer="special_notes" placeholder="Agenda, equipment needs, etc."></textarea>
                            @error('special_notes') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        {{-- Requirements checklist --}}
                        <div class="md:col-span-2">
                            <label class="{{ $label }}">Requirements</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-1">
                                @foreach ($allRequirements as $req)
                                <label class="flex items-center space-x-2 text-sm text-gray-700">
                                    <input type="checkbox"
                                        wire:model.defer="selectedRequirements"
                                        value="{{ $req->requirement_id }}"
                                        class="rounded border-gray-300 text-gray-900 focus:ring-gray-900/30">
                                    <span>{{ $req->name }}</span>
                                </label>
                                @endforeach
                            </div>
                            @error('selectedRequirements') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                        <button type="button" class="{{ $btnLt }}" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="{{ $btnBlk }}" wire:loading.attr="disabled" wire:target="update">
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