<div class="bg-gray-50 min-h-screen">
    {{-- Style Variables (Adjusted for compact card design) --}}
    @php
        $card = 'bg-white rounded-2xl border border-gray-100 shadow-md overflow-hidden';
        $label = 'block text-sm font-medium text-gray-700 mb-2';
        $input = 'w-full h-10 px-3 rounded-xl border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
        $btnBlk = 'px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition h-7'; // Tombol lebih pendek (h-7)
        $btnRed = 'px-3 py-1.5 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition h-7'; // Tombol lebih pendek (h-7)
        $btnLite = 'px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900/10 disabled:opacity-60 transition h-7'; // Tombol lebih pendek (h-7)
        $chip = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-[10px] text-gray-600 font-medium'; // Chip lebih kecil
        $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md w-fit'; // Mono lebih kecil
        $ico = 'w-9 h-9 bg-gray-100 text-gray-900 rounded-lg flex items-center justify-center font-semibold text-xs shrink-0 border border-gray-200'; // Ikon lebih kecil
        $storageCard = 'bg-white rounded-xl border border-gray-100 shadow-sm p-4 space-y-3 hover:shadow-lg transition-shadow duration-300';
    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-8">
        {{-- HERO --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <x-heroicon-o-bars-3 class="w-6 h-6 text-white" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg sm:text-xl font-semibold">Manage Storage</h2>
                        <p class="text-sm text-white/80">
                            Cabang: <span
                                class="font-semibold">{{ optional(Auth::user()->company)->company_name ?? '-' }}</span>
                        </p>
                    </div>
                    <a href="{{ route('superadmin.managerequirements') }}" class="px-4 h-10 rounded-xl border border-white/20 text-white text-sm font-medium hover:bg-white/10 transition">Go to Requirements</a>
                </div>
            </div>
        </div>

        {{-- FORM + LIST CARD CONTAINER --}}
        <section class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <h3 class="text-base font-semibold text-gray-900 shrink-0">Add New Storage</h3>
                <div class="w-full sm:w-72 relative">
                    <input type="text" wire:model.live.debounce.400ms="search" class="{{ $input }} pl-10 shadow-sm"
                        placeholder="Search by code or name…">
                    <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                </div>
            </div>

            {{-- Create Form --}}
            <form class="p-5" wire:submit.prevent="create">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                    <div class="md:col-span-1">
                        <label class="{{ $label }}">Company</label>
                        <input type="text" class="{{ $input }}"
                               value="{{ optional(Auth::user()->company)->company_name ?? '-' }}" readonly>
                    </div>

                    <div class="md:col-span-1">
                        <label class="{{ $label }}">Code</label>
                        <input type="text" wire:model.defer="code" class="{{ $input }}" placeholder="e.g. RACK-A01">
                        @error('code') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-1">
                        <label class="{{ $label }}">Name</label>
                        <input type="text" wire:model.defer="name" class="{{ $input }}" placeholder="e.g. Rak A01">
                        @error('name') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-3 pt-6 md:col-span-1">
                        <input id="is_active" type="checkbox" wire:model.defer="is_active"
                            class="h-4 w-4 rounded border-gray-300">
                        <label for="is_active" class="text-sm text-gray-700">Active</label>
                        @error('is_active') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-5">
                    <button type="submit" class="{{ $btnBlk }} inline-flex items-center gap-2 h-10 px-5 rounded-xl text-sm font-medium" wire:loading.attr="disabled"
                        wire:target="create">
                        <span wire:loading.remove wire:target="create">Save Storage</span>
                        <span class="inline-flex items-center gap-2" wire:loading wire:target="create">
                            <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" />
                            Saving…
                        </span>
                    </button>
                </div>
            </form>

            {{-- LIST (Card Grid) --}}
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse ($rows as $row)
                        @php $rowNo = ($rows->currentPage() - 1) * $rows->perPage() + $loop->iteration; @endphp
                        
                        <div class="{{ $storageCard }}" wire:key="row-{{ $row->storage_id }}">
                            
                            {{-- Header: Name, Code, No --}}
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="{{ $ico }}">
                                        <x-heroicon-o-bars-3 class="w-4 h-4 text-gray-900" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-semibold text-gray-900 text-sm leading-snug truncate">{{ $row->name }}</h4>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="{{ $chip }} bg-gray-200 text-gray-900 font-semibold">{{ $row->code }}</span>
                                            <p class="{{ $mono }}">No. {{ $rowNo }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Core Details (Status, Dates) --}}
                            <div class="flex flex-col space-y-1 text-xs pt-3 border-t border-gray-100">
                                
                                <div class="flex items-center justify-between text-gray-500">
                                    <span class="font-medium">Status:</span>
                                    @if ($row->is_active)
                                        <span class="{{ $chip }} bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            Active
                                        </span>
                                    @else
                                        <span class="{{ $chip }} bg-gray-100 text-gray-600 border border-gray-200">
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between text-gray-500">
                                    <span class="font-medium">Created:</span>
                                    <span class="{{ $chip }} bg-white text-gray-700 border border-gray-200">
                                        {{ $row->created_at?->format('d M Y, H:i') }}
                                    </span>
                                </div>

                            </div>
                            
                            {{-- Action Buttons & Trash Status --}}
                            <div class="flex gap-2 justify-end pt-3 border-t border-gray-100">
                                @if($row->deleted_at)
                                    <span class="{{ $chip }} bg-rose-100 text-rose-700 mr-auto">
                                        <span class="w-1.5 h-1.5 bg-rose-500 rounded-full"></span>
                                        Trashed
                                    </span>
                                @endif

                                @if(!$row->deleted_at)
                                    <button type="button" class="{{ $btnBlk }}"
                                        wire:click="openEdit({{ $row->storage_id }})" wire:loading.attr="disabled"
                                        wire:target="openEdit({{ $row->storage_id }})">
                                        <span wire:loading.remove wire:target="openEdit({{ $row->storage_id }})">Edit</span>
                                        <span wire:loading wire:target="openEdit({{ $row->storage_id }})">Loading…</span>
                                    </button>
                                    <button type="button" class="{{ $btnRed }}" wire:click="delete({{ $row->storage_id }})"
                                        onclick="return confirm('Move to trash?')" wire:loading.attr="disabled"
                                        wire:target="delete({{ $row->storage_id }})">
                                        <span wire:loading.remove wire:target="delete({{ $row->storage_id }})">Delete</span>
                                        <span wire:loading wire:target="delete({{ $row->storage_id }})">Deleting…</span>
                                    </button>
                                @else
                                    <button type="button" class="{{ $btnLite }}"
                                        wire:click="restore({{ $row->storage_id }})" wire:loading.attr="disabled"
                                        wire:target="restore({{ $row->storage_id }})">
                                        Restore
                                    </button>
                                    <button type="button" class="{{ $btnRed }}"
                                        wire:click="forceDelete({{ $row->storage_id }})"
                                        onclick="return confirm('Permanently delete?')" wire:loading.attr="disabled"
                                        wire:target="forceDelete({{ $row->storage_id }})">
                                        Delete Permanently
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-14 text-center text-gray-500 text-sm bg-white rounded-xl border border-gray-200 shadow-sm">No storage found.</div>
                    @endforelse
                </div>
            </div>

            @if($rows->hasPages())
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                    <div class="flex justify-center">
                        {{ $rows->links() }}
                    </div>
                </div>
            @endif
        </section>

        {{-- Edit Modal --}}
        @if($modalEdit)
            <div class="fixed inset-0 z-[60] flex items-center justify-center" role="dialog" aria-modal="true"
                wire:key="edit-modal" wire:keydown.escape.window="$set('modalEdit', false)">
                <button type="button" class="absolute inset-0 bg-black/50" aria-label="Close overlay"
                    wire:click="$set('modalEdit', false)"></button>
                <div class="relative w-full max-w-xl mx-4 {{ $card }} focus:outline-none" tabindex="-1">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">Edit Storage</h3>
                        <button class="text-gray-500 hover:text-gray-700" type="button"
                            wire:click="$set('modalEdit', false)" aria-label="Close">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <form class="p-5" wire:submit.prevent="update">
                        <div class="space-y-5">
                            <div>
                                <label class="{{ $label }}">Code</label>
                                <input type="text" wire:model.defer="edit_code" class="{{ $input }}" autofocus>
                                @error('edit_code') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="{{ $label }}">Name</label>
                                <input type="text" wire:model.defer="edit_name" class="{{ $input }}">
                                @error('edit_name') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex items-center gap-3 pt-2">
                                <input id="edit_is_active" type="checkbox" wire:model.defer="edit_is_active"
                                    class="h-4 w-4 rounded border-gray-300">
                                <label for="edit_is_active" class="text-sm text-gray-700">Active</label>
                                @error('edit_is_active') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>


                        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                            <button type="button" class="{{ $btnLite }} h-10 px-4"
                                wire:click="$set('modalEdit', false)">Cancel</button>
                            <button type="submit" class="{{ $btnBlk }} inline-flex items-center gap-2 h-10 px-4" wire:loading.attr="disabled" wire:target="update">
                                <span wire:loading.remove wire:target="update">Save Changes</span>
                                <span class="inline-flex items-center gap-2" wire:loading wire:target="update">
                                    <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" />
                                    Saving…
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </main>
</div>