<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientContract;
use App\Models\Deceased;
use App\Models\Lot;
use App\Models\PaymentPlan;
use App\Models\PaymentTransaction;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        return view('admin.analytics.index');
    }

    public function clients()
    {
        $activeCutoff = now()->subDays(30);
        $inactiveCutoff = now()->subMonths(6);
        $monthStart = CarbonImmutable::today()->startOfMonth();
        $monthEnd = $monthStart->endOfMonth();

        $statsBase = Client::query();
        $stats = [
            'total' => (clone $statsBase)->count(),
            'active' => (clone $statsBase)->where(function ($builder) use ($activeCutoff) {
                $builder
                    ->where('created_at', '>=', $activeCutoff)
                    ->orWhere('updated_at', '>=', $activeCutoff)
                    ->orWhereHas('communications', fn ($q) => $q->where('created_at', '>=', $activeCutoff))
                    ->orWhereHas('reservations', fn ($q) => $q->where('updated_at', '>=', $activeCutoff))
                    ->orWhereHas('maintenanceRecords', fn ($q) => $q->where('updated_at', '>=', $activeCutoff));
            })->count(),
            'new_this_month' => (clone $statsBase)->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            'inactive' => (clone $statsBase)
                ->where('created_at', '<', $inactiveCutoff)
                ->where('updated_at', '<', $inactiveCutoff)
                ->whereDoesntHave('communications', fn ($q) => $q->where('created_at', '>=', $inactiveCutoff))
                ->whereDoesntHave('reservations', fn ($q) => $q->where('updated_at', '>=', $inactiveCutoff))
                ->whereDoesntHave('maintenanceRecords', fn ($q) => $q->where('updated_at', '>=', $inactiveCutoff))
                ->count(),
        ];

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

        $retentionRate = $hasActivity > 0 ? round(($returning / $hasActivity) * 100, 1) : 0.0;

        return view('admin.analytics.clients', compact(
            'stats',
            'growthMonths',
            'growthCounts',
            'topActiveClients',
            'retentionRate',
        ));
    }

    public function plots()
    {
        $lotsTotal = Lot::query()->count();
        $lotsAvailable = Lot::query()->where('status', 'available')->count();
        $lotsReserved = Lot::query()->where('status', 'reserved')->count();
        $lotsOccupied = Lot::query()->where('status', 'occupied')->count();

        $statusLabels = ['Available', 'Reserved', 'Occupied'];
        $statusSeries = [$lotsAvailable, $lotsReserved, $lotsOccupied];

        $sections = Lot::query()
            ->selectRaw("COALESCE(section, 'Unassigned') as section")
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available")
            ->selectRaw("SUM(CASE WHEN status = 'reserved' THEN 1 ELSE 0 END) as reserved")
            ->selectRaw("SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) as occupied")
            ->groupBy(DB::raw("COALESCE(section, 'Unassigned')"))
            ->orderBy('section')
            ->get();

        $sectionCategories = $sections->pluck('section')->values();
        $sectionSeries = [
            ['name' => 'Available', 'data' => $sections->pluck('available')->map(fn ($v) => (int) $v)->values()],
            ['name' => 'Reserved', 'data' => $sections->pluck('reserved')->map(fn ($v) => (int) $v)->values()],
            ['name' => 'Occupied', 'data' => $sections->pluck('occupied')->map(fn ($v) => (int) $v)->values()],
        ];

        return view('admin.analytics.plots', compact(
            'lotsTotal',
            'lotsAvailable',
            'lotsReserved',
            'lotsOccupied',
            'statusLabels',
            'statusSeries',
            'sectionCategories',
            'sectionSeries',
        ));
    }

    public function payments()
    {
        $plansTotal = PaymentPlan::query()->count();
        $plansActive = PaymentPlan::query()->where('status', 'active')->count();
        $plansCanceled = PaymentPlan::query()->where('status', 'canceled')->count();

        $transactionsTotal = PaymentTransaction::query()->count();
        $collectionsTotal = (float) PaymentTransaction::query()->sum('amount');

        $monthStart = CarbonImmutable::today()->startOfMonth();
        $collectionsThisMonth = (float) PaymentTransaction::query()
            ->whereBetween('transaction_date', [$monthStart->toDateString(), $monthStart->endOfMonth()->toDateString()])
            ->sum('amount');

        $months = [];
        $collectionsByMonth = array_fill(0, 12, 0.0);
        for ($i = 11; $i >= 0; $i--) {
            $mStart = CarbonImmutable::today()->startOfMonth()->subMonths($i);
            $mEnd = $mStart->endOfMonth();
            $months[] = $mStart->format('M Y');
            $collectionsByMonth[11 - $i] = (float) PaymentTransaction::query()
                ->whereBetween('transaction_date', [$mStart->toDateString(), $mEnd->toDateString()])
                ->sum('amount');
        }

        $methodData = PaymentTransaction::query()
            ->select('method', DB::raw('COUNT(*) as total'))
            ->groupBy('method')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $methodLabels = $methodData->pluck('method')->values();
        $methodSeries = $methodData->pluck('total')->map(fn ($v) => (int) $v)->values();

        return view('admin.analytics.payments', compact(
            'plansTotal',
            'plansActive',
            'plansCanceled',
            'transactionsTotal',
            'collectionsTotal',
            'collectionsThisMonth',
            'months',
            'collectionsByMonth',
            'methodLabels',
            'methodSeries',
        ));
    }

    public function documents()
    {
        $intermentsTotal = Deceased::query()->count();
        $deathCertCount = Deceased::query()->whereNotNull('death_certificate_path')->where('death_certificate_path', '!=', '')->count();
        $burialPermitCount = Deceased::query()->whereNotNull('burial_permit_path')->where('burial_permit_path', '!=', '')->count();
        $intermentFormCount = Deceased::query()->whereNotNull('interment_form_path')->where('interment_form_path', '!=', '')->count();

        $complianceReadyCount = Deceased::query()
            ->whereNotNull('client_id')
            ->whereNotNull('burial_date')
            ->whereNotNull('death_certificate_path')
            ->whereNotNull('burial_permit_path')
            ->count();

        $missingComplianceCount = Deceased::query()
            ->where(function ($builder) {
                $builder
                    ->whereNull('client_id')
                    ->orWhereNull('burial_date')
                    ->orWhereNull('death_certificate_path')
                    ->orWhere(function ($confirmed) {
                        $confirmed
                            ->where('status', 'confirmed')
                            ->whereNull('burial_permit_path');
                    });
            })
            ->count();

        $contractPdfCount = ClientContract::query()->whereNotNull('pdf_path')->where('pdf_path', '!=', '')->count();
        $reservationContractCount = DB::table('reservations')->whereNotNull('contract_path')->where('contract_path', '!=', '')->count();
        $receiptCount = PaymentTransaction::query()->whereNotNull('receipt_path')->where('receipt_path', '!=', '')->count();

        $documentTypeLabels = ['Death Certificates', 'Burial Permits', 'Interment Forms', 'Contract PDFs', 'Reservation Contracts', 'Payment Receipts'];
        $documentTypeSeries = [
            $deathCertCount,
            $burialPermitCount,
            $intermentFormCount,
            $contractPdfCount,
            $reservationContractCount,
            $receiptCount,
        ];

        $topMissing = Deceased::query()
            ->with(['client:id,first_name,last_name', 'lot:id,lot_number,section,name'])
            ->where(function ($builder) {
                $builder
                    ->whereNull('client_id')
                    ->orWhereNull('burial_date')
                    ->orWhereNull('death_certificate_path')
                    ->orWhere(function ($confirmed) {
                        $confirmed
                            ->where('status', 'confirmed')
                            ->whereNull('burial_permit_path');
                    });
            })
            ->orderByRaw('CASE WHEN burial_date IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('burial_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('admin.analytics.documents', compact(
            'intermentsTotal',
            'deathCertCount',
            'burialPermitCount',
            'intermentFormCount',
            'complianceReadyCount',
            'missingComplianceCount',
            'contractPdfCount',
            'reservationContractCount',
            'receiptCount',
            'documentTypeLabels',
            'documentTypeSeries',
            'topMissing',
        ));
    }

    public function interments()
    {
        $total = Deceased::query()->count();
        $pending = Deceased::query()->where('status', 'pending')->count();
        $confirmed = Deceased::query()->where('status', 'confirmed')->count();
        $exhumed = Deceased::query()->where('status', 'exhumed')->count();

        $missingDocs = Deceased::query()->where(function ($builder) {
            $builder
                ->whereNull('client_id')
                ->orWhereNull('burial_date')
                ->orWhereNull('death_certificate_path')
                ->orWhere(function ($confirmed) {
                    $confirmed
                        ->where('status', 'confirmed')
                        ->whereNull('burial_permit_path');
                });
        })->count();

        $statusLabels = ['Pending', 'Confirmed', 'Exhumed'];
        $statusSeries = [$pending, $confirmed, $exhumed];

        $months = [];
        $burialsByMonth = [];
        for ($i = 11; $i >= 0; $i--) {
            $mStart = CarbonImmutable::today()->startOfMonth()->subMonths($i);
            $mEnd = $mStart->endOfMonth();
            $months[] = $mStart->format('M Y');
            $burialsByMonth[] = Deceased::query()
                ->whereNotNull('burial_date')
                ->whereBetween('burial_date', [$mStart->toDateString(), $mEnd->toDateString()])
                ->count();
        }

        $recent = Deceased::query()
            ->with(['client:id,first_name,last_name', 'lot:id,lot_number,section,name'])
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('admin.analytics.interments', compact(
            'total',
            'pending',
            'confirmed',
            'exhumed',
            'missingDocs',
            'statusLabels',
            'statusSeries',
            'months',
            'burialsByMonth',
            'recent',
        ));
    }

    public function visitors()
    {
        return view('admin.analytics.visitors');
    }
}
