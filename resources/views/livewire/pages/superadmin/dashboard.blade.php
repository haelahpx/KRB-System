<div class="min-h-screen bg-gray-50" wire:poll.10000ms="tick">
    {{-- Style Variables (Adjusted for consistency, though charts need wide space) --}}
    @php
        $card = 'bg-white rounded-2xl border border-gray-100 shadow-md overflow-hidden'; // Diperbarui
        $label = 'block text-sm font-medium text-gray-700 mb-2';
        $input = 'w-full h-10 px-3 rounded-xl border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition'; // Diperbarui
        $btnBlk = 'px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition h-7';
        $btnRed = 'px-3 py-1.5 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition h-7';
        $btnLite = 'px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900/10 disabled:opacity-60 transition h-7';
        $chip = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-[10px] text-gray-600 font-medium';
        $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md w-fit';
        $ico = 'w-9 h-9 bg-gray-100 text-gray-900 rounded-lg flex items-center justify-center font-semibold text-xs shrink-0 border border-gray-200';
    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-8">

        <div
            class="rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white p-6 sm:p-8 shadow-2xl flex items-center gap-3">
            <x-heroicon-o-chart-bar class="w-8 h-8 text-white/80 shrink-0" />
            <div>
                <h2 class="text-lg sm:text-xl font-semibold">Selamat Datang, {{ $admin_name }}!</h2>
                <p class="text-sm text-white/80">Berikut adalah ringkasan aktivitas tahun ini.</p>
            </div>
        </div>

        <section class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($stats as $s)
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 sm:p-5 text-center">
                    <x-heroicon-o-chart-bar class="w-5 h-5 sm:w-6 sm:h-6 mx-auto mb-1 text-gray-600" />
                    <p class="text-gray-500 text-xs sm:text-sm truncate">{{ $s['label'] }}</p>
                    <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 mt-1 sm:mt-2">{{ number_format($s['value']) }}</h3>
                </div>
            @endforeach
        </section>

        <section class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 sm:p-6">
            <h3 class="flex items-center gap-2 text-base sm:text-lg font-semibold text-gray-900 mb-4">
                <x-heroicon-o-calendar class="w-5 h-5 text-gray-700 shrink-0" />
                Statistik Bulanan
            </h3>
            <div wire:ignore class="relative h-64 sm:h-80">
                <canvas id="bookingChart" class="w-full h-full"></canvas>
                
            </div>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            {{-- Priority Distribution (Bar) --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-2">
                    <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900">
                        <x-heroicon-o-arrow-trending-up class="w-5 h-5 text-gray-700 shrink-0" />
                        Distribusi Prioritas Tiket
                    </h3>
                    <span class="text-xs text-gray-500 shrink-0">Rendah=1, Sedang=2, Tinggi=3</span>
                </div>
                <div wire:ignore class="relative h-64">
                    <canvas id="ticketPriorityBar" class="w-full h-full"></canvas>
                    
                </div>
            </div>

            {{-- Status Distribution (Doughnut) --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 sm:p-6">
                <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900 mb-4">
                    <x-heroicon-o-chart-pie class="w-5 h-5 text-gray-700 shrink-0" />
                    Distribusi Status Tiket
                </h3>
                <div wire:ignore class="relative h-64">
                    <canvas id="ticketStatusPie" class="w-full h-full"></canvas>
                    
                </div>
            </div>

            {{-- Monthly Priority Average (Line) --}}
            <div class="md:col-span-2 lg:col-span-1 bg-white border border-gray-200 rounded-2xl shadow-sm p-4 sm:p-6">
                <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900 mb-4">
                    <x-heroicon-o-presentation-chart-line class="w-5 h-5 text-gray-700 shrink-0" />
                    Rata-rata Prioritas Bulanan (Tahun Ini)
                </h3>
                <div wire:ignore class="relative h-64">
                    <canvas id="ticketPriorityAvg" class="w-full h-full"></canvas>
                    
                </div>
            </div>
        </section>

    </main>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        function buildMonthlyChart() {
            const ctx = document.getElementById('bookingChart')?.getContext('2d');
            if (!ctx) return;
            if (window.__dashboardChart) window.__dashboardChart.destroy();

            const labels = @json($chartData['labels']);
            const room = @json($chartData['room']);
            const vehicle = @json($chartData['vehicle']);
            const ticket = @json($chartData['ticket']);

            window.__dashboardChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        { label: 'Pemesanan Ruangan', data: room, borderColor: '#1d4ed8', backgroundColor: 'rgba(29,78,216,.08)', tension: 0.35, pointRadius: 3 },
                        { label: 'Pemesanan Kendaraan', data: vehicle, borderColor: '#059669', backgroundColor: 'rgba(5,150,105,.08)', tension: 0.35, pointRadius: 3 },
                        { label: 'Tiket Dukungan', data: ticket, borderColor: '#dc2626', backgroundColor: 'rgba(220,38,38,.08)', tension: 0.35, pointRadius: 3 },
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } }, scales: { y: { beginAtZero: true } } }
            });
        }

        function buildTicketCharts() {
            if (window.__ticketPriorityBar) window.__ticketPriorityBar.destroy();
            if (window.__ticketStatusPie) window.__ticketStatusPie.destroy();
            if (window.__ticketPriorityAvg) window.__ticketPriorityAvg.destroy();

            const bar = document.getElementById('ticketPriorityBar')?.getContext('2d');
            if (bar) {
                const data = @json(array_values($ticketCharts['priorityCounts']));
                window.__ticketPriorityBar = new Chart(bar, {
                    type: 'bar',
                    data: { labels: ['Rendah', 'Sedang', 'Tinggi'], datasets: [{ data, backgroundColor: ['#93c5fd', '#fbbf24', '#f87171'] }] },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
                });
            }

            const pie = document.getElementById('ticketStatusPie')?.getContext('2d');
            if (pie) {
                const map = @json($ticketCharts['statusCounts']);
                const data = ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED'].map(k => map[k] || 0);
                window.__ticketStatusPie = new Chart(pie, {
                    type: 'doughnut',
                    data: { labels: ['TERBUKA', 'DALAM PROSES', 'TERSELESAIKAN', 'DITUTUP'], datasets: [{ data, backgroundColor: ['#60a5fa', '#f59e0b', '#10b981', '#9ca3af'] }] },
                    options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, cutout: '55%' }
                });
            }

            // Avg Priority Line
            const avg = document.getElementById('ticketPriorityAvg')?.getContext('2d');
            if (avg) {
                const data = @json($ticketCharts['avgPriority']);
                const labels = @json($ticketCharts['labels']);
                window.__ticketPriorityAvg = new Chart(avg, {
                    type: 'line',
                    data: { labels, datasets: [{ label: 'Rata-rata Prioritas', data, borderColor: '#312e81', backgroundColor: 'rgba(49,46,129,.08)', tension: .35, pointRadius: 3 }] },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { min: 0, max: 3, ticks: { stepSize: 1, callback: (v) => ({ 0: '', 1: 'Rendah', 2: 'Sedang', 3: 'Tinggi' }[v] || v) } } } }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => { buildMonthlyChart(); buildTicketCharts(); });
        document.addEventListener('livewire:load', () => { buildMonthlyChart(); buildTicketCharts(); });
        document.addEventListener('livewire:navigated', () => { buildMonthlyChart(); buildTicketCharts(); });
    </script>
</div>