<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="bg-[#0a0a0a] rounded-xl shadow-sm border-2 border-black p-4 md:p-6 mb-4 md:mb-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            {{-- Title --}}
            <h1 class="text-xl md:text-2xl font-bold text-white text-center md:text-left">
                Sistem Tiket Dukungan
            </h1>

            @php
            $isCreate = request()->routeIs('create-ticket') || request()->is('create-ticket');
            $isStatus = request()->routeIs('ticketstatus') || request()->is('ticketstatus');
            @endphp

            {{-- Navigation Tabs --}}
            <div class="flex rounded-lg overflow-hidden bg-gray-100 border border-gray-200 w-full md:w-auto">

                <a href="{{ route('create-ticket') }}"
                    @class([ 'flex-1 md:flex-none px-3 md:px-4 py-2 text-sm font-medium cursor-default border-r border-gray-200 text-center' , 'bg-gray-900 text-white'=> $isCreate,
                    'text-gray-700 hover:text-gray-900 hover:bg-gray-50' => !$isCreate,
                    ])>
                    Buat Tiket
                </a>

                <a href="{{ route('ticketstatus') }}"
                    @class([ 'flex-1 md:flex-none px-3 md:px-4 py-2 text-sm font-medium cursor-default border-r border-gray-200 text-center' , 'bg-gray-900 text-white'=> $isStatus,
                    'text-gray-700 hover:text-gray-900 hover:bg-gray-50' => !$isStatus,
                    ])>
                    Status Tiket
                </a>
            </div>
        </div>
    </div>
    {{-- Main Content --}}
    <div class="bg-white rounded-xl shadow-sm border-2 border-black p-4 md:p-5">
        {{-- Filters --}}
        <div class="flex flex-col md:flex-row md:items-center gap-3 pb-4 mb-4 border-b border-gray-100">

            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 w-full">

                <select wire:model.live="statusFilter"
                    class="w-full px-2 py-1.5 text-xs md:text-sm text-gray-900 border border-gray-300 rounded-md
                   focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="open">Buka</option>
                    <option value="in_progress">Dalam Proses</option>
                    <option value="resolved">Selesai</option>
                    <option value="closed">Ditutup</option>
                </select>

                <select wire:model.live="priorityFilter"
                    class="w-full px-2 py-1.5 text-xs md:text-sm text-gray-900 border border-gray-300 rounded-md
                   focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                    <option value="">Semua Prioritas</option>
                    <option value="low">Rendah</option>
                    <option value="medium">Sedang</option>
                    <option value="high">Tinggi</option>
                </select>

                <select wire:model.live="departmentFilter"
                    class="w-full px-2 py-1.5 text-xs md:text-sm text-gray-900 border border-gray-300 rounded-md
                   focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                    <option value="">Semua Departemen</option>
                    @foreach ($departments as $dept)
                    <option value="{{ $dept->department_id }}">{{ $dept->department_name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="sortFilter"
                    class="w-full px-2 py-1.5 text-xs md:text-sm text-gray-900 border border-gray-300 rounded-md
                   focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                    <option value="recent">Terbaru dulu</option>
                    <option value="oldest">Tertua dulu</option>
                    <option value="due">Jatuh tempo terdekat</option>
                </select>

            </div>
        </div>

        {{-- Cards --}}
        <div class="space-y-4">
            @forelse ($tickets as $t)
            @php
            $priority = strtolower($t->priority ?? '');
            $statusUp = strtoupper($t->status ?? 'OPEN');
            $statusLabel = ucfirst(strtolower(str_replace('_',' ', $statusUp)));
            $userName = $t->user->full_name ?? $t->user->name ?? 'User';
            $initial = strtoupper(mb_substr($userName, 0, 1));

            $isOpen = $statusUp === 'OPEN';
            $isAssignedOrProgress = in_array($statusUp, ['ASSIGNED','IN_PROGRESS'], true);
            $isResolvedOrClosed = in_array($statusUp, ['RESOLVED','CLOSED'], true);

            $hasAgent = (int)($t->agent_count ?? 0) > 0;
            $unreadCount = $t->unread_comments_count ?? 0;
            @endphp

            {{-- TICKET CARD - Responsive: Accordion on Mobile / Full Card on Desktop --}}
            <div class="group relative">

                {{-- MOBILE (Accordion) --}}
                <div class="block md:hidden bg-white rounded-xl border-2 border-black">

                    <div x-data="{ open: false }">

                        {{-- Accordion Toggler --}}
                        <button
                            class="w-full flex items-center justify-between px-4 py-3"
                            @click="open = !open">

                            <div class="flex flex-col text-left">
                                <span class="text-base font-bold text-gray-900 truncate flex items-center gap-1">
                                    {{ $t->subject }}

                                    {{-- unread --}}
                                    @if ($unreadCount > 0)
                                    <span class="text-[9px] bg-red-500 text-white px-1.5 py-0.5 rounded-full font-bold leading-none">
                                        {{ $unreadCount }}
                                    </span>
                                    @endif
                                </span>

                                <span class="text-[11px] text-gray-600">
                                    #{{ $t->ticket_id }} • {{ ucfirst($priority ?: 'rendah') }} Prioritas
                                </span>
                            </div>

                            <span class="transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''">
                                <x-heroicon-o-chevron-down class="w-5 h-5 text-gray-600" />
                            </span>
                        </button>

                        {{-- ACCORDION CONTENT --}}
                        <div x-show="open" x-collapse class="px-4 pb-4 space-y-3">

                            {{-- Badge row --}}
                            <div class="flex flex-wrap items-center gap-1.5 text-[10px]">

                                {{-- Ticket ID --}}
                                <span class="font-mono font-medium bg-gray-100 px-2 py-1 rounded flex items-center gap-1">
                                    <x-heroicon-o-hashtag class="w-3 h-3" /> {{ $t->ticket_id }}
                                </span>

                                {{-- Priority --}}
                                @php
                                $isHigh = $priority==='high';
                                $isMed = $priority==='medium';
                                @endphp

                                <span class="px-2 py-1 rounded-md border flex items-center gap-1
                                {{ $isHigh ? 'bg-orange-50 text-orange-800 border-orange-200' : '' }}
                                {{ $isMed ? 'bg-yellow-50 text-yellow-800 border-yellow-200' : '' }}
                                {{ !$isHigh && !$isMed ? 'bg-gray-50 text-gray-700 border-gray-200' : '' }}">
                                    <x-heroicon-o-bolt class="w-3 h-3" />
                                    {{ ucfirst($priority ?: 'low') }}
                                </span>

                                {{-- Department --}}
                                @if ($t->department)
                                <span class="px-2 py-1 rounded-md border border-gray-200 bg-gray-50 text-gray-700 flex items-center gap-1">
                                    <x-heroicon-o-building-office-2 class="w-3 h-3" />
                                    {{ $t->department->department_name }}
                                </span>
                                @endif

                                {{-- Creator --}}
                                <span class="px-2 py-1 rounded-md border border-gray-200 bg-gray-50 text-gray-700 flex items-center gap-1">
                                    <span class="w-4 h-4 bg-gray-200 rounded-full flex items-center justify-center text-[10px] font-bold text-gray-600">
                                        {{ $initial }}
                                    </span>
                                    {{ $userName }}
                                </span>

                            </div>

                            {{-- Status Badge --}}
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide border
                            @if($isOpen) bg-yellow-100 text-yellow-800 border-yellow-200 @endif
                            @if($isAssignedOrProgress) bg-blue-100 text-blue-800 border-blue-200 @endif
                            @if($isResolvedOrClosed) bg-green-100 text-green-800 border-green-200 @endif">
                                {{ $statusLabel }}
                            </span>

                            {{-- Description --}}
                            <p class="text-xs text-gray-600">
                                {{ $t->description }}
                            </p>

                            {{-- Timestamps --}}
                            <div class="text-[10px] text-gray-500 space-y-1">
                                <div class="flex items-center gap-1">
                                    <x-heroicon-o-clock class="w-3 h-3" />
                                    Dibuat {{ optional($t->created_at)->format('d M Y, H:i') }}
                                </div>
                                <div class="flex items-center gap-1">
                                    <x-heroicon-o-arrow-path class="w-3 h-3" />
                                    Diperbarui {{ optional($t->updated_at)->diffForHumans() }}
                                </div>
                            </div>

                            {{-- Action button --}}
                            @if (in_array($statusUp, ['OPEN','ASSIGNED','IN_PROGRESS','RESOLVED','CLOSED'], true))
                            <div class="mt-1">
                                {{-- Mark as Resolved --}}
                                @if (in_array($statusUp, ['OPEN','ASSIGNED','IN_PROGRESS'], true))
                                <button
                                    @disabled(!$hasAgent)
                                    @class([ 'w-full text-center px-4 py-2 rounded-lg text-xs font-medium transition-colors' ,
                                    $hasAgent
                                    ? 'bg-gray-900 text-white hover:bg-gray-800'
                                    : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                    ])
                                    @if($hasAgent)
                                    wire:click.stop="markComplete({{ (int) $t->ticket_id }})"
                                    @endif
                                    wire:loading.attr="disabled"
                                    wire:target="markComplete"
                                    type="button">
                                    <x-heroicon-o-check-circle class="w-4 h-4 inline-block mr-1" />
                                    Tandai Selesai
                                </button>

                                {{-- Close Ticket --}}
                                @elseif ($statusUp === 'RESOLVED')
                                <button
                                    wire:click.stop="markComplete({{ (int) $t->ticket_id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="markComplete"
                                    type="button"
                                    class="w-full text-center px-4 py-2 bg-gray-900 text-white rounded-lg text-xs font-medium hover:bg-gray-800 transition-colors">
                                    <x-heroicon-o-lock-closed class="w-4 h-4 inline-block mr-1" />
                                    Tutup Tiket
                                </button>

                                {{-- Closed --}}
                                @elseif ($statusUp === 'CLOSED')
                                <span class="w-full flex items-center justify-center gap-2 px-4 py-2 text-[10px] font-bold text-gray-600 bg-gray-100 border border-gray-200 rounded-lg uppercase tracking-wide">
                                    <x-heroicon-o-lock-closed class="w-3 h-3" />
                                    Ditutup
                                </span>
                                @endif

                            </div>
                            @endif

                            {{-- BUTTON to open ticket --}}
                            <div class="mt-1">
                                <a href="{{ route('user.ticket.show', $t) }}"
                                    class="block w-full text-center px-4 py-2 bg-gray-900 text-white rounded-lg text-xs font-medium hover:bg-gray-800">
                                    Buka Tiket
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- DESKTOP (Original Full Card) --}}
                <div class="hidden md:block">
                    <div class="relative bg-white rounded-xl border-2 border-black p-5 hover:shadow-md transition-shadow duration-200">

                        {{-- Clickable wrapper --}}
                        <a href="{{ route('user.ticket.show', $t) }}"
                            class="absolute inset-0 z-20 rounded-xl"></a>

                        {{-- HEADER --}}
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-3 relative z-0 pointer-events-none">

                            <div class="flex-1 min-w-0">

                                {{-- Subject --}}
                                <div class="flex items-center gap-1.5 mb-1">
                                    <h3 class="text-lg font-bold text-gray-900 truncate">{{ $t->subject }}</h3>

                                    @if ($unreadCount > 0)
                                    <span class="text-[10px] bg-red-500 text-white px-1.5 py-0.5 rounded-full font-bold leading-none animate-pulse shrink-0">
                                        {{ $unreadCount }}
                                    </span>
                                    @endif
                                </div>

                                {{-- BADGES --}}
                                <div class="flex flex-wrap items-center gap-1.5 text-xs">

                                    {{-- Ticket ID --}}
                                    <span class="font-mono font-medium text-gray-800 inline-flex items-center gap-1 bg-gray-100 px-2 py-1 rounded">
                                        <x-heroicon-o-hashtag class="w-3 h-3" /> {{ $t->ticket_id }}
                                    </span>

                                    {{-- Priority --}}
                                    @php
                                    $isHigh = $priority === 'high';
                                    $isMedium = $priority === 'medium';
                                    $isLow = !$isHigh && !$isMedium;
                                    @endphp

                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-md border font-medium
                                        {{ $isHigh ? 'bg-orange-50 text-orange-800 border-orange-200' : '' }}
                                        {{ $isMedium ? 'bg-yellow-50 text-yellow-800 border-yellow-200' : '' }}
                                        {{ $isLow ? 'bg-gray-50 text-gray-700 border-gray-200' : '' }}">
                                        <x-heroicon-o-bolt class="w-3 h-3" /> {{ ucfirst($priority ?: 'low') }}
                                    </span>

                                    {{-- Department --}}
                                    @if ($t->department)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md border border-gray-200 bg-gray-50 text-gray-700 font-medium">
                                        <x-heroicon-o-building-office-2 class="w-3 h-3" /> {{ $t->department->department_name }}
                                    </span>
                                    @endif

                                    {{-- Creator --}}
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md border border-gray-200 bg-gray-50 text-gray-700">
                                        <span class="w-4 h-4 bg-gray-200 text-[10px] font-bold rounded-full flex items-center justify-center">
                                            {{ $initial }}
                                        </span>
                                        {{ $userName }}
                                    </span>

                                    {{-- Agent --}}
                                    @if ($hasAgent)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md border border-emerald-200 bg-emerald-50 text-emerald-800 font-medium">
                                        <x-heroicon-o-user-circle class="w-3 h-3" />
                                        Agen Ditugaskan
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Status badge --}}
                            <span
                                class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide border
                                @if($isOpen) bg-yellow-100 text-yellow-800 border-yellow-200 @endif
                                @if($isAssignedOrProgress) bg-blue-100 text-blue-800 border-blue-200 @endif
                                @if($isResolvedOrClosed) bg-green-100 text-green-800 border-green-200 @endif">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        {{-- DESCRIPTION --}}
                        <p class="text-sm text-gray-600 line-clamp-2 mb-3 relative z-0 pointer-events-none">
                            {{ $t->description }}
                        </p>

                        {{-- FOOTER --}}
                        <div class="flex flex-row items-end justify-between pt-3 border-t border-gray-100">

                            {{-- Timestamps --}}
                            <div class="text-[11px] text-gray-500 flex flex-col gap-1">
                                <div class="flex items-center gap-1">
                                    <x-heroicon-o-clock class="w-3 h-3" />
                                    <span>Dibuat {{ optional($t->created_at)->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <x-heroicon-o-arrow-path class="w-3 h-3" />
                                    <span>Diperbarui {{ optional($t->updated_at)->diffForHumans() }}</span>
                                </div>
                            </div>

                            {{-- Action button --}}
                            @if (in_array($statusUp, ['OPEN','ASSIGNED','IN_PROGRESS','RESOLVED','CLOSED'], true))
                            <div class="relative z-30">

                                {{-- Mark as Resolved --}}
                                @if (in_array($statusUp, ['OPEN','ASSIGNED','IN_PROGRESS'], true))
                                <button
                                    @disabled(!$hasAgent)
                                    @class([ 'inline-flex items-center gap-1.5 px-3 md:px-4 py-1.5 md:py-2 text-xs md:text-sm font-medium rounded-lg transition-colors' ,
                                    $hasAgent
                                    ? 'bg-gray-900 text-white hover:bg-gray-800'
                                    : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                    ])
                                    @if($hasAgent)
                                    wire:click.stop="markComplete({{ (int) $t->ticket_id }})"
                                    @endif
                                    wire:loading.attr="disabled"
                                    wire:target="markComplete"
                                    type="button">
                                    <x-heroicon-o-check-circle class="w-4 h-4" />
                                    Tandai Selesai
                                </button>

                                {{-- Close Ticket --}}
                                @elseif ($statusUp === 'RESOLVED')
                                <button
                                    wire:click.stop="markComplete({{ (int) $t->ticket_id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="markComplete"
                                    type="button"
                                    class="inline-flex items-center gap-1.5 px-3 md:px-4 py-1.5 md:py-2 text-xs md:text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors">
                                    <x-heroicon-o-lock-closed class="w-4 h-4" />
                                    Tutup Tiket
                                </button>

                                {{-- Closed --}}
                                @elseif ($statusUp === 'CLOSED')
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 text-[10px] md:text-xs font-bold text-gray-600 bg-gray-100 border border-gray-200 rounded-lg uppercase tracking-wide">
                                    <x-heroicon-o-lock-closed class="w-3 h-3" />
                                    Ditutup
                                </span>
                                @endif

                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                <x-heroicon-o-document-text class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tiket tidak ditemukan</h3>
                <p class="mt-1 text-sm text-gray-500">Coba sesuaikan filter di atas.</p>
            </div>
            @endforelse
        </div>
    </div>

    @if(method_exists($tickets, 'links'))
    <div class="mt-6">
        {{ $tickets->links() }}
    </div>
    @endif
</div>
</div>