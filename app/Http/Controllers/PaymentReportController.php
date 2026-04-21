<?php

namespace App\Http\Controllers;

use App\Models\PaymentInstallment;
use App\Models\PaymentPlan;
use App\Models\PaymentTransaction;
use App\Services\Payments\PaymentPenaltyService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class PaymentReportController extends Controller
{
    public function index(Request $request, PaymentPenaltyService $penalties)
    {
        $from = $request->date('from') ? CarbonImmutable::parse($request->date('from')) : CarbonImmutable::now()->startOfMonth();
        $to = $request->date('to') ? CarbonImmutable::parse($request->date('to')) : CarbonImmutable::now()->endOfMonth();

        $collections = PaymentTransaction::query()
            ->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()])
            ->sum('amount');

        $asOf = CarbonImmutable::today();
        $plans = PaymentPlan::query()
            ->with(['client', 'installments'])
            ->where('status', '!=', 'canceled')
            ->get();

        $overdue = collect();
        $outstandingTotal = 0.0;

        foreach ($plans as $plan) {
            $penalties->recalculate($plan, $asOf);

            $installmentBalance = $plan->installments->sum(fn (PaymentInstallment $i) => $i->installmentBalance());
            $penaltyBalance = $plan->installments->sum(fn (PaymentInstallment $i) => $i->penaltyBalance());
            $outstanding = $installmentBalance + $penaltyBalance;
            $outstandingTotal += $outstanding;

            $overdueInstallments = $plan->installments->filter(function (PaymentInstallment $i) use ($plan, $asOf) {
                if ($i->installmentBalance() <= 0) {
                    return false;
                }
                $effectiveDue = $i->dueDateImmutable()->addDays((int) $plan->penalty_grace_days);

                return $asOf->greaterThan($effectiveDue);
            });

            if ($overdueInstallments->isNotEmpty()) {
                $overdue->push([
                    'plan' => $plan,
                    'count' => $overdueInstallments->count(),
                    'amount' => $overdueInstallments->sum(fn (PaymentInstallment $i) => $i->installmentBalance() + $i->penaltyBalance()),
                ]);
            }
        }

        $overdueAccounts = $overdue->count();

        return view('admin.reports.payments', [
            'from' => $from,
            'to' => $to,
            'collections' => $collections,
            'overdueAccounts' => $overdueAccounts,
            'outstandingTotal' => $outstandingTotal,
            'overdue' => $overdue->sortByDesc('amount')->values(),
        ]);
    }
}
