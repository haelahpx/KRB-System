<div class="bg-gray-50 min-h-screen" wire:poll.2000ms.keep-alive wire:key="admin-dashboard-page" id="admin-dashboard-root" data-weekly-activity='@json($weeklyActivityData ?? [])'>
    @php
        // LAYOUT HELPERS
        $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
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
                <div class="flex items-start gap-4 sm:gap-6">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20 shrink-0">
                        <x-heroicon-o-cube class="w-6 h-6 text-white" />
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                            <div class="space-y-1.5">
                                <h2 class="text-xl sm:text-2xl font-semibold leading-tight">
                                    Dashboard Admin
                                </h2>

                                <div class="text-sm text-white/80 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                                    <span>Departemen: <span class="font-semibold">{{ $departmentList }}</span></span>
                                </div>

                                <p class="text-xs text-white/60 pt-1 sm:pt-0">
                                    Menampilkan statistik utama dan aktivitas mingguan sistem manajemen tiket.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STATISTICS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Total Tiket --}}
            <div class="{{ $card }}">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm text-gray-600 mb-1">Tiket Dibuat (7 hari)</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $weeklyTicketsCount }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Status vs minggu lalu</p>
                        </div>
                        <div class="p-3 bg-gray-100 rounded-lg shrink-0">
                            <x-heroicon-o-ticket class="w-6 h-6 text-gray-700" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Pemesanan Ruangan --}}
            <div class="{{ $card }}">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm text-gray-600 mb-1">Pemesanan Ruang (7 hari)</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $weeklyRoomBookingsCount }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Status vs minggu lalu</p>
                        </div>
                        <div class="p-3 bg-gray-100 rounded-lg shrink-0">
                            <x-heroicon-o-calendar-days class="w-6 h-6 text-gray-700" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Entri Informasi --}}
            <div class="{{ $card }}">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm text-gray-600 mb-1">Entri Informasi (7 hari)</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $weeklyInformationCount }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Status vs minggu lalu</p>
                        </div>
                        <div class="p-3 bg-gray-100 rounded-lg shrink-0">
                            <x-heroicon-o-document-text class="w-6 h-6 text-gray-700" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Agen Terbaik --}}
            <div class="{{ $card }}">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-600 mb-1">Agen Terbaik (Tiket Selesai)</p>
                            <p class="text-xl font-bold text-gray-900 truncate" title="{{ $topAgent->full_name ?? 'N/A' }}">
                                {{ $topAgent->full_name ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-700 mt-1">
                                Selesai: <span class="font-semibold text-gray-900">{{ $topAgent->solved_count ?? 0 }}</span>
                            </p>
                        </div>
                        <div class="p-3 bg-amber-100 rounded-lg shrink-0">
                            <x-heroicon-o-star class="w-6 h-6 text-amber-700" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CHARTS SECTION --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Weekly Activity Chart --}}
            <div class="lg:col-span-2 {{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-chart-bar class="w-5 h-5 text-gray-700" />
                            <div>
                                <h3 class="{{ $titleC }}">Aktivitas Mingguan</h3>
                                <p class="text-xs text-gray-500">Tiket / Pemesanan Ruang / Informasi</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">
                            7 hari terakhir (Data Real-Time)
                        </p>
                    </div>
                </div>

                <div class="p-5">
                    {{-- wire:ignore sangat penting untuk mencegah Livewire me-render ulang canvas --}}
                    <div class="h-[320px]" wire:ignore>
                        <canvas id="activityChart" class="w-full" style="max-height:320px"></canvas>
                    </div>
                </div>
            </div>

            {{-- Priority Distribution --}}
            <div class="{{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-flag class="w-5 h-5 text-gray-700" />
                        <div>
                            <h3 class="{{ $titleC }}">Distribusi Prioritas Tiket</h3>
                            <p class="text-xs text-gray-500">Bulan ini</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 space-y-4">
                    {{-- Prioritas Tinggi --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Prioritas Tinggi</span>
                            <span class="text-sm font-bold text-gray-900">{{ $highPercent }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-red-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ max(0, min(100, $highPercent)) }}%"></div>
                        </div>
                    </div>

                    {{-- Prioritas Sedang --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Prioritas Sedang</span>
                            <span class="text-sm font-bold text-gray-900">{{ $mediumPercent }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-yellow-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ max(0, min(100, $mediumPercent)) }}%"></div>
                        </div>
                    </div>

                    {{-- Prioritas Rendah --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Prioritas Rendah</span>
                            <span class="text-sm font-bold text-gray-900">{{ $lowPercent }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-green-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ max(0, min(100, $lowPercent)) }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="px-5 pb-5">
                    <div class="p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-600 mb-1">Total Tiket Bulan Ini</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalTicketsThisMonth }}</p>
                        <p class="text-xs text-gray-600 mt-1">Semua status</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Chart.js v4 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

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

            const datasets = [
                {
                    label: 'Tiket',
                    data: weekly.ticket,
                    borderColor: paletteLine.ticket,
                    backgroundColor: paletteLine.ticket + '20',
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: paletteLine.ticket,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    fill: false,
                    borderWidth: 3,
                },
                {
                    label: 'Pemesanan Ruang',
                    data: weekly.room,
                    borderColor: paletteLine.room,
                    backgroundColor: paletteLine.room + '20',
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: paletteLine.room,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    fill: false,
                    borderWidth: 3,
                },
                {
                    label: 'Informasi',
                    data: weekly.information,
                    borderColor: paletteLine.information,
                    backgroundColor: paletteLine.information + '20',
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: paletteLine.information,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    fill: false,
                    borderWidth: 3,
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
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 12,
                                    weight: '500'
                                }
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(17, 24, 39, 0.95)',
                            padding: 12,
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            titleFont: {
                                size: 13,
                                weight: '600'
                            },
                            bodyFont: {
                                size: 12
                            },
                            bodySpacing: 6,
                            usePointStyle: true,
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
                                },
                                font: {
                                    size: 11
                                },
                                color: '#6b7280'
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.04)',
                                drawBorder: false,
                            },
                            border: {
                                display: false
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 11
                                },
                                color: '#6b7280'
                            },
                            grid: {
                                display: false,
                            },
                            border: {
                                display: false
                            }
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