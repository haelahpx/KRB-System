<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\BookingRoom;
use App\Models\VehicleBooking;
use App\Models\Guestbook;
use App\Models\Delivery;

#[Layout('layouts.receptionist')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    protected string $tz = 'Asia/Jakarta';

    private function asCarbon(null|Carbon|\DateTimeInterface|string $v): ?Carbon
    {
        if ($v === null) return null;
        if ($v instanceof Carbon) return $v->timezone($this->tz);
        if ($v instanceof \DateTimeInterface) return Carbon::instance($v)->timezone($this->tz);

        try {
            return Carbon::parse($v)->timezone($this->tz);
        } catch (\Throwable) {
            return null;
        }
    }

    private function fmtDate(null|Carbon|\DateTimeInterface|string $v, string $fmt = 'd M Y'): string
    {
        $c = $this->asCarbon($v);
        return $c ? $c->format($fmt) : '—';
    }

    private function fmtTime(null|Carbon|\DateTimeInterface|string $v, string $fmt = 'H.i'): string
    {
        $c = $this->asCarbon($v);
        return $c ? $c->format($fmt) : '—';
    }

    private function wowPct(int $current, int $prev): int
    {
        if ($prev <= 0) return $current > 0 ? 100 : 0;
        return (int) round((($current - $prev) / $prev) * 100);
    }

    private function groupCountByDate(string $modelClass, ?int $companyId, Carbon $start, Carbon $end, string $dateColumn = 'created_at'): array
    {
        /** @var \Illuminate\Database\Eloquent\Model $modelClass */
        return $modelClass::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->whereBetween($dateColumn, [$start, $end])
            ->selectRaw("DATE($dateColumn) as d, COUNT(*) as c")
            ->groupBy('d')
            ->pluck('c', 'd')
            ->toArray();
    }

    private function makeDailySeries(array $days, array $mapByDate): array
    {
        return array_map(function (Carbon $day) use ($mapByDate) {
            $key = $day->toDateString();
            return (int) ($mapByDate[$key] ?? 0);
        }, $days);
    }

    public function render()
    {
        $companyId = optional(Auth::user())->company_id;

        // 7 hari terakhir (hari ini + 6 hari ke belakang)
        $startOfRange = Carbon::now($this->tz)->subDays(6)->startOfDay();
        $endOfRange   = Carbon::now($this->tz)->endOfDay();

        // 7 hari sebelumnya (untuk % vs last week)
        $prevStart = $startOfRange->copy()->subDays(7)->startOfDay();
        $prevEnd   = $startOfRange->copy()->subDay()->endOfDay();

        // Days array
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = $startOfRange->copy()->addDays($i);
        }

        /**
         * Weekly totals (7 hari terakhir)
         */
        $weeklyRoomBookingsCount = BookingRoom::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->whereBetween('created_at', [$startOfRange, $endOfRange])
            ->count();

        $weeklyVehicleBookingsCount = VehicleBooking::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->whereBetween('created_at', [$startOfRange, $endOfRange])
            ->count();

        $weeklyGuestsCount = Guestbook::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->whereBetween('created_at', [$startOfRange, $endOfRange])
            ->count();

        $weeklyDocsCount = Delivery::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->whereBetween('created_at', [$startOfRange, $endOfRange])
            ->count();

        /**
         * Previous week totals (untuk %)
         */
        $prevRoom = BookingRoom::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->count();

        $prevVehicle = VehicleBooking::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->count();

        $prevGuest = Guestbook::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->count();

        $prevDoc = Delivery::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->count();

        $roomWowPct    = $this->wowPct($weeklyRoomBookingsCount, $prevRoom);
        $vehicleWowPct = $this->wowPct($weeklyVehicleBookingsCount, $prevVehicle);
        $guestWowPct   = $this->wowPct($weeklyGuestsCount, $prevGuest);
        $docWowPct     = $this->wowPct($weeklyDocsCount, $prevDoc);

        /**
         * Weekly chart activity (REAL)
         */
        $labels = array_map(fn(Carbon $d) => $d->format('D'), $days);

        $roomByDate = $this->groupCountByDate(BookingRoom::class, $companyId, $startOfRange, $endOfRange, 'created_at');
        $vehByDate  = $this->groupCountByDate(VehicleBooking::class, $companyId, $startOfRange, $endOfRange, 'created_at');
        $docByDate  = $this->groupCountByDate(Delivery::class, $companyId, $startOfRange, $endOfRange, 'created_at');
        $gstByDate  = $this->groupCountByDate(Guestbook::class, $companyId, $startOfRange, $endOfRange, 'created_at');

        $weeklyActivity = [
            'labels'  => $labels,
            'room'    => $this->makeDailySeries($days, $roomByDate),
            'vehicle' => $this->makeDailySeries($days, $vehByDate),
            'docpac'  => $this->makeDailySeries($days, $docByDate),
            'guest'   => $this->makeDailySeries($days, $gstByDate),
        ];

        /**
         * Status Distribution (This Month) - gabungan BookingRoom + VehicleBooking
         */
        $now = Carbon::now($this->tz);
        $monthStart = $now->copy()->startOfMonth()->startOfDay();
        $monthEnd   = $now->copy()->endOfMonth()->endOfDay();

        $lastMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth()->startOfDay();
        $lastMonthEnd   = $now->copy()->subMonthNoOverflow()->endOfMonth()->endOfDay();

        $approvedStatuses = ['approved', 'accept', 'accepted'];
        $pendingStatuses  = ['pending', 'waiting', 'submitted'];
        $rejectedStatuses = ['rejected', 'declined', 'cancelled', 'canceled'];

        $countStatus = function (string $model, array $statuses, Carbon $from, Carbon $to) use ($companyId) {
            return $model::query()
                ->when($companyId, fn($q) => $q->where('company_id', $companyId))
                ->whereBetween('created_at', [$from, $to])
                ->whereIn(\DB::raw('LOWER(status)'), $statuses)
                ->count();
        };

        $approvedCount = $countStatus(BookingRoom::class, $approvedStatuses, $monthStart, $monthEnd)
            + $countStatus(VehicleBooking::class, $approvedStatuses, $monthStart, $monthEnd);

        $pendingCount = $countStatus(BookingRoom::class, $pendingStatuses, $monthStart, $monthEnd)
            + $countStatus(VehicleBooking::class, $pendingStatuses, $monthStart, $monthEnd);

        $rejectedCount = $countStatus(BookingRoom::class, $rejectedStatuses, $monthStart, $monthEnd)
            + $countStatus(VehicleBooking::class, $rejectedStatuses, $monthStart, $monthEnd);

        $totalRequestsThisMonth =
            BookingRoom::query()->when($companyId, fn($q) => $q->where('company_id', $companyId))
                ->whereBetween('created_at', [$monthStart, $monthEnd])->count()
            + VehicleBooking::query()->when($companyId, fn($q) => $q->where('company_id', $companyId))
                ->whereBetween('created_at', [$monthStart, $monthEnd])->count();

        $totalRequestsLastMonth =
            BookingRoom::query()->when($companyId, fn($q) => $q->where('company_id', $companyId))
                ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count()
            + VehicleBooking::query()->when($companyId, fn($q) => $q->where('company_id', $companyId))
                ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();

        $statusApprovedPct = $totalRequestsThisMonth > 0 ? (int) round(($approvedCount / $totalRequestsThisMonth) * 100) : 0;
        $statusPendingPct  = $totalRequestsThisMonth > 0 ? (int) round(($pendingCount / $totalRequestsThisMonth) * 100) : 0;

        // biar total 100% rapih
        $statusRejectedPct = max(0, 100 - $statusApprovedPct - $statusPendingPct);

        $monthVsLastMonthPct = $this->wowPct($totalRequestsThisMonth, $totalRequestsLastMonth);

        /**
         * Newest lists (punyamu tetap)
         */
        $latestBookingRooms = BookingRoom::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(fn($br) => [
                'id' => $br->bookingroom_id,
                'title' => $br->meeting_title ?? '—',
                'room_id' => $br->room_id,
                'time' => $this->fmtTime($br->start_time) . ' - ' . $this->fmtTime($br->end_time),
                'date' => $this->fmtDate($br->date),
                'status' => ucfirst($br->status ?? '—'),
            ]);

        $latestVehicleBookings = VehicleBooking::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(fn($vb) => [
                'id' => $vb->vehiclebooking_id,
                'borrower' => $vb->borrower_name ?? '—',
                'purpose' => $vb->purpose ?? '—',
                'destination' => $vb->destination ?? '—',
                'time' => $this->fmtTime($vb->start_at) . ' - ' . $this->fmtTime($vb->end_at),
                'status' => ucfirst($vb->status ?? '—'),
            ]);

        $latestGuests = Guestbook::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(fn($g) => [
                'id' => $g->guestbook_id,
                'name' => $g->name ?? '—',
                'purpose' => $g->keperluan ?? '—',
                'time_in' => $this->fmtTime($g->jam_in),
                'date' => $this->fmtDate($g->date),
            ]);

        $latestDocs = Delivery::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(fn($d) => [
                'id' => $d->delivery_id,
                'item' => $d->item_name ?? '—',
                'type' => ucfirst($d->type ?? '—'),
                'status' => ucfirst($d->status ?? '—'),
                'direction' => ucfirst($d->direction ?? '—'),
                'created' => $this->fmtDate($d->created_at),
            ]);

        return view('livewire.pages.receptionist.dashboard', [
            'latestBookingRooms' => $latestBookingRooms,
            'latestVehicleBookings' => $latestVehicleBookings,
            'latestGuests' => $latestGuests,
            'latestDocs' => $latestDocs,

            'weeklyRoomBookingsCount' => $weeklyRoomBookingsCount,
            'weeklyVehicleBookingsCount' => $weeklyVehicleBookingsCount,
            'weeklyGuestsCount' => $weeklyGuestsCount,
            'weeklyDocsCount' => $weeklyDocsCount,

            'roomWowPct' => $roomWowPct,
            'vehicleWowPct' => $vehicleWowPct,
            'guestWowPct' => $guestWowPct,
            'docWowPct' => $docWowPct,

            'weeklyActivity' => $weeklyActivity,

            'statusApprovedPct' => $statusApprovedPct,
            'statusPendingPct' => $statusPendingPct,
            'statusRejectedPct' => $statusRejectedPct,
            'totalRequestsThisMonth' => $totalRequestsThisMonth,
            'monthVsLastMonthPct' => $monthVsLastMonthPct,
        ]);
    }
}
