<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\ClientContract;
use App\Models\ClientLotOwnership;
use App\Models\Deceased;
use App\Models\IntermentPayment;
use App\Models\Lot;
use App\Models\PaymentTransaction;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        $today = Carbon::today();
        $activeCutoff = now()->subDays(30);
        $inactiveCutoff = now()->subMonths(6);
        $monthStart = CarbonImmutable::today()->startOfMonth();
        $monthEnd = $monthStart->endOfMonth();

        $lotsTotal = Lot::query()->count();
        $lotsAvailable = Lot::query()->where('status', 'available')->count();
        $lotsReserved = Lot::query()->where('status', 'reserved')->count();
        $lotsOccupied = Lot::query()->where('status', 'occupied')->count();

        $clientsTotal = Client::query()->count();
        $deceasedTotal = Deceased::query()->count();

        $clientsActive = Client::query()
            ->where(function ($builder) use ($activeCutoff) {
                $builder
                    ->where('created_at', '>=', $activeCutoff)
                    ->orWhere('updated_at', '>=', $activeCutoff)
                    ->orWhereHas('communications', fn ($q) => $q->where('created_at', '>=', $activeCutoff))
                    ->orWhereHas('reservations', fn ($q) => $q->where('updated_at', '>=', $activeCutoff))
                    ->orWhereHas('maintenanceRecords', fn ($q) => $q->where('updated_at', '>=', $activeCutoff));
            })
            ->count();

        $clientsNewThisMonth = Client::query()->whereBetween('created_at', [$monthStart, $monthEnd])->count();

        $clientsInactive = Client::query()
            ->where('created_at', '<', $inactiveCutoff)
            ->where('updated_at', '<', $inactiveCutoff)
            ->whereDoesntHave('communications', fn ($q) => $q->where('created_at', '>=', $inactiveCutoff))
            ->whereDoesntHave('reservations', fn ($q) => $q->where('updated_at', '>=', $inactiveCutoff))
            ->whereDoesntHave('maintenanceRecords', fn ($q) => $q->where('updated_at', '>=', $inactiveCutoff))
            ->count();

        $contractsActive = ClientContract::query()->where('status', 'active')->count();
        $contractsPastDue = ClientContract::query()->where('status', 'pending')->count();

        $outstandingBalance = (float) ClientContract::query()
            ->whereIn('status', ['active', 'pending'])
            ->selectRaw('COALESCE(SUM(COALESCE(total_amount, 0) - COALESCE(amount_paid, 0)), 0) as balance')
            ->value('balance');

        $contractsCollectible = (float) ClientContract::query()
            ->whereIn('status', ['active', 'pending'])
            ->selectRaw('COALESCE(SUM(COALESCE(total_amount, 0)), 0) as total')
            ->value('total');

        $contractsPaid = (float) ClientContract::query()
            ->whereIn('status', ['active', 'pending'])
            ->selectRaw('COALESCE(SUM(COALESCE(amount_paid, 0)), 0) as paid')
            ->value('paid');

        $upcomingBurials = Deceased::query()
            ->with('lot')
            ->whereNotNull('burial_date')
            ->whereDate('burial_date', '>=', $today)
            ->orderBy('burial_date')
            ->limit(8)
            ->get();

        $upcomingIntermentsCount = Deceased::query()
            ->whereNotNull('burial_date')
            ->whereDate('burial_date', '>=', $today)
            ->count();

        $intermentsThisMonth = Deceased::query()
            ->whereNotNull('burial_date')
            ->whereBetween('burial_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->count();

        $recentInterments = Deceased::query()
            ->with('lot')
            ->latest()
            ->limit(8)
            ->get();

        $recentReservations = ClientLotOwnership::query()
            ->with(['client', 'lot'])
            ->latest()
            ->limit(8)
            ->get();

        $avgReservationDays = (float) Reservation::query()
            ->whereNotNull('reserved_at')
            ->whereNotNull('expires_at')
            ->where('status', Reservation::STATUS_ACTIVE)
            ->selectRaw('AVG(DATEDIFF(expires_at, reserved_at)) as avg_days')
            ->value('avg_days');

        $avgReservationDays = max(0.0, $avgReservationDays);

        $pastDueContracts = ClientContract::query()
            ->with(['client', 'lot'])
            ->where('status', 'pending')
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END, due_date ASC')
            ->limit(8)
            ->get();

        $lotsBySection = Lot::query()
            ->selectRaw("COALESCE(section, 'Unassigned') as section")
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available")
            ->selectRaw("SUM(CASE WHEN status = 'reserved' THEN 1 ELSE 0 END) as reserved")
            ->selectRaw("SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) as occupied")
            ->groupBy(DB::raw("COALESCE(section, 'Unassigned')"))
            ->orderBy('section')
            ->get();

        $growthMonths = [];
        $growthCounts = [];
        for ($i = 11; $i >= 0; $i--) {
            $mStart = CarbonImmutable::today()->startOfMonth()->subMonths($i);
            $mEnd = $mStart->endOfMonth();
            $growthMonths[] = $mStart->format('M Y');
            $growthCounts[] = Client::query()->whereBetween('created_at', [$mStart, $mEnd])->count();
        }

        $insightSince = now()->subMonths(6);
        $topActiveClients = Client::query()
            ->select(['clients.id', 'clients.first_name', 'clients.last_name'])
            ->selectSub(function ($q) use ($insightSince) {
                $q->from('reservations')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('reservations.client_id', 'clients.id')
                    ->where('reservations.updated_at', '>=', $insightSince);
            }, 'recent_reservations')
            ->selectSub(function ($q) use ($insightSince) {
                $q->from('client_communications')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('client_communications.client_id', 'clients.id')
                    ->where('client_communications.created_at', '>=', $insightSince);
            }, 'recent_communications')
            ->selectSub(function ($q) use ($insightSince) {
                $q->from('maintenance_records')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('maintenance_records.client_id', 'clients.id')
                    ->where('maintenance_records.updated_at', '>=', $insightSince);
            }, 'recent_maintenance')
            ->get()
            ->map(function ($client) {
                $client->activity_score = (int) ($client->recent_reservations ?? 0)
                    + (int) ($client->recent_communications ?? 0)
                    + (int) ($client->recent_maintenance ?? 0);

                return $client;
            })
            ->sortByDesc('activity_score')
            ->take(10)
            ->values();

        $hasActivity = Client::query()
            ->where(function ($q) {
                $q->whereHas('reservations')
                    ->orWhereHas('communications')
                    ->orWhereHas('maintenanceRecords');
            })
            ->count();

        $returning = Client::query()
            ->whereRaw('(SELECT COUNT(*) FROM reservations WHERE reservations.client_id = clients.id) >= 2')
            ->orWhereRaw('(SELECT COUNT(*) FROM client_communications WHERE client_communications.client_id = clients.id) >= 2')
            ->orWhereRaw('(SELECT COUNT(*) FROM maintenance_records WHERE maintenance_records.client_id = clients.id) >= 2')
            ->count();

        $retentionRate = $hasActivity > 0
            ? (int) round(($returning / $hasActivity) * 100)
            : 0;

        $paymentMonths = [];
        $paymentRevenue = array_fill(0, 12, 0.0);
        $intermentRevenue = array_fill(0, 12, 0.0);
        for ($i = 11; $i >= 0; $i--) {
            $mStart = CarbonImmutable::today()->startOfMonth()->subMonths($i);
            $mEnd = $mStart->endOfMonth();
            $paymentMonths[] = $mStart->format('M Y');
            $paymentRevenue[11 - $i] = (float) PaymentTransaction::query()
                ->whereBetween('transaction_date', [$mStart->toDateString(), $mEnd->toDateString()])
                ->sum('amount');
            $intermentRevenue[11 - $i] = (float) IntermentPayment::query()
                ->whereBetween('payment_date', [$mStart->toDateString(), $mEnd->toDateString()])
                ->sum('amount');
        }

        $recentPaymentTransactions = PaymentTransaction::query()
            ->with('client:id,first_name,last_name')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $recentAuditLogs = AuditLog::query()
            ->with('user:id,name')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.index', compact(
            'lotsTotal',
            'lotsAvailable',
            'lotsReserved',
            'lotsOccupied',
            'clientsTotal',
            'clientsActive',
            'clientsInactive',
            'clientsNewThisMonth',
            'deceasedTotal',
            'contractsActive',
            'contractsPastDue',
            'outstandingBalance',
            'contractsCollectible',
            'contractsPaid',
            'upcomingBurials',
            'upcomingIntermentsCount',
            'intermentsThisMonth',
            'recentInterments',
            'recentReservations',
            'avgReservationDays',
            'pastDueContracts',
            'lotsBySection',
            'growthMonths',
            'growthCounts',
            'retentionRate',
            'topActiveClients',
            'paymentMonths',
            'paymentRevenue',
            'intermentRevenue',
            'recentPaymentTransactions',
            'recentAuditLogs',
        ));
    }
}
