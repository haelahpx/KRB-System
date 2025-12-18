{{-- resources/views/livewire/pages/admin/user-management.blade.php --}}
<div class="bg-gray-50 min-h-screen" wire:key="user-management-page">
    @php
    // LAYOUT HELPERS
    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';

    // BUTTONS
    $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition inline-flex items-center justify-center';
    $btnLt = 'px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300/40 disabled:opacity-60 transition inline-flex items-center justify-center';
    $btnRed = 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition inline-flex items-center justify-center';

    // BADGES & ICONS
    $chip = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium ring-1 ring-inset';
    $chipInfo = 'bg-gray-100 text-gray-700 ring-gray-200';
    $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
    $ico = 'w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center text-white font-semibold text-sm shrink-0';
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
                            <x-heroicon-o-users class="w-6 h-6 text-white" />
                        </div>

                        <div class="space-y-1.5">
                            <h2 class="text-xl sm:text-2xl font-semibold leading-tight">
                                User Management
                            </h2>

                            <div class="text-sm text-white/80 flex flex-col sm:block">
                                <span>Cabang: <span class="font-semibold">{{ $company_name }}</span></span>
                                <span class="hidden sm:inline mx-2">•</span>
                                <span>Departemen: <span class="font-semibold">{{ $department_name }}</span></span>
                            </div>

                            <p class="text-xs text-white/60 pt-1 sm:pt-0">
                                Menampilkan user untuk departemen:
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
                            @foreach ($departmentOptions as $opt)
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

        {{-- FORM CREATE --}}
        <div class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-user-plus class="w-5 h-5 text-gray-700" />
                    <div>
                        <h3 class="{{ $titleC }}">Tambah User</h3>
                        <p class="text-xs text-gray-500">
                            User baru otomatis masuk ke departemen:
                            <span class="font-medium">{{ $department_name }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <form class="p-5" wire:submit.prevent="store">
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
                        <label class="{{ $label }}">Employee ID</label>
                        <input type="text" class="{{ $input }}" wire:model.defer="employee_id" placeholder="e.g. EMP-00123">
                        @error('employee_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
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

                <div class="pt-5 flex justify-end border-t border-gray-100 mt-5">
                    <button type="submit" wire:loading.attr="disabled" wire:target="store"
                        class="{{ $btnBlk }} gap-2"
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
        </div>

        {{-- FILTER SECTION --}}
        <div class="p-5 {{ $card }}">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <x-heroicon-o-funnel class="w-5 h-5 inline-block mr-1 align-text-bottom text-gray-500" />
                Filter User
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- SEARCH INPUT --}}
                <div>
                    <label class="{{ $label }}">Cari User</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400" />
                        </span>
                        <input
                            type="text"
                            wire:model.live.debounce.400ms="search"
                            class="{{ $input }} pl-9"
                            placeholder="Cari nama atau email…">
                    </div>
                </div>

                {{-- ROLE FILTER --}}
                <div>
                    <label class="{{ $label }}">Filter Role</label>
                    <select wire:model.live="roleFilter" class="{{ $input }}">
                        <option value="">Semua Role</option>
                        @foreach ($roles as $r)
                        <option value="{{ $r['id'] }}">{{ $r['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- AGENT FILTER --}}
                <div>
                    <label class="{{ $label }}">Filter Agent</label>
                    <select wire:model.live="agentFilter" class="{{ $input }}">
                        <option value="">Semua User</option>
                        <option value="yes">Hanya Agent</option>
                        <option value="no">Non Agent</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- TABLE SECTION --}}
        <div class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-user-group class="w-5 h-5 text-gray-700" />
                    <div>
                        <h3 class="{{ $titleC }}">Daftar User</h3>
                        <p class="text-xs text-gray-500">Semua user pada departemen ini.</p>
                    </div>
                </div>
                <span class="{{ $mono }}">Total: {{ $users->total() }}</span>
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden md:block overflow-x-auto">
                @if($users->count())
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-3">#</th>
                            <th scope="col" class="px-6 py-3">User</th>
                            <th scope="col" class="px-6 py-3">Email</th>
                            <th scope="col" class="px-6 py-3">Phone</th>
                            <th scope="col" class="px-6 py-3">Role</th>
                            <th scope="col" class="px-6 py-3">Department</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                        @php
                        $roleName = strtolower($u->role->name ?? '');
                        $isSelf = auth()->id() === $u->user_id;
                        $canEdit = !in_array($roleName, ['admin', 'superadmin']) || $isSelf;
                        $rowNumber = $users->firstItem() + $loop->index;
                        @endphp
                        <tr wire:key="user-desktop-{{ $u->user_id }}" class="bg-white border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <span class="{{ $mono }}">#{{ $rowNumber }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 line-clamp-1">
                                            {{ $u->full_name }}
                                        </p>
                                        <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">
                                            ID: {{ $u->employee_id ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                {{ $u->email }}
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                {{ $u->phone_number ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="{{ $chip }} {{ $chipInfo }}">
                                    <x-heroicon-o-briefcase class="w-3.5 h-3.5" />
                                    <span>{{ $u->role->name ?? 'No Role' }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                {{ $u->department->department_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @if($u->is_agent == 'yes' || $u->is_agent == 1)
                                    <span class="{{ $chip }} bg-blue-50 text-blue-700 ring-blue-200">
                                        <x-heroicon-o-user class="w-3.5 h-3.5" />
                                        <span>Agent</span>
                                    </span>
                                    @endif
                                    @if($isSelf)
                                    <span class="{{ $chip }} bg-emerald-50 text-emerald-700 ring-emerald-200">
                                        <span>You</span>
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
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
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="px-6 py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center gap-2">
                        <x-heroicon-o-inbox class="w-12 h-12 text-gray-300" />
                        <p>Tidak ada user ditemukan.</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Mobile Table View --}}
            <div class="md:hidden">
                @if($users->count())
                <table class="w-full text-sm">
                    <tbody>
                        @foreach ($users as $u)
                        @php
                        $roleName = strtolower($u->role->name ?? '');
                        $isSelf = auth()->id() === $u->user_id;
                        $canEdit = !in_array($roleName, ['admin', 'superadmin']) || $isSelf;
                        $rowNumber = $users->firstItem() + $loop->index;
                        @endphp
                        <tr wire:key="user-mobile-{{ $u->user_id }}" class="bg-white border-b">
                            <td class="p-4">
                                <div class="space-y-3">
                                    {{-- Row Number & Role Badge --}}
                                    <div class="flex items-center justify-between">
                                        <span class="{{ $mono }}">#{{ $rowNumber }}</span>
                                        <span class="{{ $chip }} {{ $chipInfo }} text-[10px]">
                                            <x-heroicon-o-briefcase class="w-3 h-3" />
                                            <span>{{ $u->role->name ?? 'No Role' }}</span>
                                        </span>
                                    </div>

                                    {{-- User Name & Employee ID --}}
                                    <div class="text-gray-900">
                                        <div class="font-semibold text-base">{{ $u->full_name }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            ID: {{ $u->employee_id ?? 'N/A' }}
                                        </div>
                                    </div>

                                    {{-- Status Badges --}}
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($u->is_agent == 'yes' || $u->is_agent == 1)
                                        <span class="{{ $chip }} bg-blue-50 text-blue-700 ring-blue-200 text-[10px]">
                                            <x-heroicon-o-user class="w-3 h-3" />
                                            <span>Agent</span>
                                        </span>
                                        @endif
                                        @if($isSelf)
                                        <span class="{{ $chip }} bg-emerald-50 text-emerald-700 ring-emerald-200 text-[10px]">
                                            <span>You</span>
                                        </span>
                                        @endif
                                    </div>

                                    {{-- Email --}}
                                    <div class="text-xs text-gray-700">
                                        <div class="flex items-center gap-1.5">
                                            <x-heroicon-o-envelope class="w-3.5 h-3.5 text-gray-400" />
                                            <span class="font-medium break-all">{{ $u->email }}</span>
                                        </div>
                                    </div>

                                    {{-- Phone --}}
                                    <div class="text-xs text-gray-700">
                                        <div class="flex items-center gap-1.5">
                                            <x-heroicon-o-phone class="w-3.5 h-3.5 text-gray-400" />
                                            <span class="font-medium">{{ $u->phone_number ?? '—' }}</span>
                                        </div>
                                    </div>

                                    {{-- Department --}}
                                    <div class="text-xs text-gray-700">
                                        <div class="flex items-center gap-1.5">
                                            <x-heroicon-o-building-office class="w-3.5 h-3.5 text-gray-400" />
                                            <span class="font-medium">{{ $u->department->department_name ?? 'N/A' }}</span>
                                        </div>
                                    </div>

                                    {{-- Action Button --}}
                                    <div class="pt-2">
                                        @if($canEdit)
                                        <button class="{{ $btnBlk }} w-full justify-center" 
                                            wire:click="openEdit({{ $u->user_id }})"
                                            wire:loading.attr="disabled" 
                                            wire:target="openEdit({{ $u->user_id }})"
                                            wire:key="btn-edit-mobile-{{ $u->user_id }}">
                                            <span class="inline-flex items-center gap-1.5"
                                                wire:loading.remove
                                                wire:target="openEdit({{ $u->user_id }})">
                                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                                                Edit User
                                            </span>
                                            <span class="inline-flex items-center gap-1.5"
                                                wire:loading
                                                wire:target="openEdit({{ $u->user_id }})">
                                                <x-heroicon-o-arrow-path class="w-4 h-4 animate-spin" />
                                                Loading…
                                            </span>
                                        </button>
                                        @else
                                        <button class="{{ $btnBlk }} w-full justify-center opacity-50 cursor-not-allowed" disabled
                                            title="Anda tidak bisa mengedit akun Admin lain">
                                            <span class="inline-flex items-center gap-1.5">
                                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                                                Edit User
                                            </span>
                                        </button>
                                        @endif
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
                        <p>Tidak ada user ditemukan.</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Pagination --}}
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
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4" role="dialog" aria-modal="true"
            wire:key="modal-edit" wire:keydown.escape.window="closeEdit">
            <button type="button" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" aria-label="Close overlay" wire:click="closeEdit"></button>

            <div class="relative w-full max-w-md md:max-w-2xl mx-auto bg-white rounded-2xl border border-gray-200 shadow-xl overflow-hidden focus:outline-none transform transition-all" tabindex="-1">
                {{-- Modal Header --}}
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-pencil-square class="w-5 h-5 text-gray-700" />
                        <h3 class="text-base font-semibold text-gray-900">Edit User</h3>
                    </div>
                    <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeEdit" aria-label="Close">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                {{-- Modal Body --}}
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
                            <label class="{{ $label }}">Employee ID</label>
                            <input type="text" class="{{ $input }}" wire:model.defer="edit_employee_id">
                            @error('edit_employee_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
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

                    {{-- Modal Footer --}}
                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                        <button type="button"
                            class="{{ $btnLt }}"
                            wire:click="closeEdit">
                            <x-heroicon-o-x-mark class="w-4 h-4 mr-1" />
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="update"
                            class="{{ $btnBlk }} gap-2">
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