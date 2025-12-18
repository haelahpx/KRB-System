<div class="min-h-screen bg-gray-50"> @php $card = "bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden"; $head = "bg-gradient-to-r from-gray-900 to-black"; $label = "block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1"; $value = "text-base font-semibold text-gray-900"; @endphp

<div class="px-4 sm:px-6 py-6 space-y-6">
    <div class="relative overflow-hidden rounded-2xl {{ $head }} text-white shadow-2xl">
        <div class="pointer-events-none absolute inset-0 opacity-10">
            <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
            <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
        </div>
        <div class="relative z-10 p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('receptionist.vehiclestatus') }}" class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20 hover:bg-white/20 transition">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">Detail Pemesanan #{{ $booking->vehiclebooking_id }}</h2>
                        <p class="text-sm text-white/80">Informasi lengkap unit dan kondisi kendaraan</p>
                    </div>
                </div>
                <div>
                    <span class="px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase backdrop-blur-md border
                        {{ $booking->status === 'approved' ? 'bg-emerald-500/20 border-emerald-500/50 text-emerald-200' : 
                           ($booking->status === 'pending' ? 'bg-amber-500/20 border-amber-500/50 text-amber-200' : 'bg-white/10 border-white/20 text-white') }}">
                        {{ $booking->status }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="{{ $card }}">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-gray-900 rounded-full"></div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-tight">Data Perjalanan</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="{{ $label }}">Peminjam</label>
                            <p class="{{ $value }}">{{ $booking->borrower_name }}</p>
                        </div>
                        <div>
                            <label class="{{ $label }}">Tujuan</label>
                            <p class="{{ $value }}">{{ $booking->destination ?? '—' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="{{ $label }}">Keperluan</label>
                            <p class="text-gray-700 leading-relaxed">{{ $booking->purpose }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="{{ $card }}">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-gray-900 rounded-full"></div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-tight">Foto Dokumentasi</h3>
                    </div>
                </div>
                <div class="p-6 space-y-8">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="w-1 h-4 bg-blue-600 rounded-full"></span>
                            <p class="text-sm font-bold text-gray-900">Foto Sebelum Keberangkatan</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @forelse($booking->photos->where('photo_type', 'before') as $p)
                                <div class="group relative overflow-hidden rounded-xl border border-gray-200 shadow-sm">
                                    <img src="{{ asset('storage/'.$p->photo_path) }}" class="w-full aspect-video object-cover group-hover:scale-105 transition duration-500">
                                </div>
                            @empty
                                <div class="col-span-2 py-8 rounded-xl border border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <p class="text-xs">Belum ada foto sebelum</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="w-1 h-4 bg-emerald-600 rounded-full"></span>
                            <p class="text-sm font-bold text-gray-900">Foto Sesudah Kepulangan</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @forelse($booking->photos->where('photo_type', 'after') as $p)
                                <div class="group relative overflow-hidden rounded-xl border border-gray-200 shadow-sm">
                                    <img src="{{ asset('storage/'.$p->photo_path) }}" class="w-full aspect-video object-cover group-hover:scale-105 transition duration-500">
                                </div>
                            @empty
                                <div class="col-span-2 py-8 rounded-xl border border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <p class="text-xs">Belum ada foto sesudah</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="{{ $card }}">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-gray-900 rounded-full"></div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-tight">Detail Unit</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-gray-900 rounded-xl flex items-center justify-center text-white shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 leading-tight">{{ $booking->vehicle->name ?? 'Unit N/A' }}</h2>
                            <p class="text-indigo-600 font-mono text-sm font-bold">{{ $booking->vehicle->plate_number ?? '—' }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3 pt-4 border-t border-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-medium text-gray-500 uppercase">Tipe</span>
                            <span class="text-sm font-bold text-gray-900">{{ $booking->vehicle->type ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-medium text-gray-500 uppercase">Bahan Bakar</span>
                            <span class="text-sm font-bold text-gray-900">{{ $booking->vehicle->fuel_type ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-amber-50 border border-dashed border-amber-200 text-sm text-amber-800 flex items-start gap-3">
                <div class="mt-0.5 shrink-0">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="font-bold">Log Sistem</p>
                    <p class="text-xs mt-1 opacity-80">Data ini diarsipkan secara otomatis oleh sistem pada saat status pemesanan diperbarui.</p>
                </div>
            </div>
        </aside>
    </div>
</div>
</div>