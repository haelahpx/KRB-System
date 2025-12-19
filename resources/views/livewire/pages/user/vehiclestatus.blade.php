<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="bg-[#0a0a0a] rounded-xl shadow-sm border-2 border-black p-4 md:p-6 mb-4 md:mb-6" wire:ignore>
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <h1 class="text-xl md:text-2xl font-bold text-white text-center lg:text-left whitespace-nowrap">
                Status Pemesanan Kendaraan
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

    {{-- Main Content --}}
    <div class="bg-white rounded-xl shadow-sm border-2 border-black p-4 md:p-5">
        {{-- Search / Filters header --}}
        <div class="flex flex-col gap-4 pb-4 mb-4 border-b border-gray-100">

            <div class="grid gap-3 grid-cols-1 sm:grid-cols-1 md:grid-cols-4">

                {{-- Search --}}
                <div class="relative md:col-span-1 col-span-1">
                    <input type="text"
                        wire:model.live.debounce.400ms="q"
                        placeholder="Cari tujuan / kendaraan..."
                        class="w-full px-3 py-2 pl-9 text-sm text-gray-900 placeholder:text-gray-400
                       border border-gray-300 rounded-md
                       focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                    <x-heroicon-o-magnifying-glass
                        class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                </div>

                <div class="grid grid-cols-3 gap-3 md:col-span-3 col-span-1 md:grid-cols-3">

                    {{-- Vehicle Filter --}}
                    <select wire:model.live="vehicleFilter"
                        class="px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-md
                       focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        <option value="">Kendaraan</option>
                        @foreach($vehicles as $v)
                        @php $label = $v->name ?? $v->plate_number ?? ('#'.$v->vehicle_id); @endphp
                        <option value="{{ $v->vehicle_id }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    {{-- Sort --}}
                    <select wire:model.live="sortFilter"
                        class="px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-md
                       focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        <option value="recent">Terbaru</option>
                        <option value="oldest">Tertua</option>
                        <option value="nearest">Terdekat</option>
                    </select>

                    {{-- Status --}}
                    <select wire:model.live="dbStatusFilter"
                        class="px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-md
                       focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        <option value="all">Status</option>
                        <option value="pending">Tertunda</option>
                        <option value="approved">Disetujui</option>
                        <option value="on_progress">Sedang Berlangsung</option>
                        <option value="returned">Dikembalikan</option>
                        <option value="rejected">Ditolak</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>

                </div>
            </div>
        </div>

        {{-- List --}}
        <div class="space-y-4">
            @forelse($bookings as $b)
            @php
            $start = \Carbon\Carbon::parse($b->start_at, 'Asia/Jakarta');
            $end = \Carbon\Carbon::parse($b->end_at, 'Asia/Jakarta');
            $dateStr = $start->format('D, M j, Y');
            $timeStr = $start->format('H:i').'–'.$end->format('H:i');
            $vehicleName = $vehicleMap[$b->vehicle_id] ?? 'Unknown';

            $statusConfig = match($b->status) {
            'pending' => ['class' => 'bg-yellow-100 text-yellow-800 border-yellow-200', 'icon' => 'clock'],
            'approved' => ['class' => 'bg-green-100 text-green-800 border-green-200', 'icon' => 'check-circle'],
            'on_progress' => ['class' => 'bg-blue-100 text-blue-800 border-blue-200', 'icon' => 'play-circle'],
            'returned' => ['class' => 'bg-indigo-100 text-indigo-800 border-indigo-200', 'icon' => 'arrow-path'],
            'rejected' => ['class' => 'bg-red-100 text-red-800 border-red-200', 'icon' => 'x-circle'],
            'cancelled' => ['class' => 'bg-gray-100 text-gray-600 border-gray-200', 'icon' => 'no-symbol'],
            'completed' => ['class' => 'bg-gray-100 text-gray-800 border-gray-200', 'icon' => 'archive-box'],
            default => ['class' => 'bg-gray-50 text-gray-600 border-gray-200', 'icon' => 'question-mark-circle'],
            };

            $currentPhotoCounts = $photoCounts[$b->vehiclebooking_id] ?? [];
            $beforeC = $currentPhotoCounts['before'] ?? 0;
            $afterC = $currentPhotoCounts['after'] ?? 0;

            $isClickable = in_array($b->status, ['approved', 'returned']);
            $needsUpload = $isClickable && (($b->status == 'approved' && $beforeC == 0) || ($b->status == 'returned' && $afterC == 0));
            $cardTag = $isClickable ? 'a' : 'div';
            @endphp

            <div class="group relative">
                {{-- MOBILE (Accordion) --}}
                <div class="block md:hidden">
                    <{{ $cardTag }}
                        @if($isClickable)
                        href="{{ route('book-vehicle', ['id' => $b->vehiclebooking_id]) }}"
                        wire:navigate
                        @endif
                        class="block bg-white rounded-xl border border-gray-200 overflow-hidden
                        {{ $isClickable ? 'hover:shadow-lg hover:border-gray-300 transition-all duration-200 cursor-pointer' : '' }}
                        {{ $needsUpload ? 'ring-2 ring-yellow-400 ring-offset-2' : '' }}"
                    >
                        <div x-data="{ open: false }">
                            {{-- Header - Always Visible --}}
                            <div class="flex items-center justify-between px-4 py-3 gap-3">
                                <div class="flex flex-col text-left flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-base font-bold text-gray-900 truncate">
                                            {{ $b->purpose ? ucfirst($b->purpose) : 'Pemesanan Kendaraan' }}
                                        </span>
                                        @if($needsUpload)
                                        <span class="shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-yellow-100 text-yellow-800 border border-yellow-300">
                                            <x-heroicon-o-camera class="w-3 h-3" />
                                            Foto
                                        </span>
                                        @endif
                                    </div>
                                    <span class="text-[11px] text-gray-600">
                                        #{{ $b->vehiclebooking_id }} • {{ $vehicleName }}
                                    </span>
                                </div>

                                <button 
                                    @click.prevent.stop="open = !open" 
                                    class="shrink-0 p-1 hover:bg-gray-100 rounded-lg transition-colors"
                                    type="button"
                                >
                                    <span class="transition-transform duration-200 block" :class="open ? 'rotate-180' : ''">
                                        <x-heroicon-o-chevron-down class="w-5 h-5 text-gray-600" />
                                    </span>
                                </button>
                            </div>

                            {{-- Status Badge --}}
                            <div class="px-4 pb-3">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $statusConfig['class'] }}">
                                    {{ str_replace('_',' ', ucfirst($b->status)) }}
                                </span>
                            </div>

                            {{-- Expandable Details --}}
                            <div x-show="open" x-collapse @click.stop class="px-4 pb-4 space-y-3 border-t border-gray-100 pt-3">
                                <div class="flex flex-wrap items-center gap-1.5 text-[10px]">
                                    <span class="px-2 py-1 rounded-md border border-gray-200 bg-gray-50 text-gray-700 flex items-center gap-1">
                                        <x-heroicon-o-calendar-days class="w-3 h-3" /> {{ $dateStr }}
                                    </span>
                                    <span class="px-2 py-1 rounded-md border border-gray-200 bg-gray-50 text-gray-700 flex items-center gap-1">
                                        <x-heroicon-o-clock class="w-3 h-3" /> {{ $timeStr }}
                                    </span>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold">Tujuan</span>
                                        <span class="text-xs text-gray-900 font-medium">{{ $b->destination ?? '-' }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold">Peminjam</span>
                                        <span class="text-xs text-gray-900 font-medium">{{ $b->borrower_name ?? '-' }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-600 border border-gray-200">
                                        <x-heroicon-o-camera class="w-3.5 h-3.5" /> Sebelum: {{ $beforeC }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-600 border border-gray-200">
                                        <x-heroicon-o-camera class="w-3.5 h-3.5" /> Sesudah: {{ $afterC }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </{{ $cardTag }}>
                </div>

                {{-- DESKTOP (Full Card) --}}
                <{{ $cardTag }}
                    @if($isClickable)
                    href="{{ route('book-vehicle', ['id' => $b->vehiclebooking_id]) }}"
                    wire:navigate
                    @endif
                    class="hidden md:block bg-white rounded-xl border border-gray-200 p-5 
                    {{ $isClickable ? 'hover:shadow-lg hover:border-gray-300 transition-all duration-200 cursor-pointer' : '' }}
                    {{ $needsUpload ? 'ring-2 ring-yellow-400 ring-offset-2' : '' }}"
                >
                    {{-- Header Section --}}
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-gray-900 truncate">
                                    {{ $b->purpose ? ucfirst($b->purpose) : 'Pemesanan Kendaraan' }}
                                </h3>
                                @if($needsUpload)
                                <span class="shrink-0 inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[10px] font-bold uppercase bg-yellow-100 text-yellow-800 border border-yellow-300">
                                    <x-heroicon-o-camera class="w-4 h-4" />
                                    Perlu Upload Foto
                                </span>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                <span class="font-mono font-medium text-gray-800 inline-flex items-center gap-1 bg-gray-100 px-2 py-1 rounded">
                                    <x-heroicon-o-hashtag class="w-3 h-3" /> {{ $b->vehiclebooking_id }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md border border-gray-200 bg-gray-50 text-gray-700 font-medium">
                                    <x-heroicon-o-truck class="w-3 h-3" /> {{ $vehicleName }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md border border-gray-200 bg-gray-50 text-gray-700 font-medium">
                                    <x-heroicon-o-calendar-days class="w-3 h-3" /> {{ $dateStr }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md border border-gray-200 bg-gray-50 text-gray-700 font-medium">
                                    <x-heroicon-o-clock class="w-3 h-3" /> {{ $timeStr }}
                                </span>
                            </div>
                        </div>

                        <span class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border {{ $statusConfig['class'] }}">
                            {{ str_replace('_',' ', ucfirst($b->status)) }}
                        </span>
                    </div>

                    {{-- Details Section --}}
                    <div class="grid grid-cols-2 gap-4 pt-3 border-t border-dashed border-gray-200">
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold mb-1">Tujuan</span>
                            <span class="text-sm text-gray-900 font-medium truncate">{{ $b->destination ?? '-' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold mb-1">Peminjam</span>
                            <span class="text-sm text-gray-900 font-medium">{{ $b->borrower_name ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- Footer Section --}}
                    <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">Foto:</span>
                            <span class="text-xs font-medium px-2 py-0.5 bg-gray-100 rounded border">Sebelum: {{ $beforeC }}</span>
                            <span class="text-xs font-medium px-2 py-0.5 bg-gray-100 rounded border">Sesudah: {{ $afterC }}</span>
                        </div>
                        @if($isClickable)
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span>Klik untuk upload foto</span>
                            <x-heroicon-o-arrow-right class="w-4 h-4" />
                        </div>
                        @else
                        <div class="text-[10px] text-gray-400">
                            Dibuat {{ optional($b->created_at)->format('d/m/Y H:i') }}
                        </div>
                        @endif
                    </div>
                </{{ $cardTag }}>
            </div>
            @empty
            <div class="rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                <x-heroicon-o-truck class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pemesanan ditemukan</h3>
            </div>
            @endforelse
        </div>

        @if(method_exists($bookings, 'links'))
        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:navigated', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
</script>