<?php

namespace App\Services\Payments;

use App\Models\PaymentInstallment;
use App\Models\PaymentPlan;
use App\Models\PaymentTransaction;
use App\Models\PaymentTransactionAllocation;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class PaymentAllocator
{
    public function __construct(
        private readonly PaymentPenaltyService $penalties,
    ) {}

    /**
     * Applies a transaction amount in this order:
     * 1) Penalties (oldest first)
     * 2) Installment balances (oldest first)
     * Remainder becomes unapplied_amount.
     */
    public function allocate(PaymentPlan $plan, PaymentTransaction $transaction): void
    {
        DB::transaction(function () use ($plan, $transaction) {
            $asOf = CarbonImmutable::parse($transaction->transaction_date);
            $this->penalties->recalculate($plan, $asOf);

            $remaining = (float) $transaction->amount;
            $installments = $plan->installments()->orderBy('sequence')->lockForUpdate()->get();

            foreach ($installments as $installment) {
                if ($remaining <= 0) {
                    break;
                }

                $remaining = $this->applyPenalty($transaction, $installment, $remaining);
                if ($remaining <= 0) {
                    break;
                }

                $remaining = $this->applyInstallment($transaction, $installment, $remaining);

                $this->refreshInstallmentStatus($plan, $installment, $asOf);
            }

            if ($remaining > 0) {
                PaymentTransactionAllocation::create([
                    'payment_transaction_id' => $transaction->id,
                    'payment_installment_id' => null,
                    'type' => 'unapplied',
                    'amount_applied' => round($remaining, 2),
                ]);
            }

            $transaction->unapplied_amount = round($remaining, 2);
            $transaction->save();

            $this->refreshPlanStatus($plan);
        });
    }

    private function applyPenalty(PaymentTransaction $transaction, PaymentInstallment $installment, float $remaining): float
    {
        $due = $installment->penaltyBalance();
        if ($due <= 0) {
            return $remaining;
        }

        $applied = min($remaining, $due);
        if ($applied <= 0) {
            return $remaining;
        }

        $installment->penalty_paid = round((float) $installment->penalty_paid + $applied, 2);
        $installment->save();

        PaymentTransactionAllocation::create([
            'payment_transaction_id' => $transaction->id,
            'payment_installment_id' => $installment->id,
            'type' => 'penalty',
            'amount_applied' => round($applied, 2),
        ]);

        return $remaining - $applied;
    }

    private function applyInstallment(PaymentTransaction $transaction, PaymentInstallment $installment, float $remaining): float
    {
        $due = $installment->installmentBalance();
        if ($due <= 0) {
            return $remaining;
        }

        $applied = min($remaining, $due);
        if ($applied <= 0) {
            return $remaining;
        }

        $installment->amount_paid = round((float) $installment->amount_paid + $applied, 2);
        $installment->save();

        PaymentTransactionAllocation::create([
            'payment_transaction_id' => $transaction->id,
            'payment_installment_id' => $installment->id,
            'type' => 'installment',
            'amount_applied' => round($applied, 2),
        ]);

        return $remaining - $applied;
    }

    private function refreshInstallmentStatus(PaymentPlan $plan, PaymentInstallment $installment, CarbonImmutable $asOf): void
    {
        $isPaid = $installment->installmentBalance() <= 0 && $installment->penaltyBalance() <= 0;
        if ($isPaid) {
            $installment->status = 'paid';
            $installment->paid_at = $installment->paid_at ?? $asOf->toDateString();
            $installment->save();
            return;
        }

        $hasAnyPayment = (float) $installment->amount_paid > 0 || (float) $installment->penalty_paid > 0;
        if ($hasAnyPayment) {
            $installment->status = 'partial';
            $installment->save();
            return;
        }

        $effectiveDue = $installment->dueDateImmutable()->addDays((int) $plan->penalty_grace_days);
        $installment->status = $asOf->greaterThan($effectiveDue) ? 'overdue' : 'pending';
        $installment->save();
    }

    private function refreshPlanStatus(PaymentPlan $plan): void
    {
        $remaining = $plan->installments()
            ->get()
            ->sum(function (PaymentInstallment $i) {
                return $i->installmentBalance() + $i->penaltyBalance();
            });

        if ($remaining <= 0.00001) {
            $plan->status = 'completed';
            $plan->save();
        }
    }
}

