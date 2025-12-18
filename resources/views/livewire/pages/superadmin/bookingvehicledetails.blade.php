<div class="bg-gray-50 min-h-screen"> @php $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden'; $label = 'block text-sm font-medium text-gray-700 mb-2'; $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 transition'; $btnLt = 'px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900/10 disabled:opacity-60 transition'; $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md'; $icoAvatar = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';

    $isRejected = ($booking->status === 'rejected');
    $statusStyle = $statusMap[$booking->status] ?? $statusMap['completed'];
    $vehicleName = $vehicleMap[$booking->vehicle_id] ?? 'Tidak Diketahui';
@endphp

<main class="px-4 sm:px-6 py-6 space-y-8">
    
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
        <div class="relative p-6 sm:p-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center border border-white/20">
                        <x-heroicon-o-truck class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">
                            Peminjaman #{{ $booking->vehiclebooking_id }}
                        </h2>
                        <p class="text-sm text-white/80">
                            Keperluan: <span class="font-semibold text-white">{{ $booking->purpose ?? 'N/A' }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 ml-auto mt-4 md:mt-0">
                    <span class="text-[11px] px-3 py-1.5 rounded-full font-bold uppercase
                        {{ $statusStyle['bg'] }} {{ $statusStyle['text'] }} border {{ $statusStyle['border'] ?? $statusStyle['bg'] }}">
                        {{ $statusStyle['label'] }}
                    </span>
                    <a href="{{ url()->previous() }}" class="{{ $btnLt }} border-white/30 text-white/90 hover:bg-white/10">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="space-y-6 lg:col-span-1 lg:order-2">
            
            <div class="{{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Kendaraan & Peminjam</h3>
                </div>

                <div class="p-5 space-y-4 text-sm">
                    
                    <div class="group flex items-center gap-3 p-3 rounded-lg border border-gray-200 bg-white shadow-sm hover:border-indigo-200 transition-colors">
                        <div class="{{ $icoAvatar }} !w-12 !h-12 !rounded-xl text-lg shrink-0 bg-indigo-50 text-indigo-700">
                            <x-heroicon-o-truck class="w-6 h-6" />
                        </div>
                        <div class="flex flex-col overflow-hidden min-w-0">
                            <span class="text-xs text-gray-400 font-medium mb-0.5">Kendaraan</span>
                            <span class="font-semibold text-gray-900 truncate">{{ $vehicleName }}</span>
                            <span class="text-[10px] text-gray-500 truncate flex items-center gap-1 mt-0.5">
                                {{ $booking->vehicle?->plate_number ?? 'Tanpa Plat' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between p-2.5 rounded-md bg-gray-50 border border-gray-100">
                        <span class="text-xs font-medium text-gray-500">Peminjam</span>
                        <span class="text-sm font-semibold text-gray-900 text-right">
                            {{ $booking->borrower_name ?? 'N/A' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-3 pt-2 border-t border-gray-100">
                        <div class="flex flex-wrap items-center justify-between">
                            <p class="text-xs text-gray-500">ID Peminjaman</p>
                            <p class="{{ $mono }} mt-0 inline-block !text-xs">{{ $booking->vehiclebooking_id }}</p>
                        </div>
                        <div class="flex flex-wrap items-center justify-between">
                            <p class="text-xs text-gray-500">Persetujuan Admin</p>
                            <p class="text-xs font-medium text-gray-700 text-right">
                                {{ $booking->approved_at ? $fmtDate($booking->approved_at) . ' ' . $fmtTime($booking->approved_at) : 'N/A' }}
                            </p>
                        </div>
                        @if($booking->deleted_at)
                        <div class="flex flex-wrap items-center justify-between">
                            <p class="text-xs text-rose-600">Dihapus (Soft-Delete)</p>
                            <p class="font-medium text-rose-700 text-right">
                                {{ $fmtDate($booking->deleted_at) }} {{ $fmtTime($booking->deleted_at) }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <section class="{{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Foto Sebelum ({{ $beforePhotos->count() }})</h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-3 gap-3">
                        @forelse($beforePhotos as $p)
                        <div class="relative group border rounded-lg overflow-hidden bg-gray-50 cursor-pointer"
                            wire:click="openPhotoPreview('{{ $photoUrl($p->photo_path) }}')">
                            <img src="{{ $photoUrl($p->photo_path) }}" alt="sebelum" class="w-full h-24 object-cover">
                            @if($p->deleted_at)
                            <span class="absolute top-1 left-1 text-[9px] bg-rose-600 text-white px-1 py-0.5 rounded">Dihapus</span>
                            @endif
                        </div>
                        @empty
                        <div class="col-span-full text-center text-sm text-gray-500 p-3">Tidak Ada Foto Sebelum</div>
                        @endforelse
                    </div>
                </div>
            </section>
            
            <section class="{{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Foto Sesudah ({{ $afterPhotos->count() }})</h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-3 gap-3">
                        @forelse($afterPhotos as $p)
                        <div class="relative group border rounded-lg overflow-hidden bg-gray-50 cursor-pointer"
                            wire:click="openPhotoPreview('{{ $photoUrl($p->photo_path) }}')">
                            <img src="{{ $photoUrl($p->photo_path) }}" alt="sesudah" class="w-full h-24 object-cover">
                            @if($p->deleted_at)
                            <span class="absolute top-1 left-1 text-[9px] bg-rose-600 text-white px-1 py-0.5 rounded">Dihapus</span>
                            @endif
                        </div>
                        @empty
                        <div class="col-span-full text-center text-sm text-gray-500 p-3">Tidak Ada Foto Sesudah</div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        <section class="lg:col-span-2 lg:order-1 space-y-6">

            <div class="{{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">Jadwal & Destinasi Peminjaman</h2>
                </div>
                <div class="p-5 space-y-4">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="{{ $label }}">Waktu Mulai</label>
                            <div class="w-full p-3 rounded-lg border border-gray-200 bg-gray-50 text-gray-800 font-medium text-sm">
                                {{ $fmtDate($booking->start_at) }} {{ $fmtTime($booking->start_at) }}
                            </div>
                        </div>
                        <div>
                            <label class="{{ $label }}">Waktu Selesai</label>
                            <div class="w-full p-3 rounded-lg border border-gray-200 bg-gray-50 text-gray-800 font-medium text-sm">
                                {{ $fmtDate($booking->end_at) }} {{ $fmtTime($booking->end_at) }}
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="{{ $label }}">Destinasi</label>
                        <div class="w-full p-3 rounded-lg border border-gray-200 bg-gray-50 text-gray-800 font-medium text-sm">
                            {{ $booking->destination ?? 'N/A' }}
                        </div>
                    </div>

                    <div>
                        <label class="{{ $label }}">Catatan / Alasan Penolakan</label>
                        @if(!empty($booking->notes))
                            <div class="p-3 rounded-lg border text-sm
                                {{ $isRejected ? 'bg-rose-50 border-rose-200 text-rose-800' : 'bg-gray-50 border-gray-200 text-gray-700' }}">
                                {{ $booking->notes }}
                            </div>
                        @else
                            <div class="p-3 rounded-lg border border-dashed border-gray-300 bg-white text-sm text-gray-500">
                                — Tidak Ada Catatan —
                            </div>
                        @endif
                    </div>
                    
                </div>
            </div>
        </section>
    </div>

    @if($showPreviewModal)
        <div class="fixed inset-0 z-[110] flex items-center justify-center" wire:click="closePhotoPreview" wire:keydown.escape.window="closePhotoPreview">
            <div class="absolute inset-0 bg-black/80"></div>
            <div class="relative max-w-full max-h-full" @click.stop>
                <img src="{{ $previewUrl ?? '' }}" class="max-h-[90vh] max-w-[90vw] object-contain rounded-lg shadow-2xl" alt="Pratinjau Foto">
                <button class="absolute top-2 right-2 p-2 bg-white/30 rounded-full text-white hover:bg-white/50" wire:click="closePhotoPreview">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
        </div>
    @endif
    
</main>
</div>