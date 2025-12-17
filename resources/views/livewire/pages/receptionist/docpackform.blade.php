<div class="min-h-screen bg-gray-50" wire:poll.1000ms.keep-alive>
    @php
        $card   = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $head   = 'bg-gradient-to-r from-gray-900 to-black';
        $label  = 'block text-sm font-medium text-gray-700 mb-2';
        $input  = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-900 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
        $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    @endphp

    <style>
      :root { color-scheme: light; }
      select, option {
        color:#111827 !important;
        background:#ffffff !important;
        -webkit-text-fill-color:#111827 !important;
      }
      option:checked { background:#e5e7eb !important; color:#111827 !important; }
    </style>

    <div class="px-4 sm:px-6 py-6 space-y-8">
        {{-- HERO --}}
        <div class="relative overflow-hidden rounded-2xl {{ $head }} text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <x-heroicon-o-archive-box class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">Doc/Pack Form</h2>
                        <p class="text-sm text-white/80">Input paket/dokumen dengan alur masuk/keluar</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM --}}
        <div class="{{ $card }}">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-gray-900 rounded-full"></div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Tambah Data</h3>
                        <p class="text-sm text-gray-500">Lengkapi detail paket/dokumen</p>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="save" class="p-6 space-y-6">
                {{-- Row: Direction & Type & Storage --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="{{ $label }}">Arah</label>
                        <select class="{{ $input }}" wire:model.live="direction" wire:key="direction-select">
                            <option value="taken">Masuk untuk internal (Taken)</option>
                            <option value="deliver">Titip untuk dikirim (Deliver later)</option>
                        </select>
                        @error('direction') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Tipe</label>
                        <select class="{{ $input }}" wire:model.live="itemType" wire:key="type-select">
                            <option value="package">Package</option>
                            <option value="document">Document</option>
                        </select>
                        @error('itemType') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Tempat Penyimpanan</label>
                        <select class="{{ $input }}" wire:model.defer="storageId" wire:key="storage-select">
                            <option value="">Pilih penyimpanan…</option>
                            @foreach($storages as $s)
                                <option wire:key="storage-{{ $s['id'] }}" value="{{ $s['id'] }}">
                                    {{ $s['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('storageId') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Item name --}}
                <div>
                    <label class="{{ $label }}">Nama Paket/Dokumen</label>
                    <input type="text" class="{{ $input }}" wire:model.defer="itemName" placeholder="Contoh: Dokumen Kontrak PT ABC">
                    @error('itemName') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-5">
                        <div>
                            <label class="{{ $label }}">
                                {{ $direction === 'taken' ? 'Departemen Penerima' : 'Departemen Pengirim' }}
                            </label>
                            <select class="{{ $input }}" wire:model.live="departmentId" wire:key="dept-select">
                                <option value="">Pilih departemen…</option>
                                @foreach($departments as $d)
                                    <option wire:key="dept-{{ $d['id'] }}" value="{{ $d['id'] }}">
                                        {{ $d['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('departmentId') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">
                                {{ $direction === 'taken' ? 'Nama Penerima (User)' : 'Nama Pengirim (User)' }}
                            </label>

                            <select
                                class="{{ $input }} bg-white text-gray-900"
                                wire:model.live="userId"
                                wire:key="user-select-{{ $departmentId ?? 'none' }}"
                                @disabled(!$departmentId || empty($users))
                            >
                                <option value="" selected disabled>
                                    {{ !$departmentId ? 'Pilih departemen dulu…' : (empty($users) ? 'Tidak ada user pada departemen ini' : 'Pilih user…') }}
                                </option>

                                @if($departmentId && !empty($users))
                                    @foreach($users as $id => $name)
                                        <option wire:key="user-{{ $id }}" value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                @endif
                            </select>

                            @error('userId') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-5">
                        @if ($direction === 'taken')
                            <div>
                                <label class="{{ $label }}">Nama Pengirim (Free Text)</label>
                                <input type="text" class="{{ $input }}" wire:model.defer="senderText" placeholder="Kurir / Ekspedisi / Pengirim">
                                @error('senderText') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        @else
                            <div>
                                <label class="{{ $label }}">Nama Penerima (Free Text)</label>
                                <input type="text" class="{{ $input }}" wire:model.defer="receiverText" placeholder="Nama penerima">
                                @error('receiverText') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>
                </div>

                {{-- FOTO BUKTI --}}
                <div>
                    <label class="{{ $label }}">Bukti Foto (opsional)</label>

                    <div class="space-y-3">
                        <input
                            id="photo-input"
                            type="file"
                            class="{{ $input }} !h-auto py-2"
                            wire:model="photo"
                            accept="image/*"
                            capture="environment"
                        >
                        @error('photo') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror

                        <button
                            type="button"
                            id="open-camera-btn"
                            class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">
                            <x-heroicon-o-camera class="w-4 h-4" />
                            Ambil dari kamera
                        </button>

                        @if ($photo)
                            <div class="mt-2">
                                <p class="text-xs text-gray-500 mb-1">Preview bukti:</p>
                                <img src="{{ $photo->temporaryUrl() }}" class="w-40 h-40 object-cover rounded-xl border border-gray-200">
                            </div>
                        @endif

                        <p class="text-[11px] text-gray-500">
                            Di HP: bisa pakai kamera atau galeri. Di laptop/PC: klik "Ambil dari kamera", beri izin akses kamera.
                        </p>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="pt-2">
                    <button type="submit" class="{{ $btnBlk }}" wire:loading.attr="disabled" wire:target="save,photo">
                        <span wire:loading.remove wire:target="save,photo">Simpan</span>
                        <span wire:loading wire:target="save,photo" class="inline-flex items-center gap-2">
                            <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" />
                            Menyimpan…
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL KAMERA --}}
    <div id="camera-modal"
         wire:ignore
         class="fixed inset-0 bg-black/60 z-50 items-center justify-center p-4 hidden">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="bg-gray-900 text-white px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-sm font-medium">Kamera</span>
                </div>
                <button id="close-camera-btn" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </div>
            <div class="p-4 space-y-4">
                <div class="bg-black/5 rounded-lg overflow-hidden flex items-center justify-center">
                    <video id="camera-video" autoplay playsinline class="max-h-[60vh]"></video>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">
                        Pastikan browser mengizinkan akses kamera (HTTPS / localhost).
                    </span>
                    <button id="capture-btn" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black">
                        <x-heroicon-o-camera class="w-4 h-4" />
                        Ambil Foto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            function initCameraScript() {
                let stream = null;

                const openBtn   = document.getElementById('open-camera-btn');
                const modal     = document.getElementById('camera-modal');
                const closeBtn  = document.getElementById('close-camera-btn');
                const video     = document.getElementById('camera-video');
                const captureBtn= document.getElementById('capture-btn');
                const fileInput = document.getElementById('photo-input');

                if (!openBtn || !modal || !video || !captureBtn || !fileInput || !closeBtn) return;

                async function openCamera() {
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        alert('Browser tidak mendukung kamera (getUserMedia tidak tersedia).');
                        return;
                    }

                    try {
                        stream = await navigator.mediaDevices.getUserMedia({ video: true });
                        video.srcObject = stream;
                        await video.play();

                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    } catch (e) {
                        alert('Gagal mengakses kamera. Cek izin browser & HTTPS / localhost.');
                    }
                }

                function closeCamera() {
                    if (stream) {
                        stream.getTracks().forEach(t => t.stop());
                        stream = null;
                    }
                    video.srcObject = null;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                openBtn.addEventListener('click', openCamera);
                closeBtn.addEventListener('click', closeCamera);

                captureBtn.addEventListener('click', () => {
                    if (!stream) return;

                    const canvas = document.createElement('canvas');
                    canvas.width  = video.videoWidth  || 640;
                    canvas.height = video.videoHeight || 480;

                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    canvas.toBlob((blob) => {
                        if (!blob) return;

                        const file = new File([blob], 'camera-photo.png', { type: 'image/png' });
                        const dt = new DataTransfer();
                        dt.items.add(file);

                        fileInput.files = dt.files;
                        fileInput.dispatchEvent(new Event('change', { bubbles: true }));

                        closeCamera();
                    }, 'image/png');
                });
            }

            document.addEventListener('DOMContentLoaded', initCameraScript);
            document.addEventListener('livewire:load', initCameraScript);
        })();
    </script>
</div>
