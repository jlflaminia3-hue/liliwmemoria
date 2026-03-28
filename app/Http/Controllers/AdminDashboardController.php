<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientContract;
use App\Models\ClientLotOwnership;
use App\Models\Deceased;
use App\Models\Lot;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        $today = Carbon::today();

        $lotsTotal = Lot::query()->count();
        $lotsAvailable = Lot::query()->where('status', 'available')->count();
        $lotsReserved = Lot::query()->where('status', 'reserved')->count();
        $lotsOccupied = Lot::query()->where('status', 'occupied')->count();

        $clientsTotal = Client::query()->count();
        $deceasedTotal = Deceased::query()->count();

        $contractsActive = ClientContract::query()->where('status', 'active')->count();
        $contractsPastDue = ClientContract::query()->where('status', 'past_due')->count();

        $outstandingBalance = (float) ClientContract::query()
            ->whereIn('status', ['active', 'past_due'])
            ->selectRaw('COALESCE(SUM(COALESCE(total_amount, 0) - COALESCE(amount_paid, 0)), 0) as balance')
            ->value('balance');

        $upcomingBurials = Deceased::query()
            ->with('lot')
            ->whereNotNull('burial_date')
            ->whereDate('burial_date', '>=', $today)
            ->orderBy('burial_date')
            ->limit(8)
            ->get();

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

        $pastDueContracts = ClientContract::query()
            ->with(['client', 'lot'])
            ->where('status', 'past_due')
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

        return view('admin.index', compact(
            'lotsTotal',
            'lotsAvailable',
            'lotsReserved',
            'lotsOccupied',
            'clientsTotal',
            'deceasedTotal',
            'contractsActive',
            'contractsPastDue',
            'outstandingBalance',
            'upcomingBurials',
            'recentInterments',
            'recentReservations',
            'pastDueContracts',
            'lotsBySection',
        ));
    }
}

