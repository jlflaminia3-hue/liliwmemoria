<?php

namespace App\Services\Contracts;

use App\Models\ClientContract;
use App\Models\PaymentInstallment;
use App\Models\PaymentPlan;
use App\Models\PaymentTransaction;
use App\Services\Payments\PaymentPlanGenerator;

class ContractPaymentPlanSyncService
{
    public function sync(ClientContract $contract, PaymentPlanGenerator $generator): void
    {
        $plan = PaymentPlan::query()
            ->where('client_contract_id', $contract->id)
            ->latest('id')
            ->first();

        $planStatus = match ($contract->status) {
            'completed' => 'completed',
            'cancelled', 'transfered' => 'canceled',
            default => 'active',
        };

        if ($plan) {
            $plan->status = $planStatus;
            $plan->lot_id = $contract->lot_id;

            $principal = (float) ($contract->total_amount ?? 0);
            $downpayment = (float) ($contract->amount_paid ?? 0);
            $termMonths = (int) ($contract->contract_duration_months ?? 0);

            $hasRequired = $principal > 0 && $contract->signed_at && in_array($termMonths, [12, 18, 24], true);
            if ($hasRequired) {
                $plan->principal_amount = $principal;
                $plan->downpayment_amount = max(0, min($downpayment, $principal));
                $plan->term_months = $termMonths;
                $plan->interest_rate_percent = PaymentPlan::interestRateForTerm($termMonths);
                $plan->start_date = $contract->signed_at->toDateString();
            }

            $plan->save();

            if (! $hasRequired) {
                return;
            }

            $hasAnyTransactions = PaymentTransaction::query()
                ->where('payment_plan_id', $plan->id)
                ->exists();

            $hasAnyPaidInstallments = PaymentInstallment::query()
                ->where('payment_plan_id', $plan->id)
                ->where(function ($q) {
                    $q->where('amount_paid', '>', 0)->orWhere('penalty_paid', '>', 0);
                })
                ->exists();

            if (! $hasAnyTransactions && ! $hasAnyPaidInstallments) {
                PaymentInstallment::query()->where('payment_plan_id', $plan->id)->delete();
                $generator->generate($plan);
            } else {
                $downpaymentInst = PaymentInstallment::query()
                    ->where('payment_plan_id', $plan->id)
                    ->where('type', 'downpayment')
                    ->first();

                if ($downpaymentInst && (float) $downpaymentInst->amount_paid <= 0) {
                    $downpaymentInst->due_date = $plan->start_date;
                    $downpaymentInst->amount_due = $plan->downpayment_amount;
                    $downpaymentInst->principal_due = $plan->downpayment_amount;
                    $downpaymentInst->interest_due = 0;
                    $downpaymentInst->save();
                }
            }

            return;
        }

        $principal = (float) ($contract->total_amount ?? 0);
        $downpayment = (float) ($contract->amount_paid ?? 0);
        $termMonths = (int) ($contract->contract_duration_months ?? 0);

        if ($principal <= 0 || ! $contract->signed_at || ! in_array($termMonths, [12, 18, 24], true)) {
            return;
        }

        $interestRate = PaymentPlan::interestRateForTerm($termMonths);

        $plan = PaymentPlan::create([
            'client_id' => $contract->client_id,
            'client_contract_id' => $contract->id,
            'lot_id' => $contract->lot_id,
            'plan_number' => PaymentPlan::generatePlanNumber(),
            'status' => $planStatus,
            'principal_amount' => $principal,
            'downpayment_amount' => max(0, min($downpayment, $principal)),
            'term_months' => $termMonths,
            'interest_rate_percent' => $interestRate,
            'start_date' => $contract->signed_at->toDateString(),
            'penalty_grace_days' => 0,
            'penalty_rate_percent' => 0,
            'notes' => null,
        ]);

        $generator->generate($plan);
    }
}

