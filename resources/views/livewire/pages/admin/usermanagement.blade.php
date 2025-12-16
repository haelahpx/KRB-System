<div class="bg-gray-50">
    @php
    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    $btnRed = 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition';
    $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs text-black';
    $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
    $ico = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-8">
        {{-- HERO --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-6 w-28 h-28 bg-white/20 rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-6 w-20 h-20 bg-white/10 rounded-full blur-lg"></div>
            </div>

            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">

                    <div class="flex items-start gap-4 sm:gap-6 flex-1 min-w-0">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20 shrink-0">
                            <x-heroicon-o-users class="w-6 h-6 text-white" />
                        </div>

                        <div class="space-y-1.5 min-w-0">
                            <h2 class="text-xl sm:text-2xl font-semibold leading-tight truncate">
                                User Management
                            </h2>

                            <p class="text-sm text-white/80 truncate">
                                Cabang: <span class="font-semibold">{{ $company_name }}</span>
                                <span class="mx-2">•</span>
                                Departemen: <span class="font-semibold">{{ $department_name }}</span>
                            </p>

                            <p class="text-xs text-white/60 truncate">
                                Menampilkan user untuk departemen:
                                <span class="font-medium">{{ $department_name }}</span>.
                            </p>
                        </div>
                    </div>

                    {{-- Ensured w-full on mobile, then constrained on large screens --}}
                    @if ($showSwitcher)
                    <div class="w-full lg:w-[32rem] lg:ml-6">
                        <label class="block text-xs font-medium text-white/80 mb-2">
                            Pilih Departemen
                        </label>

                        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                            <select
                                wire:model.live="selected_department_id"
                                class="w-full h-11 sm:h-12 px-3 sm:px-4 rounded-lg border border-white/20 bg-white/10 text-white text-sm placeholder:text-white/60 focus:border-white focus:ring-2 focus:ring-white/30 focus:outline-none transition">
                                <option class="text-gray-900" value="{{ auth()->user()->department_id }}">
                                    {{ auth()->user()->department->name }} (Your Primary Department)
                                </option>
                                @foreach ($departmentOptions as $opt)
                                <option class="text-gray-900" value="{{ $opt['id'] }}">
                                    {{ $opt['name'] }}{{ $opt['id'] === $primary_department_id ? ' — Primary' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @else
                    <div class="w-full lg:w-80 lg:ml-auto">
                        <input
                            type="text"
                            wire:model.live.debounce.400ms="search"
                            placeholder="Cari judul atau catatan…"
                            class="w-full rounded-lg bg-white/10 text-white border border-white/20
                                   px-3 py-2 backdrop-blur-sm focus:ring-2 focus:ring-white/30 focus:outline-none">
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- FORM CREATE --}}
        <section class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Tambah User</h3>
                <p class="text-sm text-gray-500">
                    User baru otomatis masuk ke departemen:
                    <span class="font-medium">{{ $department_name }}</span>
                </p>
            </div>

            <form class="p-5" wire:submit.prevent="store">
                {{-- Already responsive with grid-cols-1 md:grid-cols-2 --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="{{ $label }}">Full Name</label>
                        <input type="text" class="{{ $input }}" wire:model.defer="full_name" placeholder="e.g. John Doe">
                        @error('full_name') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Email Address</label>
                        <input type="email" class="{{ $input }}" wire:model.defer="email" placeholder="e.g. john@company.com">
                        @error('email') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Phone Number</label>
                        <input type="text" class="{{ $input }}" wire:model.defer="phone_number" placeholder="08123456789">
                        @error('phone_number') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Password</label>
                        <input type="password" class="{{ $input }}" wire:model.defer="password" autocomplete="new-password">
                        @error('password') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Role</label>
                        <select class="{{ $input }}" wire:model.defer="role_key">
                            <option value="">Pilih role</option>
                            @foreach ($roleOptions as $r)
                            <option value="{{ $r['key'] }}">{{ $r['name'] }}</option>
                            @endforeach
                        </select>
                        @error('role_key') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Department (Terkunci)</label>
                        <input type="text" class="{{ $input }}" value="{{ $department_name }}" readonly>
                    </div>
                </div>

                <div class="pt-5">
                    <button type="submit" wire:loading.attr="disabled" wire:target="store"
                        class="inline-flex items-center gap-2 px-5 h-10 rounded-xl bg-gray-900 text-white text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900/20 transition active:scale-95 hover:bg-black disabled:opacity-60 relative overflow-hidden"
                        wire:loading.class="opacity-80 cursor-wait">
                        <span class="flex items-center gap-2" wire:loading.remove wire:target="store">
                            <x-heroicon-o-check class="w-4 h-4" />
                            Simpan Data
                        </span>
                        <span class="flex items-center gap-2" wire:loading wire:target="store">
                            <x-heroicon-o-arrow-path class="h-4 w-4 animate-spin" />
                            Menyimpan…
                        </span>
                    </button>
                </div>
            </form>
        </section>

        {{-- LIST USERS --}}
        <div class="{{ $card }}">
            {{-- Filters (Made to stack on mobile, use full width) --}}
            <div class="px-5 py-4 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                    {{-- Search --}}
                    <div class="relative flex-1 w-full">
                        <input type="text" wire:model.live="search" placeholder="Cari nama atau email..." class="{{ $input }} pl-10 w-full placeholder:text-gray-400">
                        <x-heroicon-o-magnifying-glass
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                    </div>

                    {{-- Role filter --}}
                    <div class="relative w-full lg:w-60">
                        <select wire:model.live="roleFilter" class="{{ $input }} pl-10 w-full">
                            <option value="">Semua Role</option>
                            @foreach ($roles as $r)
                            <option value="{{ $r['id'] }}">{{ $r['name'] }}</option>
                            @endforeach
                        </select>
                        <x-heroicon-o-briefcase
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                    </div>

                    {{-- Agent filter --}}
                    <div class="relative w-full lg:w-48">
                        <select wire:model.live="agentFilter" class="{{ $input }} pl-10 w-full">
                            <option value="">Semua User</option>
                            <option value="yes">Hanya Agent</option>
                            <option value="no">Non Agent</option>
                        </select>
                        {{-- Blade icon / agent icon --}}
                        <x-heroicon-o-user-circle
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-200">
                @forelse ($users as $u)
                @php
                $roleName = strtolower($u->role->name ?? '');
                $isSelf = auth()->id() === $u->user_id;
                $canEdit = !in_array($roleName, ['admin', 'superadmin']) || $isSelf;
                $canDelete = !in_array($roleName, ['admin', 'superadmin']);
                $rowNo = (($users->firstItem() ?? 1) - 0) + $loop->index;
                @endphp

                <div class="px-5 py-5 hover:bg-gray-50 transition-colors" wire:key="user-{{ $u->user_id }}">
                    {{-- Changed lg:flex-row to sm:flex-row to stack content/actions on mobile --}}
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <div class="{{ $ico }} shrink-0">{{ strtoupper(substr($u->full_name, 0, 1)) }}</div>
                            <div class="min-w-0 flex-1">
                                {{-- Badges wrapper: Added flex-wrap --}}
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate max-w-full sm:max-w-none">{{ $u->full_name }}</h4>

                                    {{-- Role badge --}}
                                    <span class="{{ $chip }} shrink-0">
                                        <span class="font-medium text-gray-700">{{ $u->role->name ?? 'No Role' }}</span>
                                    </span>

                                    {{-- Agent badge + icon --}}
                                    @if($u->is_agent == 'yes' || $u->is_agent == 1)
                                    <span class="{{ $chip }} shrink-0">
                                        <x-heroicon-o-user class="w-3.5 h-3.5 text-gray-600" />
                                        <span class="font-medium text-gray-700">Agent</span>
                                    </span>
                                    @endif

                                    {{-- Dept badge --}}
                                    <span class="{{ $chip }} shrink-0">
                                        <span class="text-gray-500">Dept:</span>
                                        <span class="font-medium text-gray-700">{{ $u->department->department_name ?? 'N/A' }}</span>
                                    </span>

                                    @if($isSelf)
                                    <span class="{{ $chip }} shrink-0">You</span>
                                    @endif
                                </div>
                                <p class="text-[12px] text-gray-500 truncate">{{ $u->email }}</p>
                                @if($u->phone_number)
                                <p class="text-[12px] text-gray-500 mt-0.5">{{ $u->phone_number }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Actions: Added flex-wrap and adjusted alignment for mobile --}}
                        <div class="text-left sm:text-right shrink-0 space-y-2 pt-2 sm:pt-0">
                            <div class="{{ $mono }} inline-block">No. {{ $rowNo }}</div>
                            <div class="flex flex-wrap gap-2 justify-start sm:justify-end">
                                @if($canEdit)
                                <button class="{{ $btnBlk }}" wire:click="openEdit({{ $u->user_id }})"
                                    wire:loading.attr="disabled" wire:target="openEdit({{ $u->user_id }})"
                                    wire:key="btn-edit-{{ $u->user_id }}">
                                    <span class="inline-flex items-center gap-1.5"
                                        wire:loading.remove
                                        wire:target="openEdit({{ $u->user_id }})">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        Edit
                                    </span>
                                    <span class="inline-flex items-center gap-1.5"
                                        wire:loading
                                        wire:target="openEdit({{ $u->user_id }})">
                                        <x-heroicon-o-arrow-path class="w-4 h-4 animate-spin" />
                                        Loading…
                                    </span>
                                </button>
                                @else
                                <button class="{{ $btnBlk }} opacity-50 cursor-not-allowed" disabled
                                    title="Anda tidak bisa mengedit akun Admin lain">
                                    <span class="inline-flex items-center gap-1.5">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        Edit
                                    </span>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-5 py-14 text-center text-gray-500 text-sm">Tidak ada user pada departemen ini.</div>
                @endforelse
            </div>

            @if($users->hasPages())
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-center">
                    {{ $users->links() }}
                </div>
            </div>
            @endif
        </div>

        {{-- MODAL EDIT --}}
        @if($modalEdit)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4" role="dialog" aria-modal="true" {{-- Added padding p-4 --}}
            wire:key="modal-edit" wire:keydown.escape.window="closeEdit">
            <button type="button" class="absolute inset-0 bg-black/50" aria-label="Close overlay" wire:click="closeEdit"></button>

            {{-- Adjusted max-w for responsiveness --}}
            <div class="relative w-full max-w-md md:max-w-2xl mx-auto {{ $card }} focus:outline-none" tabindex="-1">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Edit User</h3>
                    <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeEdit" aria-label="Close">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <form class="p-5" wire:submit.prevent="update">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="{{ $label }}">Full Name</label>
                            <input type="text" class="{{ $input }}" wire:model.defer="edit_full_name" autofocus>
                            @error('edit_full_name') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Email Address</label>
                            <input type="email" class="{{ $input }}" wire:model.defer="edit_email">
                            @error('edit_email') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Phone Number</label>
                            <input type="text" class="{{ $input }}" wire:model.defer="edit_phone_number">
                            @error('edit_phone_number') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Password (kosongkan jika tidak diubah)</label>
                            <input type="password" class="{{ $input }}" wire:model.defer="edit_password" autocomplete="new-password">
                        </div>

                        <div>
                            <label class="{{ $label }}">Role</label>
                            <select class="{{ $input }}" wire:model.live="edit_role_key">
                                <option value="">Pilih role</option>
                                @foreach ($roleOptions as $r)
                                <option value="{{ $r['key'] }}">{{ $r['name'] }}</option>
                                @endforeach
                            </select>
                            @error('edit_role_key') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Department (Terkunci)</label>
                            <input type="text" class="{{ $input }}" value="{{ $department_name }}" readonly>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                        <button type="button"
                            class="px-4 h-10 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-100 hover:border-gray-400 transition"
                            wire:click="closeEdit">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="update"
                            class="inline-flex items-center gap-2 px-5 h-10 rounded-xl bg-gray-900 text-white text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900/20 transition active:scale-95 hover:bg-black disabled:opacity-60">
                            <span class="flex items-center gap-2" wire:loading.remove wire:target="update">
                                <x-heroicon-o-check class="w-4 h-4" />
                                Simpan Perubahan
                            </span>
                            <span class="flex items-center gap-2" wire:loading wire:target="update">
                                <x-heroicon-o-arrow-path class="h-4 w-4 animate-spin" />
                                Menyimpan…
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </main>
</div>