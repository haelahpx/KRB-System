<div class="bg-gray-50 min-h-screen" wire:key="room-monitoring-history">
    @php
    // LAYOUT HELPERS
    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';

    // BUTTONS
    $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition inline-flex items-center justify-center';
    $btnLt = 'px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300/40 disabled:opacity-60 transition inline-flex items-center justify-center';

    // BADGES & ICONS
    $chip = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium ring-1 ring-inset';
    $chipInfo = 'bg-gray-100 text-gray-700 ring-gray-200';
    $chipStatus = [
        'APPROVED' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'PENDING' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'REJECTED' => 'bg-rose-50 text-rose-700 ring-rose-200',
        'COMPLETED' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'REQUEST' => 'bg-amber-50 text-amber-700 ring-amber-200',
    ];
    $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
    $titleC = 'text-base font-semibold text-gray-900';
    $detailItem = 'py-3 border-b border-gray-100';
    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-8">
        {{-- HEADER SECTION --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-6 w-28 h-28 bg-white/20 rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-6 w-20 h-20 bg-white/10 rounded-full blur-lg"></div>
            </div>

            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    {{-- LEFT SECTION --}}
                    <div class="flex items-start gap-4 sm:gap-6">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20 shrink-0">
                            @svg('heroicon-o-information-circle', 'w-6 h-6 text-white')
                        </div>

                        <div class="space-y-1.5">
                            <h2 class="text-xl sm:text-2xl font-semibold leading-tight">
                                Booking Room Center
                            </h2>

                            <div class="text-sm text-white/80 flex flex-col sm:block">
                                <span>Cabang: <span class="font-semibold">{{ $company_name }}</span></span>
                                <span class="hidden sm:inline mx-2">•</span>
                                <span>Departemen: <span class="font-semibold">{{ $department_name }}</span></span>
                            </div>

                            <p class="text-xs text-white/60 pt-1 sm:pt-0">
                                Menampilkan informasi dan notifikasi untuk departemen:
                                <span class="font-medium">{{ $department_name }}</span>.
                            </p>
                        </div>
                    </div>

                    {{-- RIGHT SECTION --}}
                    @if ($showSwitcher)
                    <div class="w-full lg:w-[32rem] lg:ml-6">
                        <label class="block text-xs font-medium text-white/80 mb-2">
                            Pilih Departemen
                        </label>
                        <select
                            wire:model.live="selected_department_id"
                            class="w-full h-11 sm:h-12 px-3 sm:px-4 rounded-lg border border-white/20 bg-white/10 text-white text-sm placeholder:text-white/60 focus:border-white focus:ring-2 focus:ring-white/30 focus:outline-none transition">
                            <option class="text-gray-900" value="{{ auth()->user()->department_id }}">
                                {{ auth()->user()->department->name }} (Your Primary Department)
                            </option>
                            @foreach ($deptOptions as $opt)
                            <option class="text-gray-900" value="{{ $opt['id'] }}">
                                {{ $opt['name'] }}{{ $opt['id'] === $primary_department_id ? ' — Primary' : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- FILTER & SEARCH SECTION --}}
        <div class="p-5 {{ $card }}">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                @svg('heroicon-o-funnel', 'w-5 h-5 inline-block mr-1 align-text-bottom text-gray-500')
                Filter Riwayat Booking
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                {{-- STATUS FILTER --}}
                <div>
                    <label class="{{ $label }}">Filter Status</label>
                    <select wire:model.live="statusFilter" class="{{ $input }}">
                        <option value="">Semua Status</option>
                        <option value="APPROVED">Approved</option>
                        <option value="PENDING">Pending</option>
                        <option value="REJECTED">Rejected</option>
                        <option value="COMPLETED">Completed</option>
                        <option value="DELETED">Deleted</option>
                    </select>
                </div>

                {{-- BOOKING TYPE FILTER --}}
                <div>
                    <label class="{{ $label }}">Tipe Booking</label>
                    <select wire:model.live="bookingTypeFilter" class="{{ $input }}">
                        <option value="">Semua Tipe</option>
                        <option value="meeting">Offline Meeting</option>
                        <option value="online_meeting">Online Meeting</option>
                    </select>
                </div>

                {{-- SEARCH INPUT --}}
                <div class="sm:col-span-2">
                    <label class="{{ $label }}">Cari Deskripsi/Catatan</label>
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="search"
                        placeholder="Cari judul atau catatan…"
                        class="{{ $input }}">
                </div>

                {{-- SORT SWITCHER BUTTON --}}
                <div>
                    <label class="{{ $label }}">Urutkan Data</label>
                    <button
                        type="button"
                        wire:click="toggleSortDirection"
                        class="w-full {{ $btnLt }} flex items-center justify-center">
                        @if ($sortDirection === 'desc')
                            @svg('heroicon-o-arrow-down-circle', 'w-4 h-4 mr-1')
                            Terbaru
                        @else
                            @svg('heroicon-o-arrow-up-circle', 'w-4 h-4 mr-1')
                            Terlama
                        @endif
                    </button>
                </div>
            </div>
        </div>

        {{-- TABLE SECTION --}}
        <div class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                <div class="flex items-center gap-2">
                    @svg('heroicon-o-calendar-days', 'w-5 h-5 text-gray-700')
                    <div>
                        <h3 class="{{ $titleC }}">Riwayat Booking</h3>
                        <p class="text-xs text-gray-500">Daftar semua booking meeting.</p>
                    </div>
                </div>
                <span class="{{ $mono }}">Total: {{ $bookings->total() }}</span>
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-3">#</th>
                            <th scope="col" class="px-6 py-3">Judul Meeting</th>
                            <th scope="col" class="px-6 py-3">Tipe</th>
                            <th scope="col" class="px-6 py-3">Waktu</th>
                            <th scope="col" class="px-6 py-3">Ruang/Provider</th>
                            <th scope="col" class="px-6 py-3">Peserta</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                        @php
                        $isDeleted = !is_null($booking->deleted_at);
                        $status = $isDeleted ? 'DELETED' : strtoupper($booking->status ?? 'CANCELLED');
                        $statusChipClass = $isDeleted ? 'bg-rose-100 text-rose-800 ring-rose-300' : ($chipStatus[$status] ?? $chipInfo);
                        $rowNumber = $bookings->firstItem() + $loop->index;
                        $isOffline = $booking->booking_type === 'meeting';
                        @endphp

                        <tr class="bg-white border-b hover:bg-gray-50 {{ $isDeleted ? 'opacity-70' : '' }}" wire:key="booking-desktop-{{ $booking->bookingroom_id }}">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <span class="{{ $mono }}">#{{ $rowNumber }}</span>
                            </td>
                            <td class="px-6 py-4 {{ $isDeleted ? 'line-through text-gray-500' : 'text-gray-900' }}">
                                <div class="font-medium">{{ $booking->meeting_title }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($isOffline)
                                    <span class="{{ $chip }} bg-blue-50 text-blue-700 ring-blue-200">
                                        @svg('heroicon-o-building-office-2', 'w-3.5 h-3.5')
                                        Offline
                                    </span>
                                @else
                                    <span class="{{ $chip }} bg-purple-50 text-purple-700 ring-purple-200">
                                        @svg('heroicon-o-wifi', 'w-3.5 h-3.5')
                                        Online
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                <div class="flex flex-col text-xs">
                                    <span class="font-medium">{{ \Illuminate\Support\Carbon::parse($booking->start_time)->format('d M Y') }}</span>
                                    <span class="text-gray-500">{{ \Illuminate\Support\Carbon::parse($booking->start_time)->format('H:i') }} – {{ \Illuminate\Support\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                @if($isOffline)
                                    {{ $booking->room->room_name ?? '—' }}
                                @else
                                    <span class="capitalize">{{ str_replace('_', ' ', $booking->online_provider ?? '—') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                <div class="flex items-center gap-1">
                                    @svg('heroicon-o-user-group', 'w-4 h-4 text-gray-400')
                                    {{ $booking->number_of_attendees }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="{{ $chip }} {{ $statusChipClass }}">
                                    {{ $isDeleted ? 'Dihapus' : ucfirst(strtolower($status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button
                                    type="button"
                                    wire:click="openDetailModal({{ $booking->bookingroom_id }})"
                                    class="{{ $btnBlk }}">
                                    @svg('heroicon-o-eye', 'w-4 h-4 mr-1')
                                    Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    @svg('heroicon-o-inbox', 'w-12 h-12 text-gray-300')
                                    <p>Tidak ada riwayat booking ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Table View --}}
            <div class="md:hidden">
                <table class="w-full text-sm">
                    <tbody>
                        @forelse ($bookings as $booking)
                        @php
                        $isDeleted = !is_null($booking->deleted_at);
                        $status = $isDeleted ? 'DELETED' : strtoupper($booking->status ?? 'CANCELLED');
                        $statusChipClass = $isDeleted ? 'bg-rose-100 text-rose-800 ring-rose-300' : ($chipStatus[$status] ?? $chipInfo);
                        $rowNumber = $bookings->firstItem() + $loop->index;
                        $isOffline = $booking->booking_type === 'meeting';
                        @endphp

                        <tr class="bg-white border-b {{ $isDeleted ? 'opacity-70' : '' }}" wire:key="booking-mobile-{{ $booking->bookingroom_id }}">
                            <td class="p-4">
                                <div class="space-y-3">
                                    {{-- Row Number & Status --}}
                                    <div class="flex items-center justify-between">
                                        <span class="{{ $mono }}">#{{ $rowNumber }}</span>
                                        <span class="{{ $chip }} {{ $statusChipClass }} text-[10px]">
                                            {{ $isDeleted ? 'Dihapus' : ucfirst(strtolower($status)) }}
                                        </span>
                                    </div>

                                    {{-- Title --}}
                                    <div class="{{ $isDeleted ? 'line-through text-gray-500' : 'text-gray-900' }}">
                                        <div class="font-semibold text-base">{{ $booking->meeting_title }}</div>
                                    </div>

                                    {{-- Type & Peserta --}}
                                    <div class="flex items-center gap-3 flex-wrap">
                                        @if($isOffline)
                                            <span class="{{ $chip }} bg-blue-50 text-blue-700 ring-blue-200 text-[10px]">
                                                @svg('heroicon-o-building-office-2', 'w-3 h-3')
                                                Offline
                                            </span>
                                        @else
                                            <span class="{{ $chip }} bg-purple-50 text-purple-700 ring-purple-200 text-[10px]">
                                                @svg('heroicon-o-wifi', 'w-3 h-3')
                                                Online
                                            </span>
                                        @endif
                                        <div class="flex items-center gap-1 text-xs text-gray-600">
                                            @svg('heroicon-o-user-group', 'w-3.5 h-3.5 text-gray-400')
                                            {{ $booking->number_of_attendees }} orang
                                        </div>
                                    </div>

                                    {{-- Time --}}
                                    <div class="text-xs text-gray-700">
                                        <div class="flex items-center gap-1.5">
                                            @svg('heroicon-o-calendar', 'w-3.5 h-3.5 text-gray-400')
                                            <span class="font-medium">{{ \Illuminate\Support\Carbon::parse($booking->start_time)->format('d M Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 mt-1 ml-5">
                                            @svg('heroicon-o-clock', 'w-3.5 h-3.5 text-gray-400')
                                            <span>{{ \Illuminate\Support\Carbon::parse($booking->start_time)->format('H:i') }} – {{ \Illuminate\Support\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                                        </div>
                                    </div>

                                    {{-- Room/Provider --}}
                                    <div class="text-xs text-gray-700">
                                        <div class="flex items-center gap-1.5">
                                            @svg('heroicon-o-map-pin', 'w-3.5 h-3.5 text-gray-400')
                                            <span class="font-medium">
                                                @if($isOffline)
                                                    {{ $booking->room->room_name ?? '—' }}
                                                @else
                                                    <span class="capitalize">{{ str_replace('_', ' ', $booking->online_provider ?? '—') }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Action Button --}}
                                    <div class="pt-2">
                                        <button
                                            type="button"
                                            wire:click="openDetailModal({{ $booking->bookingroom_id }})"
                                            class="{{ $btnBlk }} w-full justify-center">
                                            @svg('heroicon-o-eye', 'w-4 h-4 mr-1')
                                            Lihat Detail
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    @svg('heroicon-o-inbox', 'w-12 h-12 text-gray-300')
                                    <p>Tidak ada riwayat booking ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($bookings->hasPages())
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-center">
                    {{ $bookings->links() }}
                </div>
            </div>
            @endif
        </div>
    </main>

    {{-- BOOKING DETAIL MODAL --}}
    @if ($showDetailModal && $selectedBookingDetail)
    <div
        class="fixed inset-0 z-[60] flex items-center justify-center"
        role="dialog" aria-modal="true"
        wire:key="detail-modal-{{ $selectedBookingDetail->bookingroom_id }}"
        wire:keydown.escape.window="closeDetailModal">
        <button type="button" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" aria-label="Close overlay" wire:click="closeDetailModal"></button>

        <div class="relative w-full max-w-lg mx-4 bg-white rounded-2xl border border-gray-200 shadow-xl overflow-hidden focus:outline-none transform transition-all" tabindex="-1">
            {{-- Modal Header --}}
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    @svg('heroicon-o-eye', 'w-5 h-5 text-gray-700')
                    <h3 class="text-base font-semibold text-gray-900">Detail Booking</h3>
                </div>
                <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeDetailModal" aria-label="Close">
                    @svg('heroicon-o-x-mark', 'w-5 h-5')
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-5 space-y-4">
                {{-- Title and Status --}}
                <div class="pb-2 border-b border-gray-100">
                    <h4 class="text-lg font-bold text-gray-900 mb-1">{{ $selectedBookingDetail->meeting_title }}</h4>
                    <span class="{{ $chip }} {{ !is_null($selectedBookingDetail->deleted_at) ? 'bg-rose-100 text-rose-800 ring-rose-300' : ($chipStatus[strtoupper($selectedBookingDetail->status ?? 'CANCELLED')] ?? $chipInfo) }}">
                        Status: {{ !is_null($selectedBookingDetail->deleted_at) ? 'Dihapus' : ucfirst(strtolower($selectedBookingDetail->status ?? 'Cancelled')) }}
                    </span>
                    <span class="{{ $mono }} ml-2">ID: {{ $selectedBookingDetail->bookingroom_id }}</span>
                </div>

                <div class="divide-y divide-gray-100">
                    {{-- REQUIREMENT DETAILS --}}
                    @if ($selectedBookingDetail->requirements->isNotEmpty())
                    <div class="{{ $detailItem }}">
                        <div class="text-xs font-medium text-gray-500 flex items-center gap-1.5 mb-2 pb-1 border-b border-gray-100">
                            @svg('heroicon-o-check-badge', 'w-4 h-4 text-gray-400')
                            Daftar Kebutuhan:
                        </div>

                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach ($selectedBookingDetail->requirements as $requirement)
                            <span class="{{ $chip }} bg-gray-100 text-gray-700 ring-gray-300">
                                {{ $requirement->name ?? 'N/A' }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Date & Time --}}
                    <div class="{{ $detailItem }}">
                        <div class="text-xs font-medium text-gray-500 flex items-center gap-1.5 mb-1">
                            @svg('heroicon-o-calendar', 'w-4 h-4 text-gray-400')
                            Waktu Booking
                        </div>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ \Illuminate\Support\Carbon::parse($selectedBookingDetail->start_time)->format('d M Y') }}
                            <span class="text-gray-400 mx-1">/</span>
                            {{ \Illuminate\Support\Carbon::parse($selectedBookingDetail->start_time)->format('H:i') }} – {{ \Illuminate\Support\Carbon::parse($selectedBookingDetail->end_time)->format('H:i') }}
                        </p>
                    </div>

                    {{-- Type Details --}}
                    <div class="{{ $detailItem }} grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs font-medium text-gray-500 flex items-center gap-1.5 mb-1">
                                @svg('heroicon-o-user-group', 'w-4 h-4 text-gray-400') Jumlah Peserta
                            </div>
                            <p class="text-sm font-semibold text-gray-800">{{ $selectedBookingDetail->number_of_attendees }}</p>
                        </div>
                        @if ($selectedBookingDetail->booking_type === 'meeting')
                        <div>
                            <div class="text-xs font-medium text-gray-500 flex items-center gap-1.5 mb-1">
                                @svg('heroicon-o-building-office-2', 'w-4 h-4 text-gray-400') Ruang Meeting
                            </div>
                            <p class="text-sm font-semibold text-gray-800">{{ $selectedBookingDetail->room->room_name ?? '—' }}</p>
                        </div>
                        @else
                        <div>
                            <div class="text-xs font-medium text-gray-500 flex items-center gap-1.5 mb-1">
                                @svg('heroicon-o-swatch', 'w-4 h-4 text-gray-400') Provider Online
                            </div>
                            <p class="text-sm font-semibold text-gray-800 capitalize">{{ str_replace('_', ' ', $selectedBookingDetail->online_provider ?? '—') }}</p>
                        </div>
                        @endif
                    </div>

                    {{-- Online Specific Details --}}
                    @if ($selectedBookingDetail->booking_type === 'online_meeting')
                    <div class="{{ $detailItem }} grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs font-medium text-gray-500 mb-1">Kode Meeting</div>
                            <p class="text-sm font-semibold text-gray-800">{{ $selectedBookingDetail->online_meeting_code ?: '—' }}</p>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 mb-1">Password</div>
                            <p class="text-sm font-semibold text-gray-800">{{ $selectedBookingDetail->online_meeting_password ?: '—' }}</p>
                        </div>
                    </div>

                    <div class="{{ $detailItem }}">
                        <div class="text-xs font-medium text-gray-500 mb-1 flex items-center gap-1.5">
                            @svg('heroicon-o-link', 'w-4 h-4 text-gray-400') Meeting URL
                        </div>
                        @if ($selectedBookingDetail->online_meeting_url)
                        <a href="{{ $selectedBookingDetail->online_meeting_url }}" target="_blank"
                            class="text-blue-600 hover:underline break-all text-sm">
                            {{ $selectedBookingDetail->online_meeting_url }}
                        </a>
                        @else
                        <p class="text-sm text-gray-700">—</p>
                        @endif
                    </div>
                    @endif

                    {{-- Notes --}}
                    <div class="pt-3">
                        <div class="text-xs font-medium text-gray-500 mb-1 flex items-center gap-1.5">
                            @svg('heroicon-o-document-text', 'w-4 h-4 text-gray-400') Catatan Khusus Booking
                        </div>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $selectedBookingDetail->special_notes ?: '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="bg-gray-50 px-5 py-4 flex justify-end">
                <button wire:click="closeDetailModal" type="button" class="{{ $btnLt }} inline-flex items-center gap-1.5">
                    @svg('heroicon-o-x-mark', 'w-4 h-4')
                    <span>Tutup</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>