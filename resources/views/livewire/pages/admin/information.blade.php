{{-- resources/views/livewire/pages/admin/information-center.blade.php --}}
<div class="bg-gray-50 min-h-screen" wire:key="information-root">
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
    'REQUEST' => 'bg-amber-50 text-amber-700 ring-amber-200',
    'REJECTED' => 'bg-rose-50 text-rose-700 ring-rose-200',
    'CANCELLED' => 'bg-gray-100 text-gray-700 ring-gray-200',
    ];
    $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
    $titleC = 'text-base font-semibold text-gray-900';
    $detailItem = 'py-3 border-b border-gray-100';
    $detailBox = 'mt-3 w-full bg-gray-50 border border-gray-100 rounded-lg p-3 flex flex-col justify-center text-xs text-gray-700';
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
                                Information Center
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

        {{-- FILTER SECTION FOR BOOKING REQUESTS --}}
        <div class="p-5 {{ $card }}">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <x-heroicon-o-funnel class="w-5 h-5 inline-block mr-1 align-text-bottom text-gray-500" />
                Filter Booking Requests
            </h3>

            <div class="grid grid-cols-1 gap-4 pb-6">
                <div>
                    <label class="{{ $label }}">Tipe Booking</label>
                    <select wire:model.live="bookingTypeFilter" class="{{ $input }}">
                        <option value="">Semua Tipe</option>
                        <option value="meeting">Offline Meeting</option>
                        <option value="online_meeting">Online Meeting</option>
                    </select>
                </div>
            </div>

            {{-- BOOKING REQUESTS TABLE --}}
            <section class="{{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                    <div class="flex items-center gap-2">
                        @svg('heroicon-o-clipboard-document-check', 'w-5 h-5 text-gray-700')
                        <div>
                            <h3 class="{{ $titleC }}">Booking Requests</h3>
                            <p class="text-xs text-gray-500">Approved requests awaiting information</p>
                        </div>
                    </div>
                    <span class="{{ $mono }}">Total: {{ $requests->total() }}</span>
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
                                <th scope="col" class="px-6 py-3">Status Info</th>
                                <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($requests as $b)
                            @php
                            $rowNumber = $requests->firstItem() + $loop->index;
                            $title = $b->meeting_title ?: 'Meeting';
                            $date = \Carbon\Carbon::parse($b->date)->translatedFormat('d M Y');
                            $start = \Carbon\Carbon::parse($b->start_time)->format('H:i');
                            $end = \Carbon\Carbon::parse($b->end_time)->format('H:i');
                            $status = strtoupper($b->status ?? 'REQUEST');
                            $needInform = ($b->requestinformation ?? null) === 'request';
                            $statusChipClass = $chipStatus[$status] ?? $chipInfo;
                            $isOffline = $b->booking_type === 'meeting';
                            @endphp

                            <tr class="bg-white border-b hover:bg-gray-50" wire:key="req-desktop-{{ $b->bookingroom_id }}">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <span class="{{ $mono }}">#{{ $rowNumber }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-900">
                                    <div class="font-medium">{{ $title }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ optional($b->user)->name ?? 'Unknown' }} · {{ optional($b->department)->department_name ?? '-' }}
                                    </div>
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
                                        <span class="font-medium">{{ $date }}</span>
                                        <span class="text-gray-500">{{ $start }} – {{ $end }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    @if($isOffline)
                                    {{ $b->room->room_name ?? '—' }}
                                    @else
                                    <span class="capitalize">{{ str_replace('_', ' ', $b->online_provider ?? '—') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($needInform)
                                    <span class="{{ $chip }} bg-amber-50 text-amber-700 ring-amber-200">
                                        @svg('heroicon-o-clock', 'w-3.5 h-3.5')
                                        Pending
                                    </span>
                                    @else
                                    <span class="{{ $chip }} bg-emerald-50 text-emerald-700 ring-emerald-200">
                                        @svg('heroicon-o-check-circle', 'w-3.5 h-3.5')
                                        Informed
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @if ($needInform)
                                        <button type="button"
                                            wire:click.prevent="openInformModal({{ $b->bookingroom_id }})"
                                            class="{{ $btnBlk }}">
                                            @svg('heroicon-o-paper-airplane', 'w-4 h-4 mr-1')
                                            Inform
                                        </button>
                                        <button type="button"
                                            wire:click.prevent="openRejectModal({{ $b->bookingroom_id }})"
                                            class="{{ $btnBlk }} bg-rose-600 hover:bg-rose-700 focus:ring-rose-900/20">
                                            @svg('heroicon-o-x-circle', 'w-4 h-4 mr-1')
                                            Reject
                                        </button>
                                        @else
                                        <span class="text-xs text-gray-500">{{ $b->requestinformation ?? '-' }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        @svg('heroicon-o-inbox', 'w-12 h-12 text-gray-300')
                                        <p>Tidak ada booking request yang pending.</p>
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
                            @forelse ($requests as $b)
                            @php
                            $rowNumber = $requests->firstItem() + $loop->index;
                            $title = $b->meeting_title ?: 'Meeting';
                            $date = \Carbon\Carbon::parse($b->date)->translatedFormat('d M Y');
                            $start = \Carbon\Carbon::parse($b->start_time)->format('H:i');
                            $end = \Carbon\Carbon::parse($b->end_time)->format('H:i');
                            $needInform = ($b->requestinformation ?? null) === 'request';
                            $isOffline = $b->booking_type === 'meeting';
                            @endphp

                            <tr class="bg-white border-b" wire:key="req-mobile-{{ $b->bookingroom_id }}">
                                <td class="p-4">
                                    <div class="space-y-3">
                                        {{-- Row Number & Status --}}
                                        <div class="flex items-center justify-between">
                                            <span class="{{ $mono }}">#{{ $rowNumber }}</span>
                                            @if ($needInform)
                                            <span class="{{ $chip }} bg-amber-50 text-amber-700 ring-amber-200 text-[10px]">
                                                @svg('heroicon-o-clock', 'w-3 h-3')
                                                Pending
                                            </span>
                                            @else
                                            <span class="{{ $chip }} bg-emerald-50 text-emerald-700 ring-emerald-200 text-[10px]">
                                                @svg('heroicon-o-check-circle', 'w-3 h-3')
                                                Informed
                                            </span>
                                            @endif
                                        </div>

                                        {{-- Title --}}
                                        <div class="text-gray-900">
                                            <div class="font-semibold text-base">{{ $title }}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ optional($b->user)->name ?? 'Unknown' }} · {{ optional($b->department)->department_name ?? '-' }}
                                            </div>
                                        </div>

                                        {{-- Type --}}
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
                                        </div>

                                        {{-- Time --}}
                                        <div class="text-xs text-gray-700">
                                            <div class="flex items-center gap-1.5">
                                                @svg('heroicon-o-calendar', 'w-3.5 h-3.5 text-gray-400')
                                                <span class="font-medium">{{ $date }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5 mt-1 ml-5">
                                                @svg('heroicon-o-clock', 'w-3.5 h-3.5 text-gray-400')
                                                <span>{{ $start }} – {{ $end }}</span>
                                            </div>
                                        </div>

                                        {{-- Room/Provider --}}
                                        <div class="text-xs text-gray-700">
                                            <div class="flex items-center gap-1.5">
                                                @svg('heroicon-o-map-pin', 'w-3.5 h-3.5 text-gray-400')
                                                <span class="font-medium">
                                                    @if($isOffline)
                                                    {{ $b->room->room_name ?? '—' }}
                                                    @else
                                                    <span class="capitalize">{{ str_replace('_', ' ', $b->online_provider ?? '—') }}</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Action Buttons --}}
                                        @if ($needInform)
                                        <div class="pt-2 space-y-2">
                                            <button type="button"
                                                wire:click.prevent="openInformModal({{ $b->bookingroom_id }})"
                                                class="{{ $btnBlk }} w-full justify-center">
                                                @svg('heroicon-o-paper-airplane', 'w-4 h-4 mr-1')
                                                Inform
                                            </button>
                                            <button type="button"
                                                wire:click.prevent="openRejectModal({{ $b->bookingroom_id }})"
                                                class="{{ $btnBlk }} bg-rose-600 hover:bg-rose-700 focus:ring-rose-900/20 w-full justify-center">
                                                @svg('heroicon-o-x-circle', 'w-4 h-4 mr-1')
                                                Reject
                                            </button>
                                        </div>
                                        @else
                                        <div class="pt-2 text-xs text-gray-500">
                                            {{ $b->requestinformation ?? '-' }}
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        @svg('heroicon-o-inbox', 'w-12 h-12 text-gray-300')
                                        <p>Tidak ada booking request yang pending.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($requests->hasPages())
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        {{ $requests->links() }}
                    </div>
                </div>
                @endif
            </section>
        </div>

        {{-- FILTER SECTION FOR INFORMATION --}}
        <div class="p-5 {{ $card }}">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <x-heroicon-o-funnel class="w-5 h-5 inline-block mr-1 align-text-bottom text-gray-500" />
                Filter Department Information
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end pb-6">
                <div>
                    <label class="{{ $label }}">Cari Informasi</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400" />
                        </span>
                        <input
                            type="text"
                            wire:model.live.debounce.400ms="search"
                            class="{{ $input }} pl-9"
                            placeholder="Search description…">
                    </div>
                </div>

                <div class="flex justify-start sm:justify-end">
                    <button wire:click="openCreateEditModal('create')" type="button" class="{{ $btnBlk }} inline-flex items-center gap-1.5">
                        @svg('heroicon-o-plus', 'w-4 h-4')
                        <span>New Information</span>
                    </button>
                </div>
            </div>

            {{-- INFORMATION TABLE --}}
            <section class="{{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                    <div class="flex items-center gap-2">
                        @svg('heroicon-o-clipboard-document-list', 'w-5 h-5 text-gray-700')
                        <div>
                            <h3 class="{{ $titleC }}">Department Information</h3>
                            <p class="text-xs text-gray-500">Semua informasi untuk departemen ini.</p>
                        </div>
                    </div>
                    <span class="{{ $mono }}">Total: {{ $rows->total() }}</span>
                </div>

                {{-- Desktop Table View --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3">#</th>
                                <th scope="col" class="px-6 py-3">Deskripsi</th>
                                <th scope="col" class="px-6 py-3">Event Date</th>
                                <th scope="col" class="px-6 py-3">Created</th>
                                <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $r)
                            @php $rowNumber = $rows->firstItem() + $loop->index; @endphp
                            <tr class="bg-white border-b hover:bg-gray-50" wire:key="row-desktop-{{ $r->information_id }}">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <span class="{{ $mono }}">#{{ $rowNumber }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <div class="max-w-md">
                                        <p class="line-clamp-2">{{ $r->description }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    @if($r->event_at)
                                    <div class="flex flex-col text-xs">
                                        <span class="font-medium">{{ \Carbon\Carbon::parse($r->event_at)->format('d M Y') }}</span>
                                        <span class="text-gray-500">{{ \Carbon\Carbon::parse($r->event_at)->format('H:i') }}</span>
                                    </div>
                                    @else
                                    <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    {{ optional($r->created_at)->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button wire:click="openCreateEditModal('edit', {{ $r->information_id }})" type="button" class="{{ $btnBlk }}">
                                        @svg('heroicon-o-pencil-square', 'w-4 h-4 mr-1')
                                        Edit
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        @svg('heroicon-o-clipboard', 'w-12 h-12 text-gray-300')
                                        <p>No information found.</p>
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
                            @forelse ($rows as $r)
                            @php $rowNumber = $rows->firstItem() + $loop->index; @endphp
                            <tr class="bg-white border-b" wire:key="row-mobile-{{ $r->information_id }}">
                                <td class="p-4">
                                    <div class="space-y-3">
                                        {{-- Row Number --}}
                                        <div class="flex items-center justify-between">
                                            <span class="{{ $mono }}">#{{ $rowNumber }}</span>
                                            <span class="text-[10px] text-gray-500">
                                                {{ optional($r->created_at)->diffForHumans() }}
                                            </span>
                                        </div>

                                        {{-- Description --}}
                                        <div class="text-gray-900">
                                            <p class="text-sm leading-relaxed">{{ $r->description }}</p>
                                        </div>

                                        {{-- Event Date --}}
                                        @if($r->event_at)
                                        <div class="text-xs text-gray-700">
                                            <div class="flex items-center gap-1.5">
                                                @svg('heroicon-o-calendar', 'w-3.5 h-3.5 text-gray-400')
                                                <span class="font-medium">{{ \Carbon\Carbon::parse($r->event_at)->format('d M Y') }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5 mt-1 ml-5">
                                                @svg('heroicon-o-clock', 'w-3.5 h-3.5 text-gray-400')
                                                <span>{{ \Carbon\Carbon::parse($r->event_at)->format('H:i') }}</span>
                                            </div>
                                        </div>
                                        @else
                                        <div class="text-xs text-gray-400 flex items-center gap-1.5">
                                            @svg('heroicon-o-calendar', 'w-3.5 h-3.5')
                                            <span>No event date</span>
                                        </div>
                                        @endif

                                        {{-- Action Button --}}
                                        <div class="pt-2">
                                            <button 
                                                wire:click="openCreateEditModal('edit', {{ $r->information_id }})" 
                                                type="button" 
                                                class="{{ $btnBlk }} w-full justify-center">
                                                @svg('heroicon-o-pencil-square', 'w-4 h-4 mr-1')
                                                Edit Information
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        @svg('heroicon-o-clipboard', 'w-12 h-12 text-gray-300')
                                        <p>No information found.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($rows->hasPages())
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">{{ $rows->links() }}</div>
                </div>
                @endif
            </section>
        </div>


        {{-- CREATE / EDIT MODAL --}}
        @if ($mode === 'create' || $mode === 'edit')
        <div class="fixed inset-0 z-[70] flex items-center justify-center p-4" role="dialog" aria-modal="true" wire:key="create-edit-modal" wire:keydown.escape.window="cancel">
            <button type="button" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" aria-label="Close overlay" wire:click="cancel"></button>
            <div class="relative w-full max-w-lg mx-auto bg-white rounded-2xl border border-gray-200 shadow-xl overflow-hidden focus:outline-none transform transition-all" tabindex="-1">
                {{-- Modal Header --}}
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        @svg('heroicon-o-pencil-square', 'w-5 h-5 text-gray-700')
                        <h3 class="text-base font-semibold text-gray-900">
                            {{ $mode === 'create' ? 'New Information' : 'Edit Information #'.$editingId }}
                        </h3>
                    </div>
                    <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="cancel" aria-label="Close">
                        @svg('heroicon-o-x-mark', 'w-5 h-5')
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-5 space-y-5">
                    <div class="{{ $detailBox }} border-dashed border-gray-200">
                        <p class="text-xs font-medium text-gray-600 mb-1">Target Department:</p>
                        <span class="font-semibold text-gray-900">{{ $department_name }}</span>
                    </div>

                    <div>
                        <label class="{{ $label }}">Description</label>
                        <textarea wire:model.defer="description" rows="5" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 transition"></textarea>
                        @error('description') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Event At (Date & Time)</label>
                        <input type="datetime-local" wire:model.defer="event_at" class="{{ $input }}">
                        @error('event_at') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-5 py-4 flex items-center justify-end gap-3 border-t border-gray-200">
                    <button type="button" wire:click="cancel" class="{{ $btnLt }} inline-flex items-center gap-1.5">
                        @svg('heroicon-o-x-mark', 'w-4 h-4')
                        <span>Cancel</span>
                    </button>
                    <button type="button" wire:click="{{ $mode === 'create' ? 'store' : 'update' }}" class="{{ $btnBlk }} inline-flex items-center gap-1.5" wire:loading.attr="disabled" wire:target="{{ $mode === 'create' ? 'store' : 'update' }}">
                        <span class="inline-flex items-center gap-2" wire:loading.remove wire:target="{{ $mode === 'create' ? 'store' : 'update' }}">
                            @svg('heroicon-o-check', 'w-4 h-4')
                            <span>{{ $mode === 'create' ? 'Save Information' : 'Update Information' }}</span>
                        </span>
                        <span class="inline-flex items-center gap-2" wire:loading wire:target="{{ $mode === 'create' ? 'store' : 'update' }}">
                            @svg('heroicon-o-arrow-path', 'h-4 w-4 animate-spin')
                            <span>Processing...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- INFORM MODAL --}}
        @if ($informModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4" role="dialog" aria-modal="true" wire:key="inform-modal" wire:keydown.escape.window="closeInformModal">
            <button type="button" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" aria-label="Close overlay" wire:click="closeInformModal"></button>
            <div class="relative w-full max-w-lg mx-auto bg-white rounded-2xl border border-gray-200 shadow-xl overflow-hidden focus:outline-none transform transition-all" tabindex="-1">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        @svg('heroicon-o-paper-airplane', 'w-5 h-5 text-gray-700')
                        <h3 class="text-base font-semibold text-gray-900">Inform Department</h3>
                    </div>
                    <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeInformModal" aria-label="Close">
                        @svg('heroicon-o-x-mark', 'w-5 h-5')
                    </button>
                </div>
                <div class="p-5 space-y-4">
                    <p class="text-sm text-gray-700">
                        Anda akan mengirim notifikasi informasi terkait booking
                        <span class="font-semibold">"{{ $informBookingTitle ?? 'Request' }}"</span>
                        ke departemen: <span class="font-semibold">{{ $department_name }}</span>.
                        Informasi ini akan muncul di bagian Department Information.
                    </p>

                    <div>
                        <label for="informDescription" class="block text-sm font-bold text-gray-900 mb-1">Data yang akan di-Inform (Dapat Diedit):</label>
                        <textarea
                            id="informDescription"
                            wire:model.defer="informDescription"
                            rows="10"
                            class="w-full rounded-lg border border-gray-300 text-sm font-mono text-gray-800 bg-gray-50 p-3 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 transition whitespace-pre-wrap"></textarea>
                        @error('informDescription') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="px-5 py-4 flex items-center justify-end gap-3 border-t border-gray-200">
                    <button type="button" class="{{ $btnLt }} inline-flex items-center gap-1.5" wire:click="closeInformModal">
                        @svg('heroicon-o-x-mark', 'w-4 h-4')
                        <span>Cancel</span>
                    </button>
                    <button type="button" class="{{ $btnBlk }} inline-flex items-center gap-1.5" wire:click="submitInform" wire:loading.attr="disabled" wire:target="submitInform">
                        <span class="inline-flex items-center gap-2" wire:loading.remove wire:target="submitInform">
                            @svg('heroicon-o-paper-airplane', 'w-4 h-4')
                            <span>Send Information</span>
                        </span>
                        <span class="inline-flex items-center gap-2" wire:loading wire:target="submitInform">
                            @svg('heroicon-o-arrow-path', 'h-4 w-4 animate-spin')
                            <span>Sending…</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- REJECT MODAL --}}
        @if ($rejectModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4" role="dialog" aria-modal="true" wire:key="reject-modal" wire:keydown.escape.window="closeRejectModal">
            <button type="button" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" aria-label="Close overlay" wire:click="closeRejectModal"></button>
            <div class="relative w-full max-w-lg mx-auto bg-white rounded-2xl border border-gray-200 shadow-xl overflow-hidden focus:outline-none transform transition-all" tabindex="-1">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        @svg('heroicon-o-x-circle', 'w-5 h-5 text-gray-700')
                        <h3 class="text-base font-semibold text-gray-900">Reject Booking Request</h3>
                    </div>
                    <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeRejectModal" aria-label="Close">
                        @svg('heroicon-o-x-mark', 'w-5 h-5')
                    </button>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label for="rejectionReason" class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
                        <textarea id="rejectionReason" wire:model="rejectionReason" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 transition"></textarea>
                        @error('rejectionReason') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="px-5 py-4 flex items-center justify-end gap-3 border-t border-gray-200">
                    <button type="button" class="{{ $btnLt }} inline-flex items-center gap-1.5" wire:click="closeRejectModal">
                        @svg('heroicon-o-x-mark', 'w-4 h-4')
                        <span>Cancel</span>
                    </button>
                    <button type="button" class="{{ $btnBlk }} bg-rose-600 hover:bg-rose-700 focus:ring-rose-900/20 inline-flex items-center gap-1.5" wire:click="submitRejection" wire:loading.attr="disabled" wire:target="submitRejection">
                        <span class="inline-flex items-center gap-2" wire:loading.remove wire:target="submitRejection">
                            @svg('heroicon-o-x-circle', 'w-4 h-4')
                            <span>Reject Request</span>
                        </span>
                        <span class="inline-flex items-center gap-2" wire:loading wire:target="submitRejection">
                            @svg('heroicon-o-arrow-path', 'h-4 w-4 animate-spin')
                            <span>Processing…</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
        @endif
    </main>
</div>