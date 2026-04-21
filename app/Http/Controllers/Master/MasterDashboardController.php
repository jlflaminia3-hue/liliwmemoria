<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class MasterDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $auditLogs = AuditLog::query()
            ->with('user')
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $totalUsers = User::query()->count();
        $totalAdmins = User::query()->whereIn('role', ['admin', 'master_admin'])->count();

        return view('master.dashboard', compact('auditLogs', 'totalUsers', 'totalAdmins'));
    }
}
