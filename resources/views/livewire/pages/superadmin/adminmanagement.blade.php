<div class="bg-gray-50 text-gray-900 min-h-screen">
    @php
    // Reusing compact class definitions
    $card = 'bg-white rounded-2xl border border-gray-100 shadow-md overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-xl border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk = 'px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition h-7';
    $btnRed = 'px-3 py-1.5 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition h-7';
    $chip = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-[10px] text-gray-600 font-medium';
    $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md w-fit';
    $ico = 'w-9 h-9 bg-gray-100 text-gray-900 rounded-lg flex items-center justify-center font-semibold text-xs shrink-0 border border-gray-200';
    $adminCard = 'bg-white rounded-xl border border-gray-100 shadow-sm p-4 space-y-3 hover:shadow-lg transition-shadow duration-300';
    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-8">

        {{-- HERO --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4 shrink-0">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <x-heroicon-o-user-group class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Admin Management</h2>
                            <p class="text-sm text-white/80">
                                Cabang: <span class="font-semibold">{{ $company_name }}</span>
                            </p>
                        </div>
                    </div>
                    {{-- SEARCH AND BUTTONS --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                        <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search admin..."
                            class="h-10 px-3 rounded-xl border border-white/20 bg-white/10 text-white placeholder:text-white/70 focus:bg-white/20 w-full sm:w-48 lg:w-64">
                        @if($mode === 'index')
                        <button class="px-3 py-2 text-sm font-medium rounded-xl bg-white text-gray-900 hover:bg-gray-100 w-full sm:w-auto"
                            wire:click="openCreateModal">+ New Admin</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- GRID UTAMA UNTUK LIST DAN FORM (3 KOLOM) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LIST USERS (GRID CARD - Mengambil 2 Kolom) --}}
            <div class="lg:col-span-2 space-y-5">
                
                {{-- Admin Cards Grid (Internal Grid) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- Note: Internal grid di sini maksimum 3 kolom agar tetap terlihat baik di dalam wrapper 2 kolom LG --}}

                    @forelse ($rows as $u)
                    @php $rowNo = (($rows->firstItem() ?? 1) - 0) + $loop->index; @endphp
                    
                    <div class="{{ $adminCard }}" wire:key="admin-{{ $u->user_id }}">
                        
                        {{-- Header Card: Icon, Name, and Role/Number --}}
                        <div class="flex items-start gap-3">
                            <div class="{{ $ico }}">{{ strtoupper(substr($u->full_name, 0, 1)) }}</div>
                            <div class="min-w-0 flex-1 space-y-0.5">
                                <h4 class="font-semibold text-gray-900 text-sm leading-tight truncate">
                                    {{ $u->full_name }}
                                </h4>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <span class="{{ $chip }} bg-gray-900 text-white">Admin</span>
                                    <p class="{{ $mono }}">No. {{ $rowNo }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Details (Email, Phone, Departments) --}}
                        <div class="flex flex-col space-y-1 text-xs pt-3 border-t border-gray-100">
                            {{-- Email & Phone --}}
                            <div class="flex flex-col">
                                <span class="text-gray-500 font-medium">Email:</span>
                                <span class="text-gray-700 truncate">{{ $u->email }}</span>
                            </div>
                            @if($u->phone_number)
                            <div class="flex flex-col">
                                <span class="text-gray-500 font-medium">Phone:</span>
                                <span class="text-gray-700">{{ $u->phone_number }}</span>
                            </div>
                            @endif

                            {{-- Departments --}}
                            <div class="pt-2">
                                <span class="text-gray-500 font-medium block mb-1">Departments:</span>
                                <div class="flex flex-wrap gap-1">
                                    @if($u->department)
                                    <span class="{{ $chip }} bg-blue-500 text-gray-50 font-bold">Primary: {{ $u->department->department_name }}</span>
                                    @endif

                                    @foreach($u->departments as $d)
                                    @if(!$u->department || $d->department_id !== $u->department->department_id)
                                    <span class="{{ $chip }}">{{ $d->department_name }}</span>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2 justify-end pt-3 border-t border-gray-100">
                            <button class="{{ $btnBlk }}" wire:click="openEditModal({{ $u->user_id }})">Edit</button>
                            <button class="{{ $btnRed }}" wire:click="destroy({{ $u->user_id }})"
                                onclick="return confirm('Hapus admin ini?')">Hapus</button>
                        </div>
                    </div>
                    @empty
                    <div class="sm:col-span-2 lg:col-span-3 py-10 text-center text-gray-500 text-sm bg-white rounded-xl border border-gray-200 shadow-sm">Tidak ada admin.</div>
                    @endforelse
                </div>
            
                {{-- Pagination --}}
                @if($rows->hasPages())
                <div class="pt-4">
                    <div class="flex justify-center">
                        {{ $rows->links() }}
                    </div>
                </div>
                @endif
            </div>

            {{-- FORM CREATE (Sidebar Desktop - Mengambil 1 Kolom) --}}
            <section class="hidden lg:block lg:col-span-1 {{ $card }} self-start">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">
                        Tambah Admin
                    </h3>
                </div>

                <form class="p-5" wire:submit.prevent="store">
                    <div class="grid grid-cols-1 gap-5">
                        <div>
                            <label class="{{ $label }}">Company</label>
                            <input type="text" class="{{ $input }}" value="{{ $company_name }}" readonly>
                        </div>

                        <div>
                            <label class="{{ $label }}">Primary Department</label>
                            <select class="{{ $input }}" wire:model.live="primary_department_id">
                                <option value="">— Choose primary —</option>
                                @foreach($departments as $d)
                                <option value="{{ $d['department_id'] }}">{{ $d['department_name'] }}</option>
                                @endforeach
                            </select>
                            @error('primary_department_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Additional Departments (optional)</label>
                            <div class="space-y-2 max-h-56 overflow-y-auto border rounded-lg p-3">
                                @forelse($departments as $d)
                                @continue($primary_department_id == $d['department_id'])
                                <label class="flex items-center gap-2">
                                    <input type="checkbox"
                                        value="{{ $d['department_id'] }}"
                                        wire:model.live="additional_departments" />
                                    <span>{{ $d['department_name'] }}</span>
                                </label>
                                @empty
                                <p class="text-sm text-gray-500">No departments in your company.</p>
                                @endforelse
                            </div>
                            @error('additional_departments') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

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
                            <label class="{{ $label }}">Confirm Password</label>
                            <input type="password" class="{{ $input }}" wire:model.defer="password_confirmation">
                        </div>
                    </div>

                    <div class="pt-5">
                        <button type="submit" class="inline-flex items-center gap-2 px-5 h-10 rounded-xl bg-gray-900 text-white text-sm font-medium hover:bg-black w-full justify-center sm:w-auto">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </main>

    {{-- MODAL CREATE (BOTTOM-SHEET UNTUK MOBILE) --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-0 lg:hidden" wire:keydown.escape.window="closeCreateModal">
        <button type="button" class="absolute inset-0 bg-black/50" wire:click="closeCreateModal"></button>

        <div class="absolute inset-x-0 bottom-0 bg-white rounded-t-2xl shadow-xl max-h-[90vh] overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between shrink-0">
                <h3 class="text-base font-semibold text-gray-900">Tambah Admin</h3>
                <button class="text-gray-500 hover:text-gray-700" wire:click="closeCreateModal" aria-label="Close">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <form class="p-5 overflow-y-auto flex-1" wire:submit.prevent="store">
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="{{ $label }}">Company</label>
                        <input type="text" class="{{ $input }}" value="{{ $company_name }}" readonly>
                    </div>

                    <div>
                        <label class="{{ $label }}">Primary Department</label>
                        <select class="{{ $input }}" wire:model.live="primary_department_id">
                            <option value="">— Choose primary —</option>
                            @foreach($departments as $d)
                            <option value="{{ $d['department_id'] }}">{{ $d['department_name'] }}</option>
                            @endforeach
                        </select>
                        @error('primary_department_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Additional Departments (optional)</label>
                        <div class="space-y-2 max-h-56 overflow-y-auto border rounded-lg p-3">
                            @forelse($departments as $d)
                            @continue($primary_department_id == $d['department_id'])
                            <label class="flex items-center gap-2">
                                <input type="checkbox"
                                    value="{{ $d['department_id'] }}"
                                    wire:model.live="additional_departments" />
                                <span>{{ $d['department_name'] }}</span>
                            </label>
                            @empty
                            <p class="text-sm text-gray-500">No departments in your company.</p>
                            @endforelse
                        </div>
                        @error('additional_departments') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

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
                        <label class="{{ $label }}">Confirm Password</label>
                        <input type="password" class="{{ $input }}" wire:model.defer="password_confirmation">
                    </div>
                </div>
            </form>
            <div class="px-5 py-4 border-t border-gray-200 shrink-0">
                <button type="submit" wire:click="store" class="inline-flex items-center gap-2 px-5 h-10 rounded-xl bg-gray-900 text-white text-sm font-medium hover:bg-black w-full justify-center">
                    Simpan Data
                </button>
            </div>
        </div>
    </div>
    @endif
    {{-- /MODAL CREATE --}}

    {{-- MODAL EDIT (BOTTOM-SHEET DI MOBILE, CENTERING DI DESKTOP) --}}
    @if($showEditModal)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-0 lg:p-4" wire:keydown.escape.window="closeEditModal">
        <button type="button" class="absolute inset-0 bg-black/50" wire:click="closeEditModal"></button>

        <div class="relative w-full max-w-2xl {{ $card }} lg:mx-4 max-h-[90vh] overflow-hidden flex flex-col
            lg:relative lg:rounded-2xl lg:shadow-xl
            absolute inset-x-0 bottom-0 rounded-t-2xl shadow-xl">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between shrink-0">
                <h3 class="text-base font-semibold text-gray-900">Edit Admin</h3>
                <button class="text-gray-500 hover:text-gray-700" wire:click="closeEditModal" aria-label="Close">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <form class="p-5 overflow-y-auto flex-1" wire:submit.prevent="update">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Full Name --}}
                    <div>
                        <label class="{{ $label }}">Full Name</label>
                        <input type="text" class="{{ $input }}" wire:model.defer="full_name">
                        @error('full_name') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="{{ $label }}">Email Address</label>
                        <input type="email" class="{{ $input }}" wire:model.defer="email">
                        @error('email') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="{{ $label }}">Phone Number</label>
                        <input type="text" class="{{ $input }}" wire:model.defer="phone_number">
                        @error('phone_number') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Employee ID</label>
                        <input type="text" class="{{ $input }}" wire:model.defer="employee_id">
                        @error('employee_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- New Password (optional) --}}
                    <div>
                        <label class="{{ $label }}">Password (kosongkan jika tidak diubah)</label>
                        <input type="password" class="{{ $input }}" wire:model.defer="password" autocomplete="new-password">
                        @error('password') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Primary Department --}}
                    <div>
                        <label class="{{ $label }}">Primary Department</label>
                        <select class="{{ $input }}" wire:model.live="primary_department_id">
                            <option value="">— Pilih primary —</option>
                            @foreach($departments as $d)
                            <option value="{{ $d['department_id'] }}">{{ $d['department_name'] }}</option>
                            @endforeach
                        </select>
                        @error('primary_department_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Additional Departments (grid 2 kolom, no limit) --}}
                    <div>
                        <label class="{{ $label }}">Additional Departments (optional)</label>
                        <div class="border rounded-lg p-3 max-h-60 overflow-y-auto">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @forelse($departments as $d)
                                @continue($primary_department_id == $d['department_id'])
                                <label class="flex items-center gap-2">
                                    <input type="checkbox"
                                        value="{{ $d['department_id'] }}"
                                        wire:model.live="additional_departments" />
                                    <span class="truncate">{{ $d['department_name'] }}</span>
                                </label>
                                @empty
                                <p class="text-sm text-gray-500 col-span-2">No departments in your company.</p>
                                @endforelse
                            </div>
                        </div>
                        @error('additional_departments') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>
            </form>

            <div class="px-5 py-4 border-t border-gray-200 shrink-0">
                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
                    <button type="button"
                        class="px-4 h-10 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-100 w-full sm:w-auto"
                        wire:click="closeEditModal">
                        Batal
                    </button>
                    <button type="submit" wire:click="update" wire:loading.attr="disabled" wire:target="update"
                        class="inline-flex items-center justify-center gap-2 px-5 h-10 rounded-xl bg-gray-900 text-white text-sm font-medium hover:bg-black disabled:opacity-60 w-full sm:w-auto">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    {{-- /MODAL EDIT --}}

</div>