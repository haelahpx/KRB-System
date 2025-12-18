<div class="bg-gray-50" wire:poll.2000ms.keep-alive id="admin-dashboard-root" data-weekly-activity='@json($weeklyActivityData ?? [])'> <main class="px-4 sm:px-6 py-6"> <div class="space-y-8">

        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            {{-- Padding dan struktur utama --}}
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex items-start gap-4"> 
                    {{-- ICON (w-12 h-12) --}}
                    <div
                        class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center shrink-0 backdrop-blur-sm border border-white/20">
                        <x-heroicon-o-cube class="w-6 h-6 text-white" />
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-4">
                            {{-- Judul Utama (Konsisten) --}}
                            <h1 class="text-lg sm:text-xl font-semibold truncate">Ringkasan Dashboard Admin</h1>

                            {{-- POSISI BARU: Informasi Departemen dalam chip --}}
                            <div class="hidden sm:flex shrink-0">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-xs font-medium">
                                    Dept: <span class="font-bold truncate max-w-[120px]" title="{{ $departmentList }}">{{ $departmentList }}</span>
                                </span>
                            </div>
                        </div>

                        <p class="text-xs text-white/60 pt-1">Menampilkan statistik utama dan aktivitas mingguan sistem manajemen tiket.</p>

                        {{-- POSISI BARU: Tampilkan di bawah deskripsi pada layar kecil (mobile) --}}
                        <div class="mt-2 flex sm:hidden">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-xs font-medium">
                                Dept: <span class="font-bold truncate max-w-[120px]" title="{{ $departmentList }}">{{ $departmentList }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ganti '---' dengan div spacer sederhana untuk mematuhi aturan elemen root tunggal --}}
        <div class="w-full h-px bg-gray-200 my-4"></div>

        {{-- Kartu Statistik --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Total Tiket --}}
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tiket Dibuat (7 hari)</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $weeklyTicketsCount }}
                        </p>
                        <p class="text-xs text-green-600 mt-1">Perubahan status vs minggu lalu</p>
                    </div>
                    <div class="p-3 bg-gray-100 rounded-lg">
                        <x-heroicon-o-ticket class="w-6 h-6 text-gray-700" />
                    </div>
                </div>
            </div>

            {{-- Total Pemesanan Ruangan --}}
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Pemesanan Ruang (7 hari)</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $weeklyRoomBookingsCount }}
                        </p>
                        <p class="text-xs text-green-600 mt-1">Perubahan status vs minggu lalu</p>
                    </div>
                    <div class="p-3 bg-gray-100 rounded-lg">
                        <x-heroicon-o-calendar-days class="w-6 h-6 text-gray-700" />
                    </div>
                </div>
            </div>

            {{-- Total Entri Informasi --}}
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Entri Informasi (7 hari)</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $weeklyInformationCount }}
                        </p>
                        <p class="text-xs text-green-600 mt-1">Perubahan status vs minggu lalu</p>
                    </div>
                    <div class="p-3 bg-gray-100 rounded-lg">
                        <x-heroicon-o-document-text class="w-6 h-6 text-gray-700" />
                    </div>
                </div>
            </div>

            {{-- Agen Terbaik --}}
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Agen Terbaik (Tiket Selesai)</p>
                        <p class="text-xl font-bold text-gray-900 truncate" title="{{ $topAgent->full_name ?? 'N/A' }}">
                            {{ $topAgent->full_name ?? 'N/A' }}
                        </p>
                        <p class="text-sm text-gray-700 mt-1">
                            Selesai: <span class="font-semibold text-gray-900">{{ $topAgent->solved_count ?? 0 }}</span>
                        </p>
                    </div>
                    <div class="p-3 bg-gray-100 rounded-lg">
                        <x-heroicon-o-star class="w-6 h-6 text-gray-700" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Ganti '---' dengan div spacer sederhana untuk mematuhi aturan elemen root tunggal --}}
        <div class="w-full h-px bg-gray-200 my-4"></div>

        {{-- Bagian Grafik --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Grafik Aktivitas --}}
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                        <x-heroicon-o-chart-bar class="h-5 w-5 text-gray-900" />
                        Aktivitas Mingguan – Tiket / Pemesanan Ruang / Informasi
                    </h3>
                    <p class="text-xs text-gray-500">
                        7 hari terakhir (Data Real-Time)
                    </p>
                </div>

                {{-- wire:ignore sangat penting untuk mencegah Livewire me-render ulang canvas --}}
                <div class="h-[320px]" wire:ignore>
                    <canvas id="activityChart" class="w-full" style="max-height:320px"></canvas>
                </div>
            </div>

            {{-- Distribusi Status (Prioritas Tiket) --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Distribusi Prioritas Tiket (Bulan Ini)</h3>
                <div class="space-y-4">
                    {{-- Prioritas Tinggi --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Prioritas Tinggi</span>
                            <span class="text-sm font-bold text-gray-900">{{ $highPercent }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: {{ $highPercent }}%"></div>
                        </div>
                    </div>
                    {{-- Prioritas Sedang --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Prioritas Sedang</span>
                            <span class="text-sm font-bold text-gray-900">{{ $mediumPercent }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $mediumPercent }}%"></div>
                        </div>
                    </div>
                    {{-- Prioritas Rendah --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Prioritas Rendah</span>
                            <span class="text-sm font-bold text-gray-900">{{ $lowPercent }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $lowPercent }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-xs text-gray-600 mb-1">Total Tiket Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalTicketsThisMonth }}</p>
                    <p class="text-xs text-gray-600 mt-1">↑ % dari bulan lalu</p>
                </div>
            </div>
        </div>

    </div>
</main>

{{-- Chart.js v4 CDN --}}
<script src="[https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js](https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js)"></script>

@verbatim
<script>
    let __activityChart;
    const rootId = 'admin-dashboard-root';

    function getWeeklyData() {
        const root = document.getElementById(rootId);
        if (!root) {
            console.error("Elemen root Livewire tidak ditemukan.");
            return null;
        }
        const dataAttr = root.dataset.weeklyActivity;
        try {
            return JSON.parse(dataAttr || '{"labels": [], "ticket": [], "room": [], "information": []}');
        } catch (e) {
            console.error("Gagal mengurai data aktivitas mingguan:", e, dataAttr);
            return null;
        }
    }

    function rebuildActivityChart(weekly) {
        const canvas = document.getElementById('activityChart');
        if (!canvas) {
            console.error("Elemen canvas #activityChart tidak ditemukan.");
            return;
        }

        const ctx = canvas.getContext('2d');

        if (__activityChart) {
            __activityChart.destroy();
        }

        console.log("Data diterima untuk chart:", weekly);

        if (!weekly || !weekly.labels || weekly.labels.length === 0) {
            console.warn("Data aktivitas mingguan kosong atau tidak valid. Grafik tidak dirender.");
            return;
        }

        const paletteLine = {
            ticket: '#1d4ed8', // biru
            room: '#059669', // emerald
            information: '#f59e0b', // amber
        };

        const datasets = [{
                label: 'Tiket',
                data: weekly.ticket,
                borderColor: paletteLine.ticket,
                tension: 0.35,
                pointRadius: 3,
                fill: false,
            },
            {
                label: 'Pemesanan Ruang',
                data: weekly.room,
                borderColor: paletteLine.room,
                tension: 0.35,
                pointRadius: 3,
                fill: false,
            },
            {
                label: 'Informasi',
                data: weekly.information,
                borderColor: paletteLine.information,
                tension: 0.35,
                pointRadius: 3,
                fill: false,
            },
        ];

        __activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: weekly.labels,
                datasets: datasets,
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            callback: function(value) {
                                return Number.isInteger(value) ? value : null;
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)',
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                    },
                },
                interaction: {
                    mode: 'nearest',
                    intersect: false,
                },
            },
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        rebuildActivityChart(getWeeklyData());
        console.log("DOMContentLoaded: Grafik diinisialisasi.");
    });

    document.addEventListener('livewire:navigated', () => {
        rebuildActivityChart(getWeeklyData());
        console.log("livewire:navigated: Grafik diinisialisasi ulang.");
    });

    Livewire.hook('message.processed', (message, component) => {
        if (component.name === 'pages.admin.dashboard') {
            const newData = getWeeklyData();

            if (newData && newData.labels && newData.labels.length > 0) {
                console.log("Livewire Hook: Memperbarui grafik dengan data baru.");
                rebuildActivityChart(newData);
            } else {
                console.warn("Livewire Hook: Tidak ada data yang diterima untuk pembaruan grafik.");
            }
        }
    });
</script>
@endverbatim
</div>