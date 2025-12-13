<div class="bg-gray-50 min-h-screen">
    @php
    $card = 'bg-white rounded-2xl border border-gray-100 shadow-md overflow-hidden'; // Diperbarui
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-xl border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition'; // Diperbarui
    $btnBlk = 'px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition h-7'; // Tombol lebih pendek (h-7)
    $btnRed = 'px-3 py-1.5 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition h-7'; // Tombol lebih pendek (h-7)
    $chip = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] text-gray-600 font-medium'; // Diperbarui untuk chip
    $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md w-fit'; // Diperbarui
    $ico = 'w-9 h-9 bg-gray-100 text-gray-900 rounded-lg flex items-center justify-center font-semibold text-xs shrink-0 border border-gray-200'; // Ikon lebih kecil
    $wifiCard = 'bg-white rounded-xl border border-gray-100 shadow-sm p-4 space-y-3 hover:shadow-lg transition-shadow duration-300';
    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-8">

        {{-- HERO SECTION --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <x-heroicon-o-bars-3 class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">Wifi Management</h2>
                        <p class="text-sm text-white/80">
                            Cabang: <span
                                class="font-semibold">{{ optional(Auth::user()->company)->company_name ?? '-' }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM CREATE --}}
        <section class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Tambah WiFi Baru</h3>
                <p class="text-sm text-gray-500">
                    Informasi WiFi ini akan terlihat oleh karyawan internal.
                </p>
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

                <div class="pt-5">
                    <button type="submit" wire:loading.attr="disabled" wire:target="store"
                        class="inline-flex items-center gap-2 px-5 h-10 rounded-xl bg-gray-900 text-white text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900/20 transition active:scale-95 hover:bg-black disabled:opacity-60 relative overflow-hidden"
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
        </section>

        {{-- LIST WIFI (CARD GRID) --}}
        <div class="space-y-5">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-sm font-semibold text-gray-900">Daftar WiFi</h3>
                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded border border-gray-200 shadow-sm">
                    Total: {{ $wifis->total() }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse ($wifis as $wifi)
                @php
                $rowNo = (($wifis->currentPage() - 1) * $wifis->perPage()) + $loop->iteration;
                // Custom classes for password display
                $passDisplay = 'flex items-center gap-1.5 px-2 py-1 rounded border text-xs font-mono select-all cursor-text';
                @endphp

                <div class="{{ $wifiCard }}" wire:key="wifi-{{ $wifi->wifi_id }}">
                    
                    {{-- Header: Icon, SSID, No --}}
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="{{ $ico }}">
                                <x-heroicon-o-signal class="w-4 h-4 text-gray-900" />
                            </div>
                            <h4 class="font-semibold text-gray-900 text-sm truncate min-w-0">
                                {{ $wifi->ssid }}
                            </h4>
                        </div>
                        <p class="{{ $mono }}">No. {{ $rowNo }}</p>
                    </div>

                    {{-- Details: Location & Status --}}
                    <div class="flex flex-col space-y-1 pt-3 border-t border-gray-100 text-xs">
                        <div class="flex items-center justify-between text-gray-500">
                            <span class="font-medium">Lokasi:</span>
                            @if($wifi->location)
                            <span class="{{ $chip }} bg-blue-50 text-blue-700 border border-blue-100 w-fit">
                                <x-heroicon-o-map-pin class="w-3 h-3" />
                                {{ $wifi->location }}
                            </span>
                            @else
                            <span class="{{ $chip }} bg-gray-100 text-gray-500 w-fit">N/A</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between text-gray-500">
                            <span class="font-medium">Status:</span>
                            @if($wifi->is_active)
                            <span class="{{ $chip }} bg-emerald-50 text-emerald-700 border border-emerald-100 w-fit">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Active
                            </span>
                            @else
                            <span class="{{ $chip }} bg-gray-100 text-gray-600 border border-gray-200 w-fit">
                                Inactive
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="pt-3 border-t border-gray-100">
                        <span class="text-gray-500 font-medium block text-xs mb-1">Password:</span>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-2 max-w-full overflow-x-auto">
                             <span class="text-xs font-mono text-gray-600 select-all cursor-text whitespace-nowrap">{{ $wifi->password }}</span>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-2 justify-end pt-3 border-t border-gray-100">
                        <button class="{{ $btnBlk }}" wire:click="openEdit({{ $wifi->wifi_id }})"
                            wire:loading.attr="disabled" wire:target="openEdit({{ $wifi->wifi_id }})">
                            <span class="inline-flex items-center gap-1.5" wire:loading.remove wire:target="openEdit({{ $wifi->wifi_id }})">
                                <x-heroicon-o-pencil-square class="w-3 h-3" />
                                Edit
                            </span>
                            <span class="inline-flex items-center gap-1.5" wire:loading wire:target="openEdit({{ $wifi->wifi_id }})">
                                <x-heroicon-o-arrow-path class="w-3 h-3 animate-spin" />
                                Loading
                            </span>
                        </button>

                        <button class="{{ $btnRed }}" wire:click="delete({{ $wifi->wifi_id }})"
                            onclick="confirm('Apakah Anda yakin ingin menghapus WiFi {{ $wifi->ssid }}?') || event.stopImmediatePropagation()"
                            wire:loading.attr="disabled" wire:target="delete({{ $wifi->wifi_id }})">
                            <span class="flex items-center gap-1.5">
                                <x-heroicon-o-trash class="w-3 h-3" />
                                Hapus
                            </span>
                        </button>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-10 text-center bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="mx-auto h-12 w-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                        <x-heroicon-o-wifi class="w-6 h-6 text-gray-300" />
                    </div>
                    <h3 class="text-sm font-medium text-gray-900">Belum ada data WiFi</h3>
                    <p class="mt-1 text-xs text-gray-500 max-w-sm mx-auto">
                        Mulai tambahkan data WiFi untuk perusahaan ini.
                    </p>
                </div>
                @endforelse
            </div>

            @if($wifis->hasPages())
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl shadow-sm">
                <div class="flex justify-center">
                    {{ $wifis->links() }}
                </div>
            </div>
            @endif
        </div>

        {{-- MODAL EDIT --}}
        @if($modalEdit)
        <div class="fixed inset-0 z-[60] flex items-center justify-center" role="dialog" aria-modal="true"
            wire:key="modal-edit" wire:keydown.escape.window="closeEdit">

            <button type="button" class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" aria-label="Close overlay" wire:click="closeEdit"></button>

            <div class="relative w-full max-w-2xl mx-4 {{ $card }} shadow-2xl transform transition-all focus:outline-none z-10" tabindex="-1">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Edit WiFi</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Perbarui informasi jaringan WiFi.</p>
                    </div>
                    <button class="text-gray-400 hover:text-gray-700 transition p-1 rounded-md hover:bg-gray-100" type="button" wire:click="closeEdit" aria-label="Close">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <form class="p-6" wire:submit.prevent="update">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="{{ $label }}">SSID Name</label>
                            <input type="text" class="{{ $input }}" wire:model.defer="edit_ssid">
                            @error('edit_ssid') <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1"><x-heroicon-s-exclamation-circle class="w-3 h-3" /> {{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Password</label>
                            <div class="relative">
                                <input type="text" class="{{ $input }} pr-10" wire:model.defer="edit_password">
                                <x-heroicon-o-key class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            </div>
                            @error('edit_password') <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1"><x-heroicon-s-exclamation-circle class="w-3 h-3" /> {{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Location</label>
                            <input type="text" class="{{ $input }}" wire:model.defer="edit_location">
                            @error('edit_location') <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1"><x-heroicon-s-exclamation-circle class="w-3 h-3" /> {{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Status</label>
                            <select class="{{ $input }}" wire:model.defer="edit_is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('edit_is_active') <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1"><x-heroicon-s-exclamation-circle class="w-3 h-3" /> {{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 pt-5 -mx-6 -mb-6 bg-gray-50/50 px-6 pb-6 rounded-b-2xl">
                        <button type="button"
                            class="px-4 h-10 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-white hover:border-gray-400 focus:ring-2 focus:ring-gray-200 transition shadow-sm bg-white"
                            wire:click="closeEdit">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="update"
                            class="inline-flex items-center gap-2 px-5 h-10 rounded-xl bg-gray-900 text-white text-sm font-medium shadow-sm hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 transition disabled:opacity-60">
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