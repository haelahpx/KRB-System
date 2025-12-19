<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="bg-[#0a0a0a] rounded-xl shadow-sm border-2 border-black p-4 md:p-6 mb-4 md:mb-6" wire:ignore>
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">

            {{-- Title --}}
            <h1 class="text-xl md:text-2xl font-bold text-white text-center lg:text-left whitespace-nowrap">
                Sistem Pemesanan Kendaraan
            </h1>

            {{-- Navigation Tabs --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <div class="flex rounded-lg overflow-hidden bg-gray-100 border border-gray-200 w-full lg:w-auto">

                    {{-- Book Vehicle Tab --}}
                    <a href="{{ route('book-vehicle') }}" wire:navigate
                        class="flex-1 lg:flex-none px-3 md:px-4 py-2 text-sm font-medium text-center
                {{ request()->routeIs('book-vehicle') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
                        Pesan Kendaraan
                    </a>

                    {{-- Vehicle Status Tab --}}
                    <a href="{{ route('vehiclestatus') }}" wire:navigate
                        class="flex-1 lg:flex-none px-3 md:px-4 py-2 text-sm font-medium text-center
                {{ request()->routeIs('vehiclestatus') ? 'bg-gray-900 text-white cursor-default' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
                        Status Kendaraan
                    </a>

                </div>

            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- LEFT: Booking Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border-2 border-black p-4 md:p-5">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">
                    @if($booking)
                    Unggah Foto - Pemesanan #{{ $booking->vehiclebooking_id }}
                    @else
                    Pesan Kendaraan
                    @endif
                </h2>

                @if(session()->has('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800 flex items-center gap-2">
                    {{ session('success') }}
                </div>
                @endif
                @if(session()->has('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800 flex items-center gap-2">
                    {{ session('error') }}
                </div>
                @endif

                @if($booking)
                {{-- Upload Mode (QUEUE SYSTEM) --}}
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-6 text-sm space-y-2">
                    <div class="flex justify-between"><span class="text-gray-500">ID Pemesanan:</span><span class="font-bold">#{{ $booking->vehiclebooking_id }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Kendaraan:</span><span class="font-bold">{{ $booking->vehicle->name ?? 'N/A' }}</span></div>
                </div>

                <form wire:submit.prevent="handlePhotoUpload" enctype="multipart/form-data" class="space-y-5">

                    {{-- AREA UPLOAD --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex justify-between items-center mb-3">
                            <label class="block text-sm font-bold text-gray-900">
                                @if($booking->status === 'approved')
                                Foto Check-Out (Awal)
                                @else
                                Foto Check-In (Akhir)
                                @endif
                                <span class="text-red-600">*</span>
                            </label>
                            <span class="text-[10px] font-medium bg-white px-2 py-1 rounded border text-gray-600">
                                Total Dipilih: {{ count($collected_photos) }}
                            </span>
                        </div>

                        {{-- TOMBOL ACTION --}}
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-4">
                            {{-- 1. UPLOAD FILE --}}
                            <label for="file-queue-input" class="cursor-pointer group flex flex-col items-center justify-center py-4 border-2 border-dashed border-gray-300 rounded-lg hover:bg-white hover:border-gray-400 transition-all bg-white/50">
                                <x-heroicon-o-cloud-arrow-up class="w-6 h-6 mb-1 text-gray-400 group-hover:text-gray-600" />
                                <span class="text-[11px] font-bold text-gray-700 uppercase">Tambah Foto</span>
                                <input id="file-queue-input" type="file" wire:model="temp_photos" multiple accept="image/*" class="hidden" />
                            </label>

                            {{-- 2. CAMERA MOBILE (Hanya Muncul di Mobile) --}}
                            <label for="mobile-camera-input" class="md:hidden cursor-pointer group flex flex-col items-center justify-center py-4 border-2 border-dashed border-gray-300 rounded-lg hover:bg-white hover:border-gray-400 transition-all bg-white/50">
                                <x-heroicon-o-camera class="w-6 h-6 mb-1 text-gray-400 group-hover:text-gray-600" />
                                <span class="text-[11px] font-bold text-gray-700 uppercase">Kamera HP</span>
                                <input id="mobile-camera-input" type="file" wire:model="temp_photos" accept="image/*" capture="environment" class="hidden" />
                            </label>

                            {{-- 3. WEBCAM PC (Hanya Muncul di Desktop) --}}
                            <button type="button" @click="$dispatch('open-webcam')" class="hidden md:flex flex-col items-center justify-center py-4 border-2 border-dashed border-gray-300 rounded-lg hover:bg-white hover:border-gray-400 transition-all bg-white/50">
                                <x-heroicon-o-video-camera class="w-6 h-6 mb-1 text-gray-400 group-hover:text-gray-600" />
                                <span class="text-[11px] font-bold text-gray-700 uppercase">Webcam PC</span>
                            </button>
                        </div>

                        {{-- Loading State saat memilih file --}}
                        <div wire:loading wire:target="temp_photos" class="w-full text-center mt-2">
                            <span class="text-xs text-blue-600 font-medium animate-pulse">Memproses gambar...</span>
                        </div>

                        @error('temp_photos.*') <span class="text-xs text-red-600 block mt-1">{{ $message }}</span> @enderror
                        @error('collected_photos') <span class="text-xs text-red-600 block mt-1">{{ $message }}</span> @enderror

                        {{-- GALLERY PREVIEW GRID --}}
                        @if(!empty($collected_photos))
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mt-4">
                            @foreach($collected_photos as $index => $photo)
                            <div class="relative group aspect-square bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden" wire:key="photo-{{ $index }}">
                                {{-- Image --}}
                                @php
                                $tempUrl = null;
                                try {
                                $tempUrl = $photo->temporaryUrl();
                                } catch (\Exception $e) {}
                                @endphp

                                @if($tempUrl)
                                <img src="{{ $tempUrl }}" class="w-full h-full object-cover">
                                @else
                                <div class="flex items-center justify-center h-full bg-gray-100 text-[10px] text-red-500">
                                    Format Error
                                </div>
                                @endif

                                {{-- Remove Button (X) --}}
                                <button type="button" wire:click="removePhoto({{ $index }})"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 shadow hover:bg-red-600 transition-colors z-10"
                                    title="Hapus foto ini">
                                    <x-heroicon-o-x-mark class="h-3 w-3" />
                                </button>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4 bg-white border rounded-lg text-gray-400 text-xs italic">
                            Belum ada foto yang ditambahkan.
                        </div>
                        @endif
                    </div>

                    <div class="flex gap-3 pt-2 justify-end">
                        <a href="{{ route('vehiclestatus') }}" class="px-4 py-2 border rounded-lg text-sm text-gray-700 hover:bg-gray-50">Batal</a>

                        <button type="submit"
                            class="bg-gray-900 text-white px-6 py-2 rounded-lg text-sm hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
                            @if(empty($collected_photos)) disabled @endif>

                            <span wire:loading.remove wire:target="handlePhotoUpload">
                                Unggah {{ count($collected_photos) }} Foto
                            </span>
                            <span wire:loading wire:target="handlePhotoUpload" class="flex items-center gap-2">
                                <x-heroicon-o-arrow-path class="animate-spin h-4 w-4 text-white" />
                                Mengunggah...
                            </span>
                        </button>
                    </div>
                </form>
                @else
                {{-- Create Mode Form --}}
                <form wire:submit.prevent="submitBooking" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Name --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-900 mb-1.5">Nama <span class="text-red-600">*</span></label>
                            @if($name)
                            <div class="w-full px-3 py-2 text-sm text-gray-500 bg-gray-100 border border-gray-200 rounded-md cursor-not-allowed">{{ $name }}</div>
                            <input type="hidden" wire:model="name" />
                            @else
                            <input wire:model="name" type="text" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900 focus:outline-none">
                            @endif
                            @error('name') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- Dept --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-900 mb-1.5">Departemen <span class="text-red-600">*</span></label>
                            <select wire:model="department_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900 focus:outline-none">
                                <option value="">Pilih departemen</option>
                                @foreach($departments as $d) <option value="{{ $d->department_id }}">{{ $d->department_name }}</option> @endforeach
                            </select>
                            @error('department_id') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- MODE SWITCHER --}}
                        <div class="md:col-span-2 pt-2">
                            <label class="block text-xs font-bold text-gray-900 mb-2">Durasi Pemesanan</label>
                            <div class="grid grid-cols-3 bg-gray-100 p-1 rounded-lg gap-1">
                                <button type="button" wire:click="setBookingMode('perday')"
                                    class="text-sm font-medium py-2 rounded-md transition-all duration-200 {{ $booking_mode === 'perday' ? 'bg-white text-gray-900 shadow ring-1 ring-black/5 font-bold' : 'text-gray-500 hover:text-gray-700' }}">
                                    Seharian
                                    <span class="block text-[10px] font-normal opacity-75">08:00 - 17:00</span>
                                </button>
                                <button type="button" wire:click="setBookingMode('24hours')"
                                    class="text-sm font-medium py-2 rounded-md transition-all duration-200 {{ $booking_mode === '24hours' ? 'bg-white text-gray-900 shadow ring-1 ring-black/5 font-bold' : 'text-gray-500 hover:text-gray-700' }}">
                                    24 Jam
                                    <span class="block text-[10px] font-normal opacity-75">+1 Hari</span>
                                </button>
                                <button type="button" wire:click="setBookingMode('custom')"
                                    class="text-sm font-medium py-2 rounded-md transition-all duration-200 {{ $booking_mode === 'custom' ? 'bg-white text-gray-900 shadow ring-1 ring-black/5 font-bold' : 'text-gray-500 hover:text-gray-700' }}">
                                    Kustom
                                    <span class="block text-[10px] font-normal opacity-75">Waktu Manual</span>
                                </button>
                            </div>
                        </div>

                        {{-- DATES & TIMES --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-900 mb-1.5">Tanggal Mulai <span class="text-red-600">*</span></label>
                            <input wire:model.live="date_from" type="date" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900 focus:outline-none">
                            @error('date_from') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-900 mb-1.5">Tanggal Selesai <span class="text-red-600">*</span></label>
                            <input wire:model.live="date_to" wire:key="to-{{ $booking_mode }}-{{ $date_from }}" type="date"
                                @if($booking_mode !=='custom' ) readonly @endif
                                class="w-full px-3 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 {{ $booking_mode !== 'custom' ? 'bg-gray-100 text-gray-500 border-gray-200 cursor-not-allowed' : 'border-gray-300' }}">
                            @error('date_to') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-900 mb-1.5">Jam Mulai <span class="text-red-600">*</span></label>
                            <input wire:model="start_time" wire:key="start-{{ $booking_mode }}" type="time"
                                @if($booking_mode !=='custom' ) readonly @endif
                                class="w-full px-3 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 {{ $booking_mode !== 'custom' ? 'bg-gray-100 text-gray-500 border-gray-200 cursor-not-allowed' : 'border-gray-300' }}">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-900 mb-1.5">Jam Selesai <span class="text-red-600">*</span></label>
                            <input wire:model="end_time" wire:key="end-{{ $booking_mode }}" type="time"
                                @if($booking_mode !=='custom' ) readonly @endif
                                class="w-full px-3 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 {{ $booking_mode !== 'custom' ? 'bg-gray-100 text-gray-500 border-gray-200 cursor-not-allowed' : 'border-gray-300' }}">
                        </div>

                        {{-- Keperluan --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-900 mb-1.5">Keperluan <span class="text-red-600">*</span></label>
                            <input wire:model="purpose" type="text" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900 focus:outline-none">
                        </div>

                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-900 mb-1.5">Tujuan Lokasi</label>
                                <input wire:model="destination" type="text" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-900 mb-1.5">Area Ganjil/Genap</label>
                                <select wire:model="odd_even_area" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900 focus:outline-none">
                                    <option value="tidak">Tidak Masuk Area</option>
                                    <option value="ganjil">Ganjil</option>
                                    <option value="genap">Genap</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-900 mb-1.5">Jenis Keperluan</label>
                            <select wire:model="purpose_type" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900 focus:outline-none">
                                <option value="dinas">Dinas (Visitasi)</option>
                                <option value="operasional">Operasional (Logistik)</option>
                                <option value="antar_jemput">Antar Jemput</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>

                        {{-- VEHICLE SELECTOR --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-900 mb-1.5">Pilih Kendaraan (Opsional)</label>
                            <select wire:model.live="vehicle_id" @if(!$hasVehicles) disabled @endif
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900 focus:outline-none disabled:bg-gray-100">
                                <option value="">Sembarang Kendaraan</option>
                                @foreach($vehicles as $v)
                                @php
                                $isUnavailable = in_array($v->vehicle_id, $unavailableVehicleIds);
                                $label = ($v->vehicle_name ?? $v->name) . ($v->plate_number ? " — " . $v->plate_number : '');
                                @endphp
                                <option value="{{ $v->vehicle_id }}" @if($isUnavailable) disabled @endif>
                                    {{ $label }} {{ $isUnavailable ? '(Tidak Tersedia)' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Footer with Terms Modal --}}
                    <div class="pt-4 border-t border-gray-100" x-data="{ showTerms: false }">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input wire:model="has_sim_a" type="checkbox" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                            <span class="text-sm text-gray-700">Saya memiliki SIM A (Wajib)</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer mt-2">
                            <input wire:model="agree_terms" type="checkbox" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                            <span class="text-sm text-gray-700">
                                Saya Menyetujui
                                <button type="button" @click="showTerms = true" class="text-blue-600 hover:text-blue-800 underline font-medium">
                                    Syarat & Ketentuan
                                </button>
                            </span>
                        </label>

                        {{-- Terms Modal --}}
                        <div x-show="showTerms"
                            x-cloak
                            @click.self="showTerms = false"
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
                            style="display: none;">

                            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
                                @click.stop>

                                {{-- Modal Header --}}
                                <div class="sticky top-0 bg-gray-900 text-white px-6 py-4 flex items-center justify-between border-b-2 border-black">
                                    <h3 class="text-lg font-bold">Syarat dan Ketentuan Peminjaman Kendaraan</h3>
                                    <button @click="showTerms = false" class="text-white hover:text-gray-300">
                                        <x-heroicon-o-x-mark class="h-6 w-6" />
                                    </button>
                                </div>

                                {{-- Modal Body --}}
                                <div class="p-6 overflow-y-auto max-h-[calc(90vh-180px)] space-y-4 text-sm text-gray-700">

                                    <div>
                                        <h4 class="font-bold text-gray-900 mb-2">1. Persyaratan Umum</h4>
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li>Peminjam harus memiliki SIM A yang masih berlaku</li>
                                            <li>Peminjam harus karyawan aktif perusahaan</li>
                                            <li>Usia peminjam minimal 21 tahun</li>
                                            <li>Peminjaman harus disetujui oleh kepala departemen</li>
                                        </ul>
                                    </div>

                                    <div>
                                        <h4 class="font-bold text-gray-900 mb-2">2. Tanggung Jawab Peminjam</h4>
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li>Bertanggung jawab penuh atas keselamatan kendaraan selama masa peminjaman</li>
                                            <li>Wajib melakukan pengecekan kondisi kendaraan sebelum dan sesudah penggunaan</li>
                                            <li>Wajib mengembalikan kendaraan dalam kondisi bersih dan bahan bakar penuh</li>
                                            <li>Menanggung segala biaya kerusakan yang terjadi akibat kelalaian</li>
                                            <li>Wajib melaporkan segera jika terjadi kecelakaan atau kerusakan</li>
                                        </ul>
                                    </div>

                                    <div>
                                        <h4 class="font-bold text-gray-900 mb-2">3. Penggunaan Kendaraan</h4>
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li>Kendaraan hanya boleh digunakan untuk keperluan dinas perusahaan</li>
                                            <li>Dilarang menggunakan kendaraan untuk keperluan pribadi tanpa izin</li>
                                            <li>Dilarang meminjamkan kendaraan kepada pihak lain</li>
                                            <li>Wajib mematuhi peraturan lalu lintas yang berlaku</li>
                                            <li>Dilarang menggunakan kendaraan dalam kondisi tidak fit (mengantuk, sakit, dll)</li>
                                        </ul>
                                    </div>

                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-6">
                                        <p class="text-sm font-medium text-yellow-800">
                                            <strong>Perhatian:</strong> Dengan mencentang persetujuan, Anda menyatakan telah membaca, memahami, dan menyetujui seluruh syarat dan ketentuan yang berlaku.
                                        </p>
                                    </div>
                                </div>

                                {{-- Modal Footer --}}
                                <div class="sticky bottom-0 bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                                    <button type="button"
                                        @click="showTerms = false"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                        Tutup
                                    </button>
                                    <button type="button"
                                        @click="showTerms = false; $wire.set('agree_terms', true)"
                                        class="px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                                        Saya Setuju
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm hover:bg-gray-800 inline-flex items-center gap-1.5">
                            <x-heroicon-o-check-circle class="w-4 h-4" />
                            Kirim Permintaan
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>

        {{-- RIGHT: Sidebar --}}
        <div class="space-y-6">

            {{-- Availability (Accordion for Mobile) --}}
            <div
                x-data="{ open: window.innerWidth >= 1024 }"
                x-init="window.addEventListener('resize', () => open = window.innerWidth >= 1024)"
                class="bg-white rounded-xl shadow-sm border-2 border-black"
                wire:poll.5000ms="loadAvailability">

                {{-- Header --}}
                <button
                    class="w-full flex items-center justify-between px-4 py-3 md:p-5"
                    @click="if(window.innerWidth < 1024) open = !open">
                    <div class="flex flex-col text-left">
                        <h3 class="text-base font-semibold text-gray-900 leading-none">Ketersediaan</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-none">Status langsung untuk tanggal/waktu yang dipilih</p>
                    </div>

                    {{-- Arrow --}}
                    <svg class="w-4 h-4 text-gray-700 transition-transform lg:hidden"
                        :class="open ? 'rotate-90' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                {{-- Content --}}
                <div x-show="open" x-collapse>
                    <div class="px-4 pb-4 md:px-5 md:pb-5 space-y-3">
                        @forelse($availability as $a)
                        <div class="flex justify-between p-3 border rounded-lg
                {{ $a['status'] === 'available' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                            <span class="font-medium text-sm text-gray-900">{{ $a['label'] }}</span>
                            <span class="text-xs font-bold uppercase
                    {{ $a['status'] === 'available' ? 'text-green-700' : 'text-red-700' }}">
                                {{ $a['status'] }}
                            </span>
                        </div>
                        @empty
                        <div class="text-sm text-gray-500 italic">Pilih tanggal yang valid.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Recent Vehicle Usage --}}
            <div
                x-data="{ open: window.innerWidth >= 1024 }"
                x-init="window.addEventListener('resize', () => open = window.innerWidth >= 1024)"
                class="bg-white rounded-xl shadow-sm border-2 border-black">

                {{-- Header --}}
                <button
                    class="w-full flex items-center justify-between px-4 py-3 md:p-5"
                    @click="if(window.innerWidth < 1024) open = !open">
                    <div class="flex flex-col text-left">
                        <h3 class="text-base font-semibold text-gray-900 leading-none">Penggunaan Kendaraan Terbaru</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-none">3 log terakhir</p>
                    </div>

                    <svg class="w-4 h-4 text-gray-700 transition-transform lg:hidden"
                        :class="open ? 'rotate-90' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                {{-- Content --}}
                <div x-show="open" x-collapse>
                    <div class="px-4 pb-4 md:px-5 md:pb-5 space-y-4">
                        @forelse($recentBookings as $rb)
                        <div class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-0 last:pb-0">

                            {{-- Icon --}}
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center 
                            flex-shrink-0 border border-gray-200 text-gray-600">
                                <x-heroicon-o-truck class="h-4 w-4" />
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="font-bold text-sm text-gray-900 truncate">
                                        {{ $rb->vehicle->name ?? 'Unknown' }}
                                    </h4>

                                    @php
                                    $statusColor = match($rb->status) {
                                    'approved' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'returned' => 'bg-indigo-100 text-indigo-800',
                                    default => 'bg-gray-100 text-gray-800'
                                    };
                                    @endphp

                                    <span class="shrink-0 text-[10px] font-bold uppercase px-2 py-0.5 rounded {{ $statusColor }}">
                                        {{ str_replace('_', ' ', $rb->status) }}
                                    </span>
                                </div>

                                <p class="text-xs text-gray-500 mt-1 leading-tight">
                                    Dipesan oleh <span class="font-medium text-gray-700">{{ $rb->borrower_name }}</span><br>
                                    <span class="opacity-75">
                                        {{ \Carbon\Carbon::parse($rb->start_at)->format('d M, H:i') }}
                                    </span>
                                </p>
                            </div>

                        </div>
                        @empty
                        <div class="text-sm text-gray-500 text-center py-2">
                            Tidak ada pemesanan terbaru.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL WEBCAM (Aksesibilitas Terjaga) --}}
    <div x-data="webcamHandler()"
        x-show="show"
        x-on:open-webcam.window="open()"
        class="fixed inset-0 bg-black/80 z-[9999] flex items-center justify-center p-4"
        x-cloak>
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden" @click.stop>
            <div class="bg-gray-900 text-white px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    <span class="text-sm font-medium uppercase">Kamera Langsung</span>
                </div>
                <button type="button" @click="close()" class="text-white hover:text-gray-300">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            <div class="p-4 space-y-4">
                <div class="bg-black rounded-lg overflow-hidden flex items-center justify-center aspect-video relative">
                    <video x-ref="video" autoplay playsinline class="w-full h-full object-cover transform scale-x-[-1]"></video>
                </div>
                <button type="button" @click="capture()"
                    class="w-full py-3 bg-gray-900 text-white rounded-lg font-bold flex items-center justify-center gap-2">
                    <x-heroicon-o-camera class="w-5 h-5" />
                    AMBIL FOTO
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function webcamHandler() {
        return {
            show: false,
            stream: null,
            open() {
                this.show = true;
                navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: "environment"
                        }
                    })
                    .then(stream => {
                        this.stream = stream;
                        this.$refs.video.srcObject = stream;
                    })
                    .catch(err => {
                        alert("Kamera tidak ditemukan atau akses ditolak.");
                        this.show = false;
                    });
            },
            close() {
                if (this.stream) {
                    this.stream.getTracks().forEach(track => track.stop());
                    this.stream = null;
                }
                this.show = false;
            },
            async capture() {
                const video = this.$refs.video;
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);

                canvas.toBlob(async (blob) => {
                    if (blob) {
                        let file = new File([blob], "webcam_" + Date.now() + ".jpg", {
                            type: "image/jpeg"
                        });
                        @this.upload('temp_photos', file);
                    }
                    this.close();
                }, 'image/jpeg', 0.8);
            }
        }
    }
</script>