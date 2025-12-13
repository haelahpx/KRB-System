<div class="bg-gray-50 min-h-screen">
    @php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Storage;

    if (!function_exists('fmtDate')) {
    function fmtDate($v){
    try { return $v ? Carbon::parse($v)->format('d M Y') : '—'; }
    catch (\Throwable) { return '—'; }
    }
    }
    if (!function_exists('fmtTime')) {
    function fmtTime($v){
    try { return $v ? Carbon::parse($v)->format('H.i') : '—'; }
    catch (\Throwable) {
    if (is_string($v)) {
    if (preg_match('/^\d{2}:\d{2}/', $v)) return str_replace(':','.', substr($v,0,5));
    if (preg_match('/^\d{2}\.\d{2}/', $v)) return substr($v,0,5);
    }
    return '—';
    }
    }
    }
    if (!function_exists('photoUrl')) {
    function photoUrl($path) {
    if (!$path) return null;
    if (preg_match('#^https?://#', $path)) return $path;
    return Storage::url($path);
    }
    }

    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    $btnRed = 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition';
    $btnLt = 'px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900/10 disabled:opacity-60 transition';
    $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-[11px]';
    $ico = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
    $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
    $icoAvatar = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';

    $statusMap = [
        'rejected' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-800', 'label' => 'Rejected'],
        'completed' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'label' => 'Completed'],
    ];

    $showFilterModal = $showFilterModal ?? false;
    $previewUrl = $previewUrl ?? null;
    $showPreviewModal = $showPreviewModal ?? false;
    $showEditModal = $showEditModal ?? false;

    $vehicleMap = $vehicleMap ?? [];
    $vehicles = $vehicles ?? collect();
    $vehicleFilter = $vehicleFilter ?? null;
    $statusTab = $statusTab ?? 'done';
    $q = $q ?? '';
    $selectedDate = $selectedDate ?? '';
    $sortFilter = $sortFilter ?? 'recent';
    $editingBookingId = $editingBookingId ?? null;
    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-8">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-0 p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <x-heroicon-o-document-text class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Vehicle History Management</h2>
                            <p class="text-sm text-white/80">
                                Cabang: <span
                                    class="font-semibold">{{ optional(Auth::user()->company)->company_name ?? '-' }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <label class="inline-flex items-center gap-2 text-sm text-white/90 cursor-pointer">
                            <input type="checkbox"
                                wire:model.live="includeDeleted"
                                class="w-4 h-4 rounded border-white/30 bg-white/10 text-gray-900 focus:ring-2 focus:ring-white/20 cursor-pointer">
                            <span>Include Deleted</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        @if (session()->has('success'))
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl px-4 py-3 text-sm text-gray-800">
            {{ session('success') }}
        </div>
        @endif

        <div class="lg:hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="flex-1">
                    <p class="text-xs text-gray-600 font-medium">
                        @if(!is_null($vehicleFilter))
                        Vehicle: {{ $vehicleMap[$vehicleFilter] ?? '—' }}
                        @else
                        Showing {{ $statusTab === 'rejected' ? 'Rejected' : 'Completed' }} Records
                        @endif
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $q ? 'Searching for: ' . $q : 'Tap filter to adjust settings.' }}
                    </p>
                </div>
                <button type="button" class="{{ $btnBlk }} !h-8 !px-4 !py-0 shrink-0" wire:click="openFilterModal">
                    Filter
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">

            <section class="space-y-4">
                <div class="{{ $card }}">
                    <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200 hidden lg:block">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                            <div>
                                <label class="{{ $label }}">Search Purpose/User</label>
                                <div class="relative">
                                    <input type="text" wire:model.live.debounce.400ms="q" placeholder="Search purpose, destination, borrower…"
                                        class="{{ $input }} pl-10 w-full placeholder:text-gray-400">
                                    <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                </div>
                            </div>

                            <div>
                                <label class="{{ $label }}">Filter by Vehicle</label>
                                <select wire:model.live="vehicleFilter" class="{{ $input }}">
                                    <option value="">— All Vehicles —</option>
                                    @foreach ($vehicleMap as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="{{ $label }}">Filter by Date</label>
                                <div class="relative">
                                    <input type="date" class="{{ $input }}" wire:model.live="selectedDate">
                                </div>
                            </div>

                            <div>
                                <label class="{{ $label }}">Sort & Clear</label>
                                <div class="flex items-center gap-2 h-10">
                                    <select wire:model.live="sortFilter" class="{{ $input }} flex-1 !mb-0">
                                        <option value="recent">Default (terbaru)</option>
                                        <option value="oldest">Terlama dulu</option>
                                        <option value="nearest">Paling dekat</option>
                                    </select>
                                    @if(!is_null($vehicleFilter) || $selectedDate || $q)
                                    <button type="button"
                                        title="Clear All Filters"
                                        wire:click="clearFilters"
                                        class="w-10 h-10 flex items-center justify-center rounded-lg border border-rose-300 text-rose-600 hover:bg-rose-50 transition shrink-0">
                                        <x-heroicon-o-x-mark class="w-5 h-5" />
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 mt-3 border-t border-gray-100">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">Riwayat Kendaraan</h3>
                                <p class="text-xs text-gray-500">
                                    Menampilkan data {{ $statusTab === 'rejected' ? 'Rejected' : 'Completed' }}
                                </p>
                            </div>
                            <div class="inline-flex items-center bg-gray-100 rounded-full p-1 text-xs font-medium">
                                <button type="button"
                                    wire:click="$set('statusTab','completed')"
                                    class="px-3 py-1 rounded-full transition
                                            {{ $statusTab === 'completed'
                                                ? 'bg-gray-900 text-white shadow-sm'
                                                : 'text-gray-700 hover:bg-gray-200' }}">
                                    Completed
                                </button>
                                <button type="button"
                                    wire:click="$set('statusTab','rejected')"
                                    class="px-3 py-1 rounded-full transition
                                            {{ $statusTab === 'rejected'
                                                ? 'bg-gray-900 text-white shadow-sm'
                                                : 'text-gray-700 hover:bg-gray-200' }}">
                                    Rejected
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200 lg:hidden">
                         <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">Riwayat Kendaraan</h3>
                                <p class="text-xs text-gray-500">
                                    Menampilkan data {{ $statusTab === 'rejected' ? 'Rejected' : 'Completed' }}
                                </p>
                            </div>
                            <div class="inline-flex items-center bg-gray-100 rounded-full p-1 text-xs font-medium">
                                <button type="button"
                                    wire:click="$set('statusTab','completed')"
                                    class="px-3 py-1 rounded-full transition
                                            {{ $statusTab === 'completed'
                                                ? 'bg-gray-900 text-white shadow-sm'
                                                : 'text-gray-700 hover:bg-gray-200' }}">
                                    Completed
                                </button>
                                <button type="button"
                                    wire:click="$set('statusTab','rejected')"
                                    class="px-3 py-1 rounded-full transition
                                            {{ $statusTab === 'rejected'
                                                ? 'bg-gray-900 text-white shadow-sm'
                                                : 'text-gray-700 hover:bg-gray-200' }}">
                                    Rejected
                                </button>
                            </div>
                        </div>
                    </div>


                    <div class="px-4 sm:px-6 py-5">
                        @if($bookings->isEmpty())
                        <div class="py-14 text-center text-gray-500 text-sm">
                            Belum ada riwayat peminjaman pada tab ini.
                        </div>
                        @else
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            @foreach($bookings as $b)
                            @php
                            $vehicleName = $vehicleMap[$b->vehicle_id] ?? 'Unknown';
                            $avatarChar = strtoupper(substr($vehicleName, 0, 1));
                            $isRejected = ($b->status === 'rejected');
                            $statusStyle = $statusMap[$b->status] ?? $statusMap['completed'];
                            $rowNo = (($bookings->firstItem() ?? 1) + $loop->index);

                            $beforeC = $photoCounts[$b->vehiclebooking_id]['before'] ?? 0;
                            $afterC = $photoCounts[$b->vehiclebooking_id]['after'] ?? 0;
                            $before = $photosByBooking[$b->vehiclebooking_id]['before'] ?? collect();
                            $after = $photosByBooking[$b->vehiclebooking_id]['after'] ?? collect();
                            @endphp

                            <div class="bg-white border border-gray-200 rounded-xl p-4 sm:px-5 sm:py-4 hover:shadow-sm hover:border-gray-300 transition {{ $b->deleted_at ? 'opacity-50 border-rose-300' : '' }}"
                                wire:key="vh-{{ $b->vehiclebooking_id }}">
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <div class="flex items-start gap-3 flex-1 min-w-0">
                                        <div class="{{ $ico }} w-10 h-10 rounded-xl text-sm hidden sm:flex">
                                            {{ $avatarChar }}
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-2 mb-1">
                                                <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate flex-1">
                                                    {{ $b->purpose ? ucfirst($b->purpose) : 'Vehicle Booking' }}
                                                </h4>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                <span class="text-[11px] px-2 py-0.5 rounded-md font-medium {{ $statusStyle['bg'] }} {{ $statusStyle['text'] }} border {{ $statusStyle['bg'] }}">
                                                    {{ $statusStyle['label'] }}
                                                </span>
                                                <span class="{{ $chip }} bg-gray-50 border border-gray-200 text-gray-600">
                                                    #{{ $b->vehiclebooking_id }}
                                                </span>
                                                @if($b->deleted_at)
                                                <span class="text-[11px] px-2 py-0.5 rounded-md bg-rose-600 text-white font-medium">
                                                    DELETED
                                                </span>
                                                @endif
                                            </div>

                                            <p class="text-[12px] text-gray-500">
                                                Vehicle: <span class="font-medium text-gray-700 truncate">{{ $vehicleName }}</span>
                                            </p>

                                            <div class="flex flex-wrap items-center gap-2 mt-2 text-xs">
                                                <span class="{{ $chip }}"><span class="text-gray-500">Borrower:</span><span class="font-medium text-gray-700">{{ $b->borrower_name ?? 'N/A' }}</span></span>
                                                <span class="{{ $chip }}"><span class="text-gray-500">From:</span><span class="font-medium text-gray-700">{{ fmtDate($b->start_at) }} {{ fmtTime($b->start_at) }}</span></span>
                                                <span class="{{ $chip }}"><span class="text-gray-500">To:</span><span class="font-medium text-gray-700">{{ fmtTime($b->end_at) }}</span></span>
                                                @if(!empty($b->destination))
                                                <span class="{{ $chip }} bg-gray-200">
                                                    <x-heroicon-o-map-pin class="w-3.5 h-3.5 text-gray-600" />
                                                    <span class="font-medium text-gray-800">{{ $b->destination }}</span>
                                                </span>
                                                @endif
                                            </div>

                                            @if(!empty($b->notes) && $isRejected)
                                            <div class="mt-3 text-xs bg-rose-50 border border-rose-200 rounded-lg px-3 py-2">
                                                <div class="font-semibold text-rose-700 inline-flex items-center gap-1 mb-1">
                                                    <x-heroicon-o-x-circle class="w-3.5 h-3.5" />
                                                    Reject Reason
                                                </div>
                                                <div class="text-rose-800">{{ $b->notes }}</div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-col justify-end items-end shrink-0 pt-1">
                                        <div class="hidden sm:flex justify-end w-full mb-2">
                                            <div class="h-8 {{ $mono }} flex items-center justify-center !py-0 !px-2.5 w-full">No. {{ $rowNo }}</div>
                                        </div>

                                        <div class="flex flex-col gap-2 pt-1 w-full">

                                            <button class="{{ $btnLt }} w-full"
                                                wire:click="openDetails({{ (int) $b->vehiclebooking_id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="openDetails({{ (int) $b->vehiclebooking_id }})">
                                                <span wire:loading.remove wire:target="openDetails({{ (int) $b->vehiclebooking_id }})">Open</span>
                                                <span wire:loading wire:target="openDetails({{ (int) $b->vehiclebooking_id }})">...</span>
                                            </button>

                                            <button class="{{ $btnBlk }} w-full"
                                                wire:click="editBooking({{ (int) $b->vehiclebooking_id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="editBooking({{ (int) $b->vehiclebooking_id }})">
                                                <span wire:loading.remove wire:target="editBooking({{ (int) $b->vehiclebooking_id }})">Edit</span>
                                                <span wire:loading wire:target="editBooking({{ (int) $b->vehiclebooking_id }})">...</span>
                                            </button>
                                            
                                            @if(!$b->deleted_at)
                                            <button class="{{ $btnRed }} w-full"
                                                wire:click="deleteBooking({{ (int) $b->vehiclebooking_id }})"
                                                onclick="return confirm('Soft delete this booking?')"
                                                wire:loading.attr="disabled"
                                                wire:target="deleteBooking({{ (int) $b->vehiclebooking_id }})">
                                                <span wire:loading.remove wire:target="deleteBooking({{ (int) $b->vehiclebooking_id }})">Delete</span>
                                                <span wire:loading wire:target="deleteBooking({{ (int) $b->vehiclebooking_id }})">...</span>
                                            </button>
                                            @else
                                            <button class="{{ $btnBlk }} !bg-emerald-600 hover:!bg-emerald-700 w-full"
                                                wire:click="restoreBooking({{ (int) $b->vehiclebooking_id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="restoreBooking({{ (int) $b->vehiclebooking_id }})">
                                                <span wire:loading.remove wire:target="restoreBooking({{ (int) $b->vehiclebooking_id }})">Restore</span>
                                                <span wire:loading wire:target="restoreBooking({{ (int) $b->vehiclebooking_id }})">...</span>
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

                    @if(method_exists($bookings, 'links'))
                    <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-center">
                            {{ $bookings->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </section>
        </div>

        @if($showFilterModal)
            <div class="fixed inset-0 z-[60] lg:hidden">
                <div class="absolute inset-0 bg-black/50" wire:click="closeFilterModal"></div>
                <div class="absolute inset-x-0 bottom-0 bg-white rounded-t-2xl shadow-xl max-h-[80vh] overflow-hidden">
                    
                    <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Filter Settings</h3>
                            <p class="text-[11px] text-gray-500">Sesuaikan kriteria filter riwayat kendaraan.</p>
                        </div>
                        <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeFilterModal" aria-label="Close">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-4 space-y-5 max-h-[70vh] overflow-y-auto">

                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-2 border-b pb-1">Search</h4>
                            <div class="relative">
                                <input type="text" wire:model.live.debounce.400ms="q" placeholder="Purpose, destination, borrower…"
                                    class="{{ $input }} pl-10 w-full placeholder:text-gray-400">
                                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-2 border-b pb-1">Filter by Vehicle</h4>
                            <button type="button"
                                wire:click="$set('vehicleFilter', null)"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs font-medium
                                            {{ is_null($vehicleFilter) ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                                <span>All Vehicles ({{ count($vehicles) }})</span>
                                @if(is_null($vehicleFilter))
                                <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                                @endif
                            </button>

                            <div class="mt-2 space-y-1.5 max-h-40 overflow-y-auto">
                                @forelse($vehicles as $v)
                                @php
                                $vLabel = $v->name ?? $v->plate_number ?? ('#'.$v->vehicle_id);
                                $active = (int) $vehicleFilter === (int) $v->vehicle_id;
                                @endphp
                                <button type="button"
                                    wire:click="$set('vehicleFilter', {{ $v->vehicle_id }})"
                                    class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs
                                                    {{ $active ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                                    <span class="truncate">{{ $vLabel }}</span>
                                    @if($active)
                                    <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                                    @endif
                                </button>
                                @empty
                                    <p class="text-xs text-gray-500 p-2">Tidak ada data kendaraan.</p>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-2 border-b pb-1">Filter by Date</h4>
                            <div class="relative">
                                <input type="date" class="{{ $input }}" wire:model.live="selectedDate">
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-2 border-b pb-1">Sort Order</h4>
                            <select wire:model.live="sortFilter" class="{{ $input }}">
                                <option value="recent">Default (terbaru)</option>
                                <option value="oldest">Terlama dulu</option>
                                <option value="nearest">Paling dekat dengan sekarang</option>
                            </select>
                        </div>
                    </div>

                    <div class="px-4 py-3 border-t border-gray-200 shrink-0">
                        @if(!is_null($vehicleFilter) || $selectedDate || $q)
                        <button type="button"
                            class="{{ $btnLt }} w-full mb-2"
                            wire:click="clearFilters">
                            Clear Current Filters
                        </button>
                        @endif
                        <button type="button"
                            class="w-full h-10 rounded-xl bg-gray-900 text-white text-sm font-medium"
                            wire:click="closeFilterModal">
                            Apply & Close
                        </button>
                    </div>
                </div>
            </div>
        @endif


        @if($showPreviewModal)
            <div class="fixed inset-0 z-[110] flex items-center justify-center" wire:click="closePhotoPreview">
                <div class="absolute inset-0 bg-black/80"></div>
                <div class="relative max-w-full max-h-full">
                    <img src="{{ $previewUrl ?? '' }}" class="max-h-[90vh] max-w-[90vw] object-contain rounded-lg shadow-2xl" alt="Photo Preview">
                    <button class="absolute top-2 right-2 p-2 bg-white/30 rounded-full text-white hover:bg-white/50" wire:click="closePhotoPreview">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>
            </div>
        @endif
        
        @if($showEditModal)
            <div class="fixed inset-0 z-[120] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeEditModal"></div>
                
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-hidden relative">
                    <form wire:submit.prevent="saveBooking">
                        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
                            <h3 class="text-lg font-semibold text-gray-900">Edit Vehicle Booking #{{ $editingBookingId }}</h3>
                            <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeEditModal" aria-label="Close">
                                <x-heroicon-o-x-mark class="w-6 h-6" />
                            </button>
                        </div>

                        <div class="p-5 space-y-4 overflow-y-auto max-h-[calc(90vh-120px)]">
                            
                            <div>
                                <label class="{{ $label }}">Vehicle</label>
                                <select wire:model="editVehicleId" class="{{ $input }}">
                                    <option value="">— Select Vehicle —</option>
                                    @foreach ($vehicles as $v)
                                    <option value="{{ $v->vehicle_id }}">
                                        {{ $v->name ?? $v->plate_number ?? ('#'.$v->vehicle_id) }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('editVehicleId') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="{{ $label }}">Borrower Name</label>
                                <input type="text" wire:model="editBorrowerName" class="{{ $input }}">
                                @error('editBorrowerName') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="{{ $label }}">Purpose</label>
                                <input type="text" wire:model="editPurpose" class="{{ $input }}">
                                @error('editPurpose') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label class="{{ $label }}">Destination (Optional)</label>
                                <input type="text" wire:model="editDestination" class="{{ $input }}">
                                @error('editDestination') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="{{ $label }}">Start Time</label>
                                    <input type="datetime-local" wire:model="editStartAt" class="{{ $input }}">
                                    @error('editStartAt') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="{{ $label }}">End Time</label>
                                    <input type="datetime-local" wire:model="editEndAt" class="{{ $input }}">
                                    @error('editEndAt') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="{{ $label }}">Notes/Admin Notes (Optional)</label>
                                <textarea wire:model="editNotes" class="{{ $input }} h-20 resize-none"></textarea>
                                @error('editNotes') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="px-5 py-3 border-t border-gray-200 flex justify-end sticky bottom-0 bg-white z-10">
                            <button type="submit"
                                class="{{ $btnBlk }} !px-5 !py-2 !text-sm"
                                wire:loading.attr="disabled"
                                wire:target="saveBooking">
                                <span wire:loading.remove wire:target="saveBooking">Save Changes</span>
                                <span wire:loading wire:target="saveBooking">Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </main>
</div>