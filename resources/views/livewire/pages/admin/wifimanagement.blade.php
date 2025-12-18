{{-- resources/views/livewire/pages/admin/wifi-management.blade.php --}}
<div class="bg-gray-50 min-h-screen" wire:key="wifi-management-page">
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
                            <x-heroicon-o-wifi class="w-6 h-6 text-white" />
                        </div>

                        <div class="space-y-1.5">
                            <h2 class="text-xl sm:text-2xl font-semibold leading-tight">
                                WiFi Management
                            </h2>

                            <div class="text-sm text-white/80 flex flex-col sm:block">
                                <span>Perusahaan: <span class="font-semibold">{{ $company_name }}</span></span>
                                <span class="hidden sm:inline mx-2">•</span>
                                <span>Departemen: <span class="font-semibold">{{ $department_name }}</span></span>
                            </div>

                            <p class="text-xs text-white/60 pt-1 sm:pt-0">
                                Kelola akses internet (SSID & Password) untuk area operasional.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM CREATE --}}
        <div class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-plus-circle class="w-5 h-5 text-gray-700" />
                    <div>
                        <h3 class="{{ $titleC }}">Tambah WiFi Baru</h3>
                        <p class="text-xs text-gray-500">
                            Informasi WiFi ini akan terlihat oleh karyawan internal.
                        </p>
                    </div>
                </div>
            </div>

            <form class="p-5" wire:submit.prevent="store">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="{{ $label }}">SSID Name</label>
                        <input type="text" class="{{ $input }}" wire:model.defer="ssid" placeholder="Contoh: KRB_GUEST_LOBBY">
                        @error('ssid') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Password</label>
                        <div class="relative">
                            <input type="text" class="{{ $input }} pr-10" wire:model.defer="password" placeholder="Masukkan password wifi">
                            <x-heroicon-o-lock-closed class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        </div>
                        @error('password') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Location (Area)</label>
                        <input type="text" class="{{ $input }}" wire:model.defer="location" placeholder="Contoh: Lobby Utama, Ruang Meeting">
                        @error('location') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Status</label>
                        <select class="{{ $input }}" wire:model.defer="is_active">
                            <option value="1">Active (Aktif)</option>
                            <option value="0">Inactive (Non-aktif)</option>
                        </select>
                        @error('is_active') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-5 flex justify-end border-t border-gray-100 mt-5">
                    <button type="submit" wire:loading.attr="disabled" wire:target="store"
                        class="{{ $btnBlk }} gap-2"
                        wire:loading.class="opacity-80 cursor-wait">
                        <span class="flex items-center gap-2" wire:loading.remove wire:target="store">
                            <x-heroicon-o-check class="w-4 h-4" />
                            Simpan WiFi
                        </span>
                        <span class="flex items-center gap-2" wire:loading wire:target="store">
                            <x-heroicon-o-arrow-path class="h-4 w-4 animate-spin" />
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- FILTER SECTION --}}
        <div class="p-5 {{ $card }}">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <x-heroicon-o-funnel class="w-5 h-5 inline-block mr-1 align-text-bottom text-gray-500" />
                Filter WiFi
            </h3>

            <div class="grid grid-cols-1 gap-4">
                {{-- SEARCH INPUT --}}
                <div>
                    <label class="{{ $label }}">Cari WiFi</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400" />
                        </span>
                        <input
                            type="text"
                            wire:model.live.debounce.400ms="search"
                            class="{{ $input }} pl-9"
                            placeholder="Cari SSID atau Lokasi…">
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE SECTION --}}
        <div class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-wifi class="w-5 h-5 text-gray-700" />
                    <div>
                        <h3 class="{{ $titleC }}">Daftar WiFi</h3>
                        <p class="text-xs text-gray-500">Semua jaringan WiFi yang tersedia.</p>
                    </div>
                </div>
                <span class="{{ $mono }}">Total: {{ $wifis->total() }}</span>
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden md:block overflow-x-auto">
                @if($wifis->count())
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-3">#</th>
                            <th scope="col" class="px-6 py-3">SSID</th>
                            <th scope="col" class="px-6 py-3">Password</th>
                            <th scope="col" class="px-6 py-3">Location</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wifis as $wifi)
                        @php
                        $rowNumber = $wifis->firstItem() + $loop->index;
                        @endphp
                        <tr wire:key="wifi-desktop-{{ $wifi->wifi_id }}" class="bg-white border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <span class="{{ $mono }}">#{{ $rowNumber }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 line-clamp-1">
                                            {{ $wifi->ssid }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 px-2 py-1 rounded bg-gray-100 border border-gray-200 w-fit">
                                    <x-heroicon-o-key class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                                    <span class="text-xs font-mono text-gray-600 select-all cursor-text">{{ $wifi->password }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($wifi->location)
                                <span class="{{ $chip }} bg-blue-50 text-blue-700 ring-blue-200">
                                    <x-heroicon-o-map-pin class="w-3.5 h-3.5" />
                                    <span>{{ $wifi->location }}</span>
                                </span>
                                @else
                                <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($wifi->is_active)
                                <span class="{{ $chip }} bg-emerald-50 text-emerald-700 ring-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Active</span>
                                </span>
                                @else
                                <span class="{{ $chip }} bg-gray-100 text-gray-600 ring-gray-200">
                                    <span>Inactive</span>
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <button class="{{ $btnBlk }}" wire:click="openEdit({{ $wifi->wifi_id }})"
                                        wire:loading.attr="disabled" wire:target="openEdit({{ $wifi->wifi_id }})"
                                        wire:key="btn-edit-{{ $wifi->wifi_id }}">
                                        <span class="inline-flex items-center gap-1.5"
                                            wire:loading.remove
                                            wire:target="openEdit({{ $wifi->wifi_id }})">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                            Edit
                                        </span>
                                        <span class="inline-flex items-center gap-1.5"
                                            wire:loading
                                            wire:target="openEdit({{ $wifi->wifi_id }})">
                                            <x-heroicon-o-arrow-path class="w-4 h-4 animate-spin" />
                                            Loading
                                        </span>
                                    </button>

                                    <button class="{{ $btnRed }}" wire:click="delete({{ $wifi->wifi_id }})"
                                        onclick="confirm('Apakah Anda yakin ingin menghapus WiFi {{ $wifi->ssid }}?') || event.stopImmediatePropagation()"
                                        wire:loading.attr="disabled" wire:target="delete({{ $wifi->wifi_id }})">
                                        <span class="flex items-center gap-1.5">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                            Hapus
                                        </span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="px-6 py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center gap-2">
                        <x-heroicon-o-wifi class="w-12 h-12 text-gray-300" />
                        <h3 class="text-sm font-medium text-gray-900">Belum ada data WiFi</h3>
                        <p class="text-sm text-gray-500">Data WiFi yang Anda tambahkan akan muncul di sini.</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Mobile Table View --}}
            <div class="md:hidden">
                @if($wifis->count())
                <table class="w-full text-sm">
                    <tbody>
                        @foreach ($wifis as $wifi)
                        @php
                        $rowNumber = $wifis->firstItem() + $loop->index;
                        @endphp
                        <tr wire:key="wifi-mobile-{{ $wifi->wifi_id }}" class="bg-white border-b">
                            <td class="p-4">
                                <div class="space-y-3">
                                    {{-- Row Number & Status --}}
                                    <div class="flex items-center justify-between">
                                        <span class="{{ $mono }}">#{{ $rowNumber }}</span>
                                        @if($wifi->is_active)
                                        <span class="{{ $chip }} bg-emerald-50 text-emerald-700 ring-emerald-200 text-[10px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Active</span>
                                        </span>
                                        @else
                                        <span class="{{ $chip }} bg-gray-100 text-gray-600 ring-gray-200 text-[10px]">
                                            <span>Inactive</span>
                                        </span>
                                        @endif
                                    </div>

                                    {{-- SSID Name --}}
                                    <div class="text-gray-900">
                                        <div class="font-semibold text-base flex items-center gap-2">
                                            <x-heroicon-o-wifi class="w-4 h-4 text-gray-400" />
                                            {{ $wifi->ssid }}
                                        </div>
                                    </div>

                                    {{-- Password --}}
                                    <div>
                                        <div class="text-xs font-medium text-gray-500 mb-1.5">Password:</div>
                                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 border border-gray-200">
                                            <x-heroicon-o-key class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                                            <span class="text-xs font-mono text-gray-700 select-all cursor-text break-all">{{ $wifi->password }}</span>
                                        </div>
                                    </div>

                                    {{-- Location --}}
                                    @if($wifi->location)
                                    <div class="text-xs text-gray-700">
                                        <div class="flex items-center gap-1.5">
                                            <x-heroicon-o-map-pin class="w-3.5 h-3.5 text-gray-400" />
                                            <span class="font-medium">{{ $wifi->location }}</span>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Action Buttons --}}
                                    <div class="pt-2 space-y-2">
                                        <button class="{{ $btnBlk }} w-full justify-center" 
                                            wire:click="openEdit({{ $wifi->wifi_id }})"
                                            wire:loading.attr="disabled" 
                                            wire:target="openEdit({{ $wifi->wifi_id }})"
                                            wire:key="btn-edit-mobile-{{ $wifi->wifi_id }}">
                                            <span class="inline-flex items-center gap-1.5"
                                                wire:loading.remove
                                                wire:target="openEdit({{ $wifi->wifi_id }})">
                                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                                                Edit WiFi
                                            </span>
                                            <span class="inline-flex items-center gap-1.5"
                                                wire:loading
                                                wire:target="openEdit({{ $wifi->wifi_id }})">
                                                <x-heroicon-o-arrow-path class="w-4 h-4 animate-spin" />
                                                Loading
                                            </span>
                                        </button>

                                        <button class="{{ $btnRed }} w-full justify-center" 
                                            wire:click="delete({{ $wifi->wifi_id }})"
                                            onclick="confirm('Apakah Anda yakin ingin menghapus WiFi {{ $wifi->ssid }}?') || event.stopImmediatePropagation()"
                                            wire:loading.attr="disabled" 
                                            wire:target="delete({{ $wifi->wifi_id }})">
                                            <span class="flex items-center gap-1.5">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                                Hapus WiFi
                                            </span>
                                        </button>
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
                        <x-heroicon-o-wifi class="w-12 h-12 text-gray-300" />
                        <h3 class="text-sm font-medium text-gray-900">Belum ada data WiFi</h3>
                        <p class="text-sm text-gray-500">Data WiFi yang Anda tambahkan akan muncul di sini.</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Pagination --}}
            @if($wifis->hasPages())
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-center">
                    {{ $wifis->links() }}
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
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Edit WiFi</h3>
                            <p class="text-xs text-gray-500">Perbarui informasi jaringan WiFi.</p>
                        </div>
                    </div>
                    <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeEdit" aria-label="Close">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                {{-- Modal Body --}}
                <form class="p-5" wire:submit.prevent="update">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="{{ $label }}">SSID Name</label>
                            <input type="text" class="{{ $input }}" wire:model.defer="edit_ssid" autofocus>
                            @error('edit_ssid') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Password</label>
                            <div class="relative">
                                <input type="text" class="{{ $input }} pr-10" wire:model.defer="edit_password">
                                <x-heroicon-o-key class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            </div>
                            @error('edit_password') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Location</label>
                            <input type="text" class="{{ $input }}" wire:model.defer="edit_location">
                            @error('edit_location') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Status</label>
                            <select class="{{ $input }}" wire:model.defer="edit_is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('edit_is_active') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
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
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </main>
</div>