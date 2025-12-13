<div class="bg-gray-50 min-h-screen">
    @php
    use Carbon\Carbon;
    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input =
    'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk =
    'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 transition';
    $btnLt = 'px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900/10 disabled:opacity-60 transition';
    $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';

    $initials = $initials;

    $ticket = $this->ticket;
    $attachments = $ticket->attachments;
    $comments = $ticket->comments;
    $assignments = $ticket->assignments;

    $priority = strtolower($ticket->priority ?? '');
    $status = strtolower($ticket->status ?? 'open');
    $agentsAssigned = $assignments->pluck('user.full_name')->filter()->unique()->values();
    $hasAgent = $agentsAssigned->isNotEmpty();
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
                            <h2 class="text-lg sm:text-xl font-semibold">Ticket #{{ $ticket->ticket_id ?? 'Detail' }}</h2>
                            <p class="text-sm text-white/80">
                                Cabang: <span
                                    class="font-semibold">{{ optional(Auth::user()->company)->company_name ?? '-' }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 ml-auto mt-4 md:mt-0">
                        <a href="{{ route('superadmin.ticketsupport') }}" class="{{ $btnLt }} border-white/30 text-white/90 hover:bg-white/10">Back to Ticket List</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="space-y-6 lg:col-span-1 lg:order-2">

                <div class="{{ $card }}">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Ticket Summary</h3>
                    </div>

                    <div class="p-5 space-y-3 text-sm">
                        <div class="group flex items-center gap-3 p-3 rounded-lg border border-gray-200 bg-white shadow-sm hover:border-indigo-200 transition-colors">
                            <span class="flex-shrink-0 p-2 rounded-full bg-indigo-50 text-indigo-600">
                                <x-heroicon-o-user class="w-5 h-5" />
                            </span>
                            <div class="flex flex-col overflow-hidden min-w-0">
                                <span class="text-xs text-gray-400 font-medium mb-0.5">Requested By</span>
                                <span class="font-semibold text-gray-900 truncate">{{ $ticket->user->full_name ?? 'Unknown User' }}</span>
                                <span class="text-[10px] text-gray-500 truncate flex items-center gap-1 mt-0.5">
                                    {{ $ticket->requesterDepartment->department_name ?? 'No Dept' }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-3">
                            <div class="flex flex-wrap items-center justify-between p-2.5 rounded-md bg-gray-50 border border-gray-100">
                                <span class="text-xs font-medium text-gray-500">Department</span>
                                <span class="text-xs font-semibold text-gray-700 text-right">
                                    {{ $ticket->department->department_name ?? '-' }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center justify-between p-2.5 rounded-md bg-gray-50 border border-gray-100">
                                <span class="text-xs font-medium text-gray-500">Assigned Agent</span>
                                @if($hasAgent)
                                <div class="flex flex-col text-right items-end gap-0.5">
                                    <x-heroicon-o-check-circle class="w-4 h-4 text-emerald-500 hidden sm:block" />
                                    <span class="text-xs font-semibold text-gray-900">{{ $agentsAssigned->implode(', ') }}</span>
                                </div>
                                @else
                                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wide text-right">
                                    Pending
                                </span>
                                @endif
                            </div>

                            <div class="pt-2 border-t border-gray-100 text-xs text-gray-500 space-y-1">
                                <div class="flex justify-between">
                                    <span>Created:</span>
                                    <span class="font-medium text-gray-700 text-right">{{ optional($ticket->created_at)->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Last Update:</span>
                                    <span class="font-medium text-gray-700 text-right">{{ optional($ticket->updated_at)->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="{{ $card }}">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-900">Attachments ({{ $attachments->count() }})</h3>
                    </div>
                    <div class="p-5 space-y-3 text-sm">
                        @php
                        $okImg = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'];
                        $atts = $attachments
                        ->map(function ($a) use ($okImg) {
                        $url = (string) ($a->file_url ?? '');
                        $name = (string) ($a->original_filename ?? '');
                        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        return (object) ['url' => $url, 'name' => $name ?: basename($url) ?: 'attachment', 'ext' => $ext,];
                        })
                        ->filter(fn($x) => $x->url);
                        $images = $atts->filter(fn($x) => in_array($x->ext, $okImg, true))->values();
                        $others = $atts->reject(fn($x) => in_array($x->ext, $okImg, true))->values();
                        @endphp

                        @if ($atts->isEmpty())
                        <div class="text-gray-500">No attachments found.</div>
                        @else
                        @if ($images->isNotEmpty())
                        <div class="space-y-2">
                            <p class="text-xs font-medium text-gray-500">Images (Click to Preview)</p>
                            <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-4 lg:grid-cols-3 xl:grid-cols-4 gap-2 mb-3">
                                @foreach ($images as $img)
                                <button wire:click="openPreview('{{ $img->url }}')" class="block focus:outline-none">
                                    <div class="relative aspect-square overflow-hidden rounded-lg border border-gray-200 bg-white hover:scale-105 transition">
                                        <img src="{{ $img->url }}" alt="{{ $img->name }}" class="absolute inset-0 w-full h-full object-cover" />
                                    </div>
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if ($others->isNotEmpty())
                        <div class="space-y-2">
                            <p class="text-xs font-medium text-gray-500 pt-2 border-t border-dashed border-gray-100">Other Files</p>
                            @foreach ($others as $f)
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 transition">
                                <div class="w-10 h-10 rounded-md bg-gray-900 text-white flex items-center justify-center shrink-0 text-[10px] font-bold uppercase">
                                    {{ $f->ext ?: 'FILE' }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-medium text-gray-900" title="{{ $f->name }}">
                                        {{ $f->name }}
                                    </div>
                                </div>
                                <div class="shrink-0 flex items-center gap-2">
                                    <a href="{{ $f->url }}" target="_blank" class="px-3 py-1.5 text-xs font-medium rounded-md border border-gray-300 hover:bg-gray-100 text-gray-700 transition">Open</a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @endif
                    </div>
                </div>

            </div>
            <section class="lg:col-span-2 lg:order-1 space-y-6">

                <div class="{{ $card }}">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">Ticket Info</h2>
                    </div>
                    <form wire:submit.prevent="save" class="p-5 space-y-4">
                        <div>
                            <label class="{{ $label }}">Subject</label>
                            <div class="w-full p-3 rounded-lg border border-gray-200 bg-gray-50 text-gray-800 font-medium">
                                {{ $ticket->subject }}
                            </div>
                        </div>

                        <div>
                            <label class="{{ $label }}">Description</label>
                            <div class="p-3 rounded-lg border border-gray-200 bg-gray-50 text-gray-800 whitespace-pre-line min-h-20">
                                {{ $ticket->description }}
                            </div>
                        </div>

                        <div>
                            <label class="{{ $label }}">Priority</label>
                            <div class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-800 capitalize font-medium">
                                {{ $ticket->priority }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                            <div>
                                <label class="{{ $label }}">Status</label>
                                <select wire:model.defer="status" class="{{ $input }}">
                                    <option value="OPEN">Open</option>
                                    <option value="IN_PROGRESS" @disabled(!$this->agent_id)>In Progress</option>
                                    <option value="RESOLVED" @disabled(!$this->agent_id)>Resolved</option>
                                    <option value="CLOSED" @disabled(!$this->agent_id)>Closed</option>
                                </select>
                                @error('status')
                                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                                @enderror

                                @if (!$this->agent_id)
                                <p class="mt-1 text-[11px] text-gray-500">
                                    Assign agent untuk mengubah status ke In Progress/Resolved/Closed.
                                </p>
                                @endif
                            </div>

                            <div>
                                <label class="{{ $label }}">Assigned Agent</label>
                                <select wire:model.defer="agent_id" class="{{ $input }}">
                                    <option value="">— Unassigned —</option>
                                    @foreach ($this->agents as $a)
                                    <option value="{{ $a->user_id }}">{{ $a->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('agent_id')
                                <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit" class="{{ $btnBlk }}" wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading.remove wire:target="save">Save Changes</span>
                                <span class="inline-flex items-center gap-2" wire:loading wire:target="save">
                                    <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" /> Saving...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="{{ $card }}">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-900">Discussion 💬</h3>
                    </div>

                    <div class="p-5 space-y-6">
                        <form wire:submit.prevent="addComment" class="mb-2">
                            <div class="flex items-start gap-3 sm:gap-4">
                                <div class="flex-shrink-0">
                                    @php $meInitials = $initials(auth()->user()->full_name ?? auth()->user()->name ?? 'User'); @endphp
                                    <span
                                        class="inline-flex h-10 w-10 rounded-full bg-gray-900 text-white items-center justify-center text-xs font-bold">
                                        {{ $meInitials }}
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <textarea wire:model.defer="newComment" rows="3"
                                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 transition"
                                        placeholder="Tulis komentar..."></textarea>
                                    @error('newComment')
                                    <div class="text-rose-600 text-xs mt-1">{{ $message }}</div>
                                    @enderror

                                    <div class="mt-3 flex items-center justify-end">
                                        <button type="submit" class="{{ $btnBlk }}" wire:loading.attr="disabled" wire:target="addComment">
                                            <span wire:loading.remove wire:target="addComment">Post Comment</span>
                                            <span class="inline-flex items-center gap-2" wire:loading wire:target="addComment">
                                                <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" /> Posting...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="space-y-5">
                            @forelse ($comments as $comment)
                            @php
                            $isMine = $comment->user_id === auth()->id();
                            $name = $comment->user->full_name ?? ($comment->user->name ?? 'User');
                            $init = $initials($name);
                            @endphp

                            <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }} items-start gap-2 sm:gap-3">
                                
                                <div class="max-w-[90%] sm:max-w-[70%] md:max-w-md"> 
                                    <div
                                        class="flex items-center {{ $isMine ? 'justify-end' : 'justify-start' }} gap-3">
                                        <p class="text-xs font-semibold text-gray-700 truncate {{ $isMine ? 'order-2' : 'order-1' }}">{{ $name }}</p>
                                        <p class="text-[11px] text-gray-500 {{ $isMine ? 'order-1' : 'order-2' }}"
                                            title="{{ optional($comment->created_at)->format('Y-m-d H:i') }}">
                                            {{ optional($comment->created_at)->diffForHumans() }}
                                        </p>
                                    </div>

                                    <div
                                        class="mt-1 rounded-xl
                                                    {{ $isMine ? 'bg-gray-900 text-white border border-gray-900' : 'bg-gray-50 text-gray-900 border border-gray-200' }}
                                                    px-4 py-3 shadow-sm w-auto"> 
                                        <p class="text-sm whitespace-pre-wrap leading-relaxed">
                                            {{ $comment->comment_text }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex-shrink-0 {{ $isMine ? 'order-3' : 'order-1' }}">
                                    <span
                                        class="inline-flex h-9 w-9 rounded-full
                                                {{ $isMine ? 'bg-gray-900 text-white' : 'bg-gray-200 text-gray-800' }}
                                                items-center justify-center text-[11px] font-bold">
                                        {{ $init }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <div class="rounded-lg border border-dashed border-gray-300 p-8 text-center text-gray-600">
                                Belum ada komentar.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>
            </div>

        @if ($this->previewUrl)
        <div class="fixed inset-0 bg-black/80 flex items-center justify-center z-[100]" wire:click="closePreview" wire:keydown.escape.window="closePreview">
            <div class="relative p-4" @click.stop>
                <img src="{{ $this->previewUrl }}" class="max-h-[85vh] max-w-[95vw] sm:max-h-[90vh] sm:max-w-[90vw] rounded-lg shadow-lg" />
                <button wire:click="closePreview"
                    class="absolute top-6 right-6 sm:top-2 sm:right-2 bg-white/80 hover:bg-white text-gray-900 px-2 py-1 rounded-full text-xs font-semibold">✕</button>
            </div>
        </div>
        @endif
    </main>
</div>