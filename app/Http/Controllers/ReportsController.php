<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Lot;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
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

        $rows = Client::query()
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('admin.reports.clients', compact('stats', 'rows'));
    }

    public function plots()
    {
        $lotsTotal = Lot::query()->count();
        $lotsAvailable = Lot::query()->where('status', 'available')->count();
        $lotsReserved = Lot::query()->where('status', 'reserved')->count();
        $lotsOccupied = Lot::query()->where('status', 'occupied')->count();

        $sections = Lot::query()
            ->selectRaw("COALESCE(section, 'Unassigned') as section")
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available")
            ->selectRaw("SUM(CASE WHEN status = 'reserved' THEN 1 ELSE 0 END) as reserved")
            ->selectRaw("SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) as occupied")
            ->groupBy(DB::raw("COALESCE(section, 'Unassigned')"))
            ->orderBy('section')
            ->get();

        return view('admin.reports.plots', compact(
            'lotsTotal',
            'lotsAvailable',
            'lotsReserved',
            'lotsOccupied',
            'sections',
        ));
    }

    public function payments()
    {
        return redirect()->route('admin.reports.payments');
    }
}
