{{-- resources/views/livewire/pages/admin/ticket.blade.php --}}
<div class="bg-gray-50 min-h-screen" wire:poll.800ms="tick" wire:poll.keep-alive.2s="tick" wire:key="ticket-support-page">
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

    // Priority colors
    $priorityColors = [
        'low' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'medium' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'high' => 'bg-rose-50 text-rose-700 ring-rose-200',
    ];

    // Status colors
    $statusColors = [
        'open' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'in_progress' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        'resolved' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'closed' => 'bg-gray-100 text-gray-700 ring-gray-200',
    ];

    $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
    $titleC = 'text-base font-semibold text-gray-900';
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
                            <x-heroicon-o-lifebuoy class="w-6 h-6 text-white" />
                        </div>

                        <div class="space-y-1.5">
                            <h2 class="text-xl sm:text-2xl font-semibold leading-tight">
                                Ticket Support
                            </h2>

                            <div class="text-sm text-white/80 flex flex-col sm:block">
                                <span>Cabang: <span class="font-semibold">{{ $company_name }}</span></span>
                                <span class="hidden sm:inline mx-2">•</span>
                                <span>Departemen: <span class="font-semibold">{{ $department_name }}</span></span>
                            </div>

                            <p class="text-xs text-white/60 pt-1 sm:pt-0">
                                Menampilkan informasi untuk departemen:
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
                <x-heroicon-o-funnel class="w-5 h-5 inline-block mr-1 align-text-bottom text-gray-500" />
                Filter Tiket
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                {{-- SEARCH INPUT --}}
                <div class="sm:col-span-2">
                    <label class="{{ $label }}">Cari Tiket</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400" />
                        </span>
                        <input
                            type="text"
                            wire:model.live.debounce.400ms="search"
                            class="{{ $input }} pl-9"
                            placeholder="Cari judul atau catatan…">
                    </div>
                </div>

                {{-- ASSIGNMENT FILTER --}}
                <div>
                    <label class="{{ $label }}">Assignment</label>
                    <select wire:model.live="assignment" class="{{ $input }}">
                        <option value="">All assignments</option>
                        <option value="unassigned">Unassigned</option>
                        <option value="assigned">Assigned</option>
                    </select>
                </div>

                {{-- PRIORITY FILTER --}}
                <div>
                    <label class="{{ $label }}">Priority</label>
                    <select wire:model.live="priority" class="{{ $input }}">
                        <option value="">All priorities</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                {{-- STATUS FILTER --}}
                <div>
                    <label class="{{ $label }}">Status</label>
                    <select wire:model.live="status" class="{{ $input }}">
                        <option value="">All status</option>
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>

            {{-- ACTIVE FILTERS CHIPS --}}
            @if($search || $priority || $status || $assignment)
            <div class="flex flex-wrap items-center gap-2 pt-4 mt-4 border-t border-gray-100">
                @if($search)
                <span class="{{ $chip }} bg-gray-100 text-gray-700 ring-gray-200">
                    <span class="opacity-80">Search:</span>
                    <span class="font-medium">{{ $search }}</span>
                </span>
                @endif

                @if($priority)
                @php $prioKey = strtolower($priority); @endphp
                <span class="{{ $chip }} {{ $priorityColors[$prioKey] ?? 'bg-gray-100 text-gray-700 ring-gray-200' }}">
                    <span class="opacity-80">Priority:</span>
                    <span class="capitalize">{{ $prioKey }}</span>
                </span>
                @endif

                @if($status)
                @php $statusKey = strtolower($status); @endphp
                <span class="{{ $chip }} {{ $statusColors[$statusKey] ?? 'bg-gray-100 text-gray-700 ring-gray-200' }}">
                    <span class="opacity-80">Status:</span>
                    <span class="capitalize">{{ str_replace('_',' ', $statusKey) }}</span>
                </span>
                @endif

                @if($assignment)
                <span class="{{ $chip }} bg-purple-50 text-purple-700 ring-purple-200">
                    <span class="opacity-80">Assignment:</span>
                    <span class="capitalize">{{ $assignment }}</span>
                </span>
                @endif

                <button wire:click="resetFilters" type="button" class="text-xs underline text-gray-600 hover:text-gray-900 ml-1">
                    Reset filters
                </button>
            </div>
            @endif
        </div>

        {{-- TABLE SECTION --}}
        <div class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-ticket class="w-5 h-5 text-gray-700" />
                    <div>
                        <h3 class="{{ $titleC }}">Daftar Tiket</h3>
                        <p class="text-xs text-gray-500">Semua tiket support dari chatbot.</p>
                    </div>
                </div>
                <span class="{{ $mono }}">Total: {{ $tickets->total() }}</span>
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden md:block overflow-x-auto">
                @if($tickets->count())
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-3">#</th>
                            <th scope="col" class="px-6 py-3">Subject</th>
                            <th scope="col" class="px-6 py-3">Priority</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Department</th>
                            <th scope="col" class="px-6 py-3">Assigned To</th>
                            <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tickets as $t)
                        @php
                        $prioKey = strtolower($t->priority);
                        $statusKey = strtolower($t->status);
                        $rowNumber = $tickets->firstItem() + $loop->index;
                        @endphp
                        <tr wire:key="t-desktop-{{ $t->ticket_id }}" class="bg-white border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <span class="{{ $mono }}">#{{ $rowNumber }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 line-clamp-1">
                                            {{ $t->subject }}
                                        </p>
                                        <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">
                                            Pengajuan via Chatbot
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="{{ $chip }} {{ $priorityColors[$prioKey] ?? 'bg-gray-100 text-gray-700 ring-gray-200' }}">
                                    <x-heroicon-o-flag class="w-3.5 h-3.5" />
                                    <span class="capitalize">{{ $prioKey }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="{{ $chip }} {{ $statusColors[$statusKey] ?? 'bg-gray-100 text-gray-700 ring-gray-200' }}">
                                    <x-heroicon-o-clock class="w-3.5 h-3.5" />
                                    <span class="capitalize">{{ str_replace('_', ' ', $statusKey) }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                {{ $t->department->department_name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                @if($t->assignment && $t->assignment->agent)
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-user-circle class="w-4 h-4 text-gray-400" />
                                        <span>{{ $t->assignment->agent->full_name }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.ticket.show', $t) }}"
                                    class="{{ $btnBlk }}">
                                    <x-heroicon-o-eye class="w-4 h-4 mr-1" />
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="px-6 py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center gap-2">
                        <x-heroicon-o-inbox class="w-12 h-12 text-gray-300" />
                        <p>Tidak ada tiket ditemukan.</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Mobile Table View --}}
            <div class="md:hidden">
                @if($tickets->count())
                <table class="w-full text-sm">
                    <tbody>
                        @foreach ($tickets as $t)
                        @php
                        $prioKey = strtolower($t->priority);
                        $statusKey = strtolower($t->status);
                        $rowNumber = $tickets->firstItem() + $loop->index;
                        @endphp
                        <tr wire:key="t-mobile-{{ $t->ticket_id }}" class="bg-white border-b">
                            <td class="p-4">
                                <div class="space-y-3">
                                    {{-- Row Number & Priority --}}
                                    <div class="flex items-center justify-between">
                                        <span class="{{ $mono }}">#{{ $rowNumber }}</span>
                                        <span class="{{ $chip }} {{ $priorityColors[$prioKey] ?? 'bg-gray-100 text-gray-700 ring-gray-200' }} text-[10px]">
                                            <x-heroicon-o-flag class="w-3 h-3" />
                                            <span class="capitalize">{{ $prioKey }}</span>
                                        </span>
                                    </div>

                                    {{-- Subject --}}
                                    <div class="text-gray-900">
                                        <div class="font-semibold text-base">{{ $t->subject }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Pengajuan via Chatbot
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <span class="{{ $chip }} {{ $statusColors[$statusKey] ?? 'bg-gray-100 text-gray-700 ring-gray-200' }} text-[10px]">
                                            <x-heroicon-o-clock class="w-3 h-3" />
                                            <span class="capitalize">{{ str_replace('_', ' ', $statusKey) }}</span>
                                        </span>
                                    </div>

                                    {{-- Department --}}
                                    <div class="text-xs text-gray-700">
                                        <div class="flex items-center gap-1.5">
                                            <x-heroicon-o-building-office class="w-3.5 h-3.5 text-gray-400" />
                                            <span class="font-medium">{{ $t->department->department_name ?? '—' }}</span>
                                        </div>
                                    </div>

                                    {{-- Assigned To --}}
                                    <div class="text-xs text-gray-700">
                                        <div class="flex items-center gap-1.5">
                                            <x-heroicon-o-user-circle class="w-3.5 h-3.5 text-gray-400" />
                                            @if($t->assignment && $t->assignment->agent)
                                                <span class="font-medium">{{ $t->assignment->agent->full_name }}</span>
                                            @else
                                                <span class="text-gray-400 italic">Unassigned</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Action Button --}}
                                    <div class="pt-2">
                                        <a href="{{ route('admin.ticket.show', $t) }}"
                                            class="{{ $btnBlk }} w-full justify-center">
                                            <x-heroicon-o-eye class="w-4 h-4 mr-1" />
                                            <span>Lihat Detail</span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="px-6 py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center gap-2">
                        <x-heroicon-o-inbox class="w-12 h-12 text-gray-300" />
                        <p>Tidak ada tiket ditemukan.</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Pagination --}}
            @if ($tickets->hasPages())
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-center">
                    {{ $tickets->links() }}
                </div>
            </div>
            @endif
        </div>
    </main>
</div>