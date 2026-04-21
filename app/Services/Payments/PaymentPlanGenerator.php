<?php

namespace App\Services\Payments;

use App\Models\PaymentInstallment;
use App\Models\PaymentPlan;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PaymentPlanGenerator
{
    /**
     * Generates installments:
     * - optional downpayment due on start_date
     * - monthly installments starting 1 month after start_date
     * Interest is simple (flat) and starts after downpayment (financed_principal only).
     */
    public function generate(PaymentPlan $plan): Collection
    {
        return DB::transaction(function () use ($plan) {
            $installments = collect();

            $principal = (float) $plan->principal_amount;
            $downpayment = min((float) $plan->downpayment_amount, $principal);
            $financedPrincipal = max(0.0, $principal - $downpayment);

            $rate = (float) $plan->interest_rate_percent / 100.0;
            $interestAmount = round($financedPrincipal * $rate, 2);

            $plan->financed_principal = $financedPrincipal;
            $plan->interest_amount = $interestAmount;
            $plan->save();

            $start = CarbonImmutable::parse($plan->start_date);

            if ($downpayment > 0) {
                $installments->push(PaymentInstallment::create([
                    'payment_plan_id' => $plan->id,
                    'sequence' => 0,
                    'type' => 'downpayment',
                    'due_date' => $start->toDateString(),
                    'amount_due' => round($downpayment, 2),
                    'principal_due' => round($downpayment, 2),
                    'interest_due' => 0,
                    'status' => 'pending',
                ]));
            }

            $term = (int) $plan->term_months;
            if ($term <= 0 || $financedPrincipal <= 0) {
                return $installments;
            }

            $principalPer = round($financedPrincipal / $term, 2);
            $interestPer = round($interestAmount / $term, 2);

            $principalRunning = 0.0;
            $interestRunning = 0.0;

            for ($i = 1; $i <= $term; $i++) {
                $principalPart = $principalPer;
                $interestPart = $interestPer;

                if ($i === $term) {
                    $principalPart = round($financedPrincipal - $principalRunning, 2);
                    $interestPart = round($interestAmount - $interestRunning, 2);
                }

                $principalRunning += $principalPart;
                $interestRunning += $interestPart;

                $installments->push(PaymentInstallment::create([
                    'payment_plan_id' => $plan->id,
                    'sequence' => $i,
                    'type' => 'installment',
                    'due_date' => $start->addMonthsNoOverflow($i)->toDateString(),
                    'amount_due' => round($principalPart + $interestPart, 2),
                    'principal_due' => round($principalPart, 2),
                    'interest_due' => round($interestPart, 2),
                    'status' => 'pending',
                ]));
            }

            return $installments;
        });
    }
}
