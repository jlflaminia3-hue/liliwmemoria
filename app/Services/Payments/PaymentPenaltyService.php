<?php

namespace App\Services\Payments;

use App\Models\PaymentInstallment;
use App\Models\PaymentPlan;
use Carbon\CarbonImmutable;

class PaymentPenaltyService
{
    /**
     * Penalty model:
     * - If (asOf > due_date + grace) and installment has unpaid balance,
     *   penalty accrues per 30 days overdue: unpaid_balance * penalty_rate_percent * months_overdue.
     */
    public function recalculate(PaymentPlan $plan, CarbonImmutable $asOf): void
    {
        $ratePercent = (float) $plan->penalty_rate_percent;
        if ($ratePercent <= 0) {
            return;
        }

        $graceDays = (int) $plan->penalty_grace_days;

        $installments = $plan->relationLoaded('installments')
            ? $plan->installments
            : $plan->installments()->orderBy('sequence')->get();

        $installments->each(function (PaymentInstallment $installment) use ($asOf, $ratePercent, $graceDays) {
            $unpaid = $installment->installmentBalance();
            if ($unpaid <= 0) {
                if ((float) $installment->penalty_paid <= 0) {
                    $installment->penalty_accrued = 0;
                    $installment->save();
                }

                return;
            }

            $effectiveDue = $installment->dueDateImmutable()->addDays($graceDays);
            if ($asOf->lessThanOrEqualTo($effectiveDue)) {
                $installment->penalty_accrued = max((float) $installment->penalty_paid, 0);
                $installment->save();

                return;
            }

            $daysOverdue = $effectiveDue->diffInDays($asOf);
            $monthsOverdue = (int) ceil($daysOverdue / 30);
            $computed = round($unpaid * ($ratePercent / 100.0) * $monthsOverdue, 2);

            $installment->penalty_accrued = max((float) $installment->penalty_paid, $computed);
            $installment->save();
        });
    }
}
