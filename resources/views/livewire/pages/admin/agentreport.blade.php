<div class="bg-gray-50 min-h-screen" wire:key="agent-report-page">
    {{-- Download Overlay --}}
    <div id="agentDownloadOverlay" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative h-full w-full flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-xs text-center">
                <div class="mx-auto mb-3 h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-gray-900"></div>
                <p class="font-semibold text-gray-900">Menyiapkan PDF…</p>
                <p class="text-xs text-gray-500 mt-1">Tunggu sampai dialog download muncul.</p>
                <button id="agentHideOverlay" type="button" class="mt-4 text-xs text-gray-600 underline">Sembunyikan</button>
            </div>
        </div>
    </div>

    @php
        // LAYOUT HELPERS
        $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $label = 'block text-sm font-medium text-gray-700 mb-2';
        $chip = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium ring-1 ring-inset';
        $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition inline-flex items-center justify-center';
        $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
        $titleC = 'text-base font-semibold text-gray-900';

        $priorityColors = [
            'Low' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'Medium' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'High' => 'bg-rose-50 text-rose-700 ring-rose-200',
        ];

        $statusColors = [
            'Open' => 'bg-sky-50 text-sky-700 ring-sky-200',
            'In Progress' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
            'Resolved' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'Closed' => 'bg-gray-100 text-gray-700 ring-gray-200',
        ];
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
                            <x-heroicon-o-chart-bar class="w-6 h-6 text-white" />
                        </div>

                        <div class="space-y-1.5">
                            <h2 class="text-xl sm:text-2xl font-semibold leading-tight">
                                Agent Report
                            </h2>

                            <div class="text-sm text-white/80 flex flex-col sm:block">
                                <span>Cabang: <span class="font-semibold">{{ $company_name }}</span></span>
                                <span class="hidden sm:inline mx-2">•</span>
                                <span>Departemen: <span class="font-semibold">{{ $department_name }}</span></span>
                            </div>

                            <p class="text-xs text-white/60 pt-1 sm:pt-0">
                                Overview of Agents and Their Assigned Support Tickets.
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

        {{-- AHT SUMMARY CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php $sum = $ahtSummary ?? null; @endphp

            <div class="{{ $card }}">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm text-gray-600 mb-1">Avg Resolution Time</p>
                            <p class="text-2xl font-bold text-gray-900">
                                ~ {{ is_null($sum['overall_avg'] ?? null) ? '—' : number_format($sum['overall_avg'], 0) }} jam
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Based on {{ $sum['overall_count'] ?? 0 }} resolved tickets</p>
                        </div>
                        <div class="p-3 bg-gray-100 rounded-lg shrink-0">
                            <x-heroicon-o-clock class="w-6 h-6 text-gray-700" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="{{ $card }}">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm text-gray-600 mb-1">Fastest Agent</p>
                            <p class="text-base font-semibold text-gray-900 mb-1">
                                {{ $sum['fastest']['full_name'] ?? '—' }}
                            </p>
                            <p class="text-2xl font-bold text-gray-900">
                                ~ {{ is_null($sum['fastest']['avg_hours'] ?? null) ? '—' : number_format($sum['fastest']['avg_hours'], 0) }} jam
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Handled: {{ $sum['fastest']['count'] ?? 0 }} tickets</p>
                        </div>
                        <div class="p-3 bg-emerald-100 rounded-lg shrink-0">
                            <x-heroicon-o-arrow-trending-up class="w-6 h-6 text-emerald-700" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="{{ $card }}">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm text-gray-600 mb-1">Slowest Agent</p>
                            <p class="text-base font-semibold text-gray-900 mb-1">
                                {{ $sum['slowest']['full_name'] ?? '—' }}
                            </p>
                            <p class="text-2xl font-bold text-gray-900">
                                ~ {{ is_null($sum['slowest']['avg_hours'] ?? null) ? '—' : number_format($sum['slowest']['avg_hours'], 0) }} jam
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Handled: {{ $sum['slowest']['count'] ?? 0 }} tickets</p>
                        </div>
                        <div class="p-3 bg-rose-100 rounded-lg shrink-0">
                            <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-rose-700" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TOP AGENTS BY TICKET COUNT --}}
        <div class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-users class="w-5 h-5 text-gray-700" />
                    <div>
                        <h3 class="{{ $titleC }}">Top Agents by Ticket Count</h3>
                        <p class="text-xs text-gray-500">Agents with the most assigned tickets</p>
                    </div>
                </div>
            </div>

            <div class="p-5">
                @if($topAgents->isEmpty())
                    <div class="py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center gap-2">
                            <x-heroicon-o-inbox class="w-12 h-12 text-gray-300" />
                            <p>No agents found.</p>
                        </div>
                    </div>
                @else
                    @php
                        $max = $topAgents
                            ->map(fn($a) => $allTicketStatsDetailed[$a->user_id]['total'] ?? 0)
                            ->max() ?: 1;
                    @endphp

                    <div class="space-y-4">
                        @foreach($topAgents as $agent)
                            @php
                                $stats = $allTicketStatsDetailed[$agent->user_id] ?? [];
                                $total = $stats['total'] ?? 0;
                                $open = $stats['Open'] ?? 0;
                                $resolved = $stats['Resolved'] ?? 0;
                                $progress = $stats['IN_PROGRESS'] ?? 0;
                                $closed = $stats['Closed'] ?? 0;

                                $agentTickets = $allTickets->where('user_id', $agent->user_id);
                                $slaCounts = [
                                    'Open' => [
                                        'ok' => $agentTickets->where('status', 'OPEN')->where('sla_state.state', 'ok')->count(),
                                        'expired' => $agentTickets->where('status', 'OPEN')->where('sla_state.state', 'expired')->count(),
                                    ],
                                    'IN_PROGRESS' => [
                                        'ok' => $agentTickets->where('status', 'IN_PROGRESS')->where('sla_state.state', 'ok')->count(),
                                        'expired' => $agentTickets->where('status', 'IN_PROGRESS')->where('sla_state.state', 'expired')->count(),
                                    ],
                                    'Resolved' => [
                                        'ok' => $agentTickets->where('status', 'RESOLVED')->where('sla_state.state', 'ok')->count(),
                                        'expired' => $agentTickets->where('status', 'RESOLVED')->where('sla_state.state', 'expired')->count(),
                                    ],
                                    'Closed' => [
                                        'ok' => $agentTickets->where('status', 'CLOSED')->where('sla_state.state', 'ok')->count(),
                                        'expired' => $agentTickets->where('status', 'CLOSED')->where('sla_state.state', 'expired')->count(),
                                    ],
                                ];

                                $barWidth = $max > 0 ? ($total / $max) * 100 : 0;
                            @endphp

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-900 font-medium">{{ $agent->full_name }}</span>
                                    <span class="text-sm text-gray-900 font-semibold">{{ $total }} tickets</span>
                                </div>

                                <div class="w-full h-4 bg-gray-200 rounded-full overflow-hidden">
                                    <div x-data="{ width: {{ $barWidth }} }" :style="'width: ' + width + '%'"
                                        class="bg-gradient-to-r from-gray-900 to-black h-4 transition-all duration-300">
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2 text-xs text-gray-600 mt-2">
                                    <div class="px-2 py-1 bg-white shadow-sm rounded-lg border border-gray-200 flex items-center gap-1">
                                        Open: <span class="font-semibold">{{ $open }}</span>
                                        @php
                                            $ok = $slaCounts['Open']['ok'];
                                            $exp = $slaCounts['Open']['expired'];
                                        @endphp
                                        @if ($ok > 0)
                                            <span class="px-1.5 py-0.5 ml-1 text-white rounded bg-green-600 text-[10px]">{{ $ok }}</span>
                                        @endif
                                        @if ($exp > 0)
                                            <span class="px-1.5 py-0.5 ml-1 text-white rounded bg-red-600 text-[10px]">{{ $exp }}</span>
                                        @endif
                                    </div>

                                    <div class="px-2 py-1 bg-white shadow-sm rounded-lg border border-gray-200 flex items-center gap-1">
                                        In Progress: <span class="font-semibold">{{ $progress }}</span>
                                        @php
                                            $ok = $slaCounts['IN_PROGRESS']['ok'];
                                            $exp = $slaCounts['IN_PROGRESS']['expired'];
                                        @endphp
                                        @if ($ok > 0)
                                            <span class="px-1.5 py-0.5 ml-1 text-white rounded bg-green-600 text-[10px]">{{ $ok }}</span>
                                        @endif
                                        @if ($exp > 0)
                                            <span class="px-1.5 py-0.5 ml-1 text-white rounded bg-red-600 text-[10px]">{{ $exp }}</span>
                                        @endif
                                    </div>

                                    <div class="px-2 py-1 bg-white shadow-sm rounded-lg border border-gray-200 flex items-center gap-1">
                                        Resolved: <span class="font-semibold">{{ $resolved }}</span>
                                        @php
                                            $ok = $slaCounts['Resolved']['ok'];
                                            $exp = $slaCounts['Resolved']['expired'];
                                        @endphp
                                        @if ($ok > 0)
                                            <span class="px-1.5 py-0.5 ml-1 text-white rounded bg-green-600 text-[10px]">{{ $ok }}</span>
                                        @endif
                                        @if ($exp > 0)
                                            <span class="px-1.5 py-0.5 ml-1 text-white rounded bg-red-600 text-[10px]">{{ $exp }}</span>
                                        @endif
                                    </div>

                                    <div class="px-2 py-1 bg-white shadow-sm rounded-lg border border-gray-200 flex items-center gap-1">
                                        Closed: <span class="font-semibold">{{ $closed }}</span>
                                        @php
                                            $ok = $slaCounts['Closed']['ok'];
                                            $exp = $slaCounts['Closed']['expired'];
                                        @endphp
                                        @if ($ok > 0)
                                            <span class="px-1.5 py-0.5 ml-1 text-white rounded bg-green-600 text-[10px]">{{ $ok }}</span>
                                        @endif
                                        @if ($exp > 0)
                                            <span class="px-1.5 py-0.5 ml-1 text-white rounded bg-red-600 text-[10px]">{{ $exp }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- AGENT LIST --}}
        <div class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-user-group class="w-5 h-5 text-gray-700" />
                        <div>
                            <h3 class="{{ $titleC }}">Agent List</h3>
                            <p class="text-xs text-gray-500">All agents with assigned tickets</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
                        <div class="relative flex-1 sm:flex-initial">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400" />
                            </span>
                            <input type="text" wire:model.live.debounce.100ms="search"
                                placeholder="Search agent..." 
                                class="w-full sm:w-64 h-10 pl-9 pr-3 rounded-lg border border-gray-300 text-gray-800 text-sm placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition">
                        </div>

                        <button id="downloadReportBtn" wire:click="downloadReport" wire:loading.attr="disabled" wire:target="downloadReport"
                            class="{{ $btnBlk }} whitespace-nowrap">
                            <svg id="agentBtnSpinner" class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity=".25" stroke-width="4"></circle>
                                <path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="4"></path>
                            </svg>
                            <x-heroicon-o-arrow-down-tray id="agentBtnIcon" class="w-4 h-4" />
                            <span id="agentBtnLabel" class="hidden sm:inline">Download Report</span>
                        </button>
                    </div>
                </div>
            </div>

            @if(!empty($search))
                <div class="px-5 py-2 bg-white border-b border-gray-200 flex items-center gap-2">
                    <span class="text-sm text-gray-800">
                        Search:
                        <span class="px-2 py-1 bg-gray-100 rounded-lg text-gray-800 font-medium">
                            {{ $search }}
                        </span>
                    </span>
                </div>
            @endif

            <div class="p-5">
                @if($agents->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($agents as $agent)
                            <div wire:key="agent-{{ $agent->user_id }}"
                                class="cursor-pointer rounded-xl border border-gray-200 bg-white hover:shadow-md hover:border-gray-300 transition p-4"
                                wire:click="openToast('{{ $agent->user_id }}')" role="button" tabindex="0">

                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-gray-900 text-white flex items-center justify-center font-semibold text-lg shrink-0">
                                        {{ strtoupper(substr($agent->full_name, 0, 1)) }}
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-sm text-gray-900 truncate">{{ $agent->full_name }}</p>
                                        <p class="text-xs text-gray-500 truncate">
                                            {{ $agent->company_name ?? '-' }} • {{ $agent->department_name ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Assigned Tickets</p>
                                        <div class="text-xl font-bold text-gray-900">
                                            {{ $agent->tickets_count ?? ($agent->tickets?->count() ?? 0) }}
                                        </div>
                                    </div>

                                    <div class="text-right text-xs text-gray-500">
                                        <div>Open: <span class="font-semibold text-gray-700">{{ $allTicketStatsDetailed[$agent->user_id]['Open'] ?? 0 }}</span></div>
                                        <div class="mt-1">In Progress: <span class="font-semibold text-gray-700">{{ $allTicketStatsDetailed[$agent->user_id]['IN_PROGRESS'] ?? 0 }}</span></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center gap-2">
                            <x-heroicon-o-inbox class="w-12 h-12 text-gray-300" />
                            <p>No agents found.</p>
                        </div>
                    </div>
                @endif
            </div>

            @if($agents->hasPages())
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        {{ $agents->links() }}
                    </div>
                </div>
            @endif
        </div>

        {{-- MODAL --}}
        @if($openAgent)
            @php $selectedAgent = $agents->where('user_id', $openAgent)->first() ?? null; @endphp

            @if($selectedAgent)
                <div class="fixed inset-0 z-[999] flex items-center justify-center p-2 sm:p-4" aria-modal="true" role="dialog">
                    <div class="absolute inset-0 bg-black/50" wire:click="closeToast" aria-hidden="true"></div>

                    <div class="relative w-full max-w-lg md:max-w-2xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="px-5 py-4 bg-gray-900 text-white flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <h4 class="text-base font-semibold truncate">{{ $selectedAgent->full_name }}</h4>
                                <p class="text-xs text-white/80 truncate">Assigned tickets and SLA status</p>
                            </div>

                            <button wire:click="closeToast" class="p-2 rounded-md hover:bg-white/10 transition shrink-0 ml-2">
                                <x-heroicon-o-x-mark class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="max-h-[70vh] overflow-auto p-4 space-y-3 bg-gray-50">
                            @if(!empty($selectedAgent->tickets) && $selectedAgent->tickets->isNotEmpty())
                                @foreach($selectedAgent->tickets as $ticket)
                                    @php
                                        $slaData = $ticket->sla_state ?? [];
                                        $slaState = $slaData['state'] ?? null;
                                        $slaLabel = $slaData['label'] ?? null;
                                        $slaClasses = $slaData['classes'] ?? '';
                                        $hoursElapsed = $slaData['hours_elapsed'] ?? 0;
                                        $ticketStatus = ucwords(strtolower(str_replace('_', ' ', trim((string) ($ticket->status ?? '')))));
                                        $ticketPriority = ucwords(strtolower(str_replace('_', ' ', trim((string) ($ticket->priority ?? '')))));
                                    @endphp

                                    <a href="{{ url('/admin/tickets/' . $ticket->ulid) }}" class="block">
                                        <div class="p-4 bg-white rounded-lg border border-gray-200 hover:border-gray-300 hover:shadow-sm transition">
                                            <div class="flex flex-col gap-3">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div class="flex-shrink-0 px-3 py-1.5 bg-gray-50 rounded-lg text-gray-700 text-xs font-mono font-semibold">
                                                        #{{ $ticket->ticket_id }}
                                                    </div>
                                                </div>

                                                <div class="min-w-0">
                                                    <div class="font-semibold text-sm text-gray-900 line-clamp-2">
                                                        {{ $ticket->subject ?? 'No Subject' }}
                                                    </div>
                                                    
                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                        @if(!empty($ticket->status))
                                                            <span class="{{ $chip }} {{ $statusColors[$ticketStatus] ?? 'bg-gray-100 text-gray-700 ring-gray-200' }}">
                                                                <x-heroicon-o-clock class="w-3.5 h-3.5" />
                                                                <span class="capitalize">{{ $ticketStatus }}</span>
                                                            </span>
                                                        @endif

                                                        @if(!empty($ticket->priority))
                                                            <span class="{{ $chip }} {{ $priorityColors[$ticketPriority] ?? 'bg-gray-100 text-gray-700 ring-gray-200' }}">
                                                                <x-heroicon-o-flag class="w-3.5 h-3.5" />
                                                                <span class="capitalize">{{ $ticketPriority }}</span>
                                                            </span>
                                                        @endif

                                                        @if($slaState)
                                                            <span class="px-2 py-0.5 rounded {{ $slaClasses }} text-[11px] font-semibold">
                                                                {{ $slaLabel }}
                                                            </span>
                                                        @endif

                                                        @php $h = $hoursElapsed ?? 0; @endphp
                                                        @if ($h > 0)
                                                            <div class="px-2 py-0.5 bg-gray-100 rounded text-[10px] text-gray-700">
                                                                @if ($h < 1)
                                                                    {{ floor($h * 60) }}m ago
                                                                @elseif ($h < 24)
                                                                    {{ floor($h) }}h ago
                                                                @elseif ($h < 24 * 30)
                                                                    {{ floor($h / 24) }}d ago
                                                                @elseif ($h < 24 * 30 * 12)
                                                                    {{ floor($h / (24 * 30)) }}mo ago
                                                                @else
                                                                    {{ floor($h / (24 * 365)) }}y ago
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <div class="p-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <x-heroicon-o-inbox class="w-12 h-12 text-gray-300" />
                                        <p>No tickets assigned to this agent.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </main>

    <script>
        const dlBtn = document.getElementById('downloadReportBtn');
        const dlOverlay = document.getElementById('agentDownloadOverlay');
        const btnSpinner = document.getElementById('agentBtnSpinner');
        const btnIcon = document.getElementById('agentBtnIcon');
        const btnLabel = document.getElementById('agentBtnLabel');

        function setDownloading(state) {
            if (!dlBtn) return;
            if (state) {
                dlBtn.disabled = true;
                btnSpinner?.classList.remove('hidden');
                btnIcon?.classList.add('hidden');
                btnLabel && (btnLabel.textContent = 'Menyiapkan…');
                dlOverlay?.classList.remove('hidden');
            } else {
                dlBtn.disabled = false;
                btnSpinner?.classList.add('hidden');
                btnIcon?.classList.remove('hidden');
                btnLabel && (btnLabel.textContent = 'Download Report');
                dlOverlay?.classList.add('hidden');
            }
        }

        document.getElementById('agentHideOverlay')?.addEventListener('click', () => setDownloading(false));
        dlBtn?.addEventListener('click', () => setDownloading(true));

        window.addEventListener('livewire:request-end', () => setTimeout(() => setDownloading(false), 1000));
        window.addEventListener('livewire:error', () => setDownloading(false));
    </script>
</div>