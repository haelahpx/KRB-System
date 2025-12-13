<div class="bg-gray-50 min-h-screen">
    @php
    use Carbon\Carbon;
    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk= 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    $btnRed= 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition';
    $btnLt = 'px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900/10 disabled:opacity-60 transition';
    $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-[11px]';
    $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
    $ico = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';

    $statusColors = [
    'OPEN' => 'bg-emerald-50 text-emerald-700 border-emerald-300',
    'IN_PROGRESS' => 'bg-yellow-50 text-yellow-700 border-yellow-300',
    'RESOLVED' => 'bg-blue-50 text-blue-700 border-blue-300',
    'CLOSED' => 'bg-gray-100 text-gray-600 border-gray-300',
    ];

    $deptOptionsFormatted = collect($deptLookup)->map(fn($name, $id) => ['id' => $id, 'label' => $name])->values()->all();
    $priorityOptions = ['low', 'medium', 'high', 'urgent'];

    $initials = function (?string $fullName): string {
    $fullName = trim($fullName ?? '');
    if ($fullName === '') return 'US';
    $parts = preg_split('/\s+/', $fullName);
    $first = strtoupper(mb_substr($parts[0] ?? 'U', 0, 1));
    $last = strtoupper(mb_substr($parts[count($parts)-1] ?? $parts[0] ?? 'S', 0, 1));
    return $first.$last;
    };
    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-8">

        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center border border-white/20">
                            <x-heroicon-o-calendar-days class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Ticket Support Management</h2>
                            <p class="text-sm text-white/80">
                                Cabang: <span class="font-semibold">{{ optional(Auth::user()->company)->company_name ?? '-' }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 ml-auto">
                        <label class="inline-flex items-center gap-2 text-sm text-white/90">
                            <input type="checkbox"
                                wire:model.live="showDeleted"
                                class="rounded border-white/30 bg-white/10 focus:ring-white/40">
                            <span>Show Deleted</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="lg:hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="flex-1">
                    <p class="text-xs text-gray-600 font-medium">
                        @if($departmentFilter)
                        Dept: {{ $deptLookup[$departmentFilter] ?? '—' }}
                        @elseif($priorityFilter)
                        Priority: {{ ucfirst($priorityFilter) }}
                        @else
                        Showing All Tickets
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
        <div class="grid grid-cols-1 gap-6">

            <section class="space-y-4">
                <div class="{{ $card }}">
                    <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200 hidden lg:block">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                            <div class="md:col-span-2"> <label class="{{ $label }}">Search Subject/User</label>
                                <div class="relative">
                                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search subject, description or user..."
                                        class="{{ $input }} pl-10 w-full placeholder:text-gray-400">
                                    <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                </div>
                            </div>

                            <div> <label class="{{ $label }}">Filter by Department</label>
                                <select wire:model.live="departmentFilter" class="{{ $input }}">
                                    <option value="">— All Departments —</option>
                                    @foreach ($deptLookup as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="{{ $label }}">Filter by Priority</label>
                                <div class="flex items-center gap-2 h-10">
                                    <select wire:model.live="priorityFilter" class="{{ $input }} flex-1 !mb-0">
                                        <option value="">— All Priorities —</option>
                                        @foreach($priorityOptions as $p)
                                        <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                                        @endforeach
                                    </select>
                                    @if($departmentFilter || $priorityFilter || $search)
                                    <button type="button"
                                        title="Clear Filters"
                                        wire:click="clearFilters"
                                        class="w-10 h-10 flex items-center justify-center rounded-lg border border-rose-300 text-rose-600 hover:bg-rose-50 transition shrink-0">
                                        <x-heroicon-o-x-mark class="w-5 h-5" />
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 sm:px-6 py-5">
                        @if($tickets->isEmpty())
                        <div class="py-14 text-center text-gray-500 text-sm">No tickets found matching your criteria.</div>
                        @else
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            @foreach ($tickets as $t)
                            @php
                            $rowNo = (($tickets->firstItem() ?? 1) + $loop->index);
                            $p = strtolower($t->priority ?? 'low');
                            $priorityBadge = match($p) {
                            'urgent' => 'bg-rose-600 text-white',
                            'high' => 'bg-amber-500 text-white',
                            'medium' => 'bg-yellow-100 text-gray-800',
                            default => 'bg-gray-100 text-gray-800',
                            };

                            $statusKey = strtoupper(str_replace(' ','_', $t->status ?? 'OPEN'));
                            $statusBadgeClass = $statusColors[$statusKey] ?? 'bg-gray-100 text-gray-600 border-gray-300';
                            @endphp
                            <div class="bg-white border border-gray-200 rounded-xl p-4 sm:px-5 sm:py-4 hover:shadow-sm hover:border-gray-300 transition {{ $t->deleted_at ? 'opacity-50' : '' }}" wire:key="ticket-{{ $t->ticket_id }}">
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <div class="flex items-start gap-3 flex-1 min-w-0">
                                        <div class="{{ $ico }} hidden sm:flex">
                                            {{ strtoupper(substr($t->subject ?? 'T',0,1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-2 mb-1">
                                                <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate flex-1">
                                                    {{ $t->subject }}
                                                </h4>
                                                <div class="{{ $mono }} sm:hidden shrink-0">No. {{ $rowNo }}</div>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                <span class="text-[11px] px-2 py-0.5 rounded-md font-medium {{ $priorityBadge }}">{{ ucfirst($p) }}</span>

                                                <span class="text-[11px] px-2 py-0.5 rounded-md font-medium border {{ $statusBadgeClass }}">
                                                    {{ ucfirst(str_replace('_',' ',$t->status)) }}
                                                </span>

                                                @if($t->deleted_at)
                                                <span class="text-[11px] px-2 py-0.5 rounded-md bg-rose-100 text-rose-800">Deleted</span>
                                                @endif
                                            </div>

                                            <p class="text-[12px] text-gray-500">
                                                By: <span class="font-medium text-gray-700 truncate">{{ $t->user->full_name ?? '—' }}</span>
                                            </p>

                                            <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $t->description }}</p>

                                            <div class="flex flex-wrap items-center gap-2 mt-3 text-xs">
                                                <span class="{{ $chip }}"><span class="text-gray-500">Dept:</span><span class="font-medium text-gray-700 truncate">{{ $t->department->department_name ?? ($deptLookup[$t->department_id] ?? '—') }}</span></span>
                                                @if($t->attachments && $t->attachments->count())
                                                <span class="{{ $chip }} bg-gray-200 text-gray-600">
                                                    <x-heroicon-o-paper-clip class="w-3 h-3" />
                                                    {{ $t->attachments->count() }}
                                                </span>
                                                @endif
                                                <span class="{{ $chip }}"><span class="text-gray-500">Created:</span><span class="font-medium text-gray-700">{{ optional($t->created_at)->format('d M Y H:i') }}</span></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col justify-end items-end shrink-0 pt-1">
                                        
                                        <div class="hidden sm:flex justify-end w-full mb-2">
                                            <div class="h-8 {{ $mono }} flex items-center justify-center !py-0 !px-2.5 w-full">No. {{ $rowNo }}</div>
                                        </div>
                                        
                                        <div class="flex flex-col gap-2 pt-1 w-full">

                                            <button class="{{ $btnLt }} w-full"
                                                wire:click="redirectToTicketDetails({{ (int) $t->ticket_id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="redirectToTicketDetails({{ (int) $t->ticket_id }})">
                                                <span wire:loading.remove wire:target="redirectToTicketDetails({{ (int) $t->ticket_id }})">Open</span>
                                                <span wire:loading wire:target="redirectToTicketDetails({{ (int) $t->ticket_id }})">...</span>
                                            </button>

                                            <button class="{{ $btnBlk }} w-full"
                                                wire:click="openEdit({{ (int) $t->ticket_id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="openEdit({{ (int) $t->ticket_id }})">
                                                <span wire:loading.remove wire:target="openEdit({{ (int) $t->ticket_id }})">Edit</span>
                                                <span wire:loading wire:target="openEdit({{ (int) $t->ticket_id }})">...</span>
                                            </button>

                                            @if(!$t->deleted_at)
                                            <button class="{{ $btnRed }} w-full"
                                                wire:click="delete({{ (int) $t->ticket_id }})"
                                                onclick="return confirm('Soft delete this ticket (move to trash)?')"
                                                wire:loading.attr="disabled"
                                                wire:target="delete({{ (int) $t->ticket_id }})">
                                                <span wire:loading.remove wire:target="delete({{ (int) $t->ticket_id }})">Delete</span>
                                                <span wire:loading wire:target="delete({{ (int) $t->ticket_id }})">...</span>
                                            </button>
                                            @else
                                            <button class="{{ $btnBlk }} !bg-emerald-600 hover:!bg-emerald-700 w-full"
                                                wire:click="restore({{ (int) $t->ticket_id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="restore({{ (int) $t->ticket_id }})">
                                                <span wire:loading.remove wire:target="restore({{ (int) $t->ticket_id }})">Restore</span>
                                                <span wire:loading wire:target="restore({{ (int) $t->ticket_id }})">...</span>
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

                    @if($tickets->hasPages())
                    <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-center">
                            {{ $tickets->withQueryString()->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </section>
        </div>

        @if($showFilterModal)
        <div class="fixed inset-0 z-[60] lg:hidden">
            <div class="absolute inset-0 bg-black/50" wire:click="closeFilterModal"></div>
            <div class="absolute inset-x-0 bottom-0 bg-white rounded-t-2xl shadow-xl max-h-[90vh] overflow-hidden flex flex-col md:inset-y-0 md:bottom-auto md:top-auto md:max-w-md md:mx-auto md:rounded-b-2xl md:translate-y-0">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between shrink-0">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Filter Settings</h3>
                        <p class="text-[11px] text-gray-500">Filter daftar ticket berdasarkan departemen atau prioritas.</p>
                    </div>
                    <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeFilterModal" aria-label="Close">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-4 space-y-5 overflow-y-auto flex-1">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-800 mb-2 border-b pb-1">Search Subject/User</h4>
                        <div class="relative">
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search subject, description or user..."
                                class="{{ $input }} pl-10 w-full placeholder:text-gray-400">
                            <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-800 mb-2 border-b pb-1">Filter by Department</h4>

                        <button type="button"
                            wire:click="$set('departmentFilter', '')"
                            class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs font-medium
                                        {{ !$departmentFilter ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                            <span>All Departments</span>
                            @if(!$departmentFilter)
                            <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                            @endif
                        </button>

                        <div class="mt-2 space-y-1.5">
                            @foreach($deptOptionsFormatted as $d)
                            @php $active = (string) $departmentFilter === (string) $d['id']; @endphp
                            <button type="button"
                                wire:click="$set('departmentFilter', '{{ $d['id'] }}')"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs
                                                {{ $active ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                                <span class="truncate">{{ $d['label'] }}</span>
                                @if($active)
                                <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                                @endif
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-800 mb-2 border-b pb-1">Filter by Priority</h4>

                        <button type="button"
                            wire:click="$set('priorityFilter', '')"
                            class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs font-medium
                                        {{ !$priorityFilter ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                            <span>All Priorities</span>
                            @if(!$priorityFilter)
                            <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                            @endif
                        </button>

                        <div class="mt-2 space-y-1.5">
                            @foreach($priorityOptions as $p)
                            @php $active = $priorityFilter === $p; @endphp
                            <button type="button"
                                wire:click="$set('priorityFilter', '{{ $p }}')"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs
                                                {{ $active ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                                <span class="truncate">{{ ucfirst($p) }}</span>
                                @if($active)
                                <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                                @endif
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

        @if($modal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true" wire:key="modal-ticket" wire:keydown.escape.window="closeModal">
            <button type="button" class="absolute inset-0 bg-black/50" aria-label="Close overlay" wire:click="closeModal"></button>

            <div class="relative w-full max-w-lg mx-auto {{ $card }} focus:outline-none" tabindex="-1">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Edit Ticket #{{ $editingTicketId }}</h3>
                    <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeModal" aria-label="Close">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <form class="p-5" wire:submit.prevent="update">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
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

                        <div>
                            <label class="{{ $label }}">Priority</label>
                            <select class="{{ $input }}" wire:model.defer="priority">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                            @error('priority') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="{{ $label }}">Subject</label>
                            <input type="text" class="{{ $input }}" wire:model.defer="subject">
                            @error('subject') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="{{ $label }}">Description</label>
                            <textarea class="{{ $input }} h-28" wire:model.defer="description"></textarea>
                            @error('description') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Status</label>
                            <select class="{{ $input }}" wire:model.defer="status">
                                <option value="OPEN">Open</option>
                                <option value="IN_PROGRESS">In Progress</option>
                                <option value="RESOLVED">Resolved</option>
                                <option value="CLOSED">Closed</option>
                            </select>
                            @error('status') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                        <button type="button" class="{{ $btnLt }}" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="{{ $btnBlk }}" wire:loading.attr="disabled" wire:target="update">
                            <span wire:loading.remove wire:target="update">Update</span>
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