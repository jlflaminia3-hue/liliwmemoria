<?php

namespace App\Http\Controllers;

use App\Mail\PaymentScheduleMail;
use App\Models\Client;
use App\Models\ClientContract;
use App\Models\PaymentInstallment;
use App\Models\PaymentPlan;
use App\Services\Payments\PaymentPenaltyService;
use App\Services\Payments\PaymentPlanGenerator;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class PaymentPlanController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'status' => ['nullable', 'string', Rule::in(['all', 'active', 'completed', 'canceled'])],
            'search' => 'nullable|string|max:255',
        ]);

        $clientId = $validated['client_id'] ?? null;
        $status = $validated['status'] ?? 'all';
        $search = trim((string) ($validated['search'] ?? ''));

        $query = PaymentPlan::query()
            ->with(['client', 'contract', 'lot', 'installments']);

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('plan_number', 'like', '%'.$search.'%')
                    ->orWhereHas('client', function ($cq) use ($search) {
                        $cq->where('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('lot', function ($lq) use ($search) {
                        $lq->where('lot_number', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('contract', function ($coq) use ($search) {
                        $coq->where('contract_number', 'like', '%'.$search.'%');
                    });
            });
        }

        $plans = $query->orderByDesc('id')->get();

        $plans->each(function (PaymentPlan $plan) {
            $plan->outstanding_total = $plan->installments->sum(fn (PaymentInstallment $i) => $i->installmentBalance() + $i->penaltyBalance());
            $plan->paid_total = $plan->installments->sum(fn (PaymentInstallment $i) => (float) $i->amount_paid + (float) $i->penalty_paid);
        });

        $client = $clientId ? Client::find($clientId) : null;
        $clients = Client::query()->orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'last_name']);

        return view('admin.payments.index', compact('plans', 'client', 'clients', 'clientId', 'status', 'search'));
    }

    public function create(Request $request)
    {
        $clientId = $request->integer('client_id') ?: null;

        $clients = Client::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        $contracts = ClientContract::query()
            ->with(['client', 'lot'])
            ->orderByDesc('id')
            ->get();

        $terms = [
            12 => 10,
            18 => 15,
            24 => 20,
        ];

        return view('admin.payments.create', compact('clients', 'contracts', 'terms', 'clientId'));
    }

    public function store(Request $request, PaymentPlanGenerator $generator)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'client_contract_id' => 'nullable|exists:client_contracts,id',
            'lot_id' => 'nullable|exists:lots,id',
            'principal_amount' => 'required|numeric|min:0.01',
            'downpayment_amount' => 'nullable|numeric|min:0',
            'term_months' => 'required|integer|in:12,18,24',
            'start_date' => 'required|date',
            'penalty_grace_days' => 'nullable|integer|min:0|max:365',
            'penalty_rate_percent' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $interestRate = PaymentPlan::interestRateForTerm((int) $validated['term_months']);

        $plan = PaymentPlan::create([
            'client_id' => $validated['client_id'],
            'client_contract_id' => $validated['client_contract_id'] ?? null,
            'lot_id' => $validated['lot_id'] ?? null,
            'plan_number' => PaymentPlan::generatePlanNumber(),
            'status' => 'active',
            'principal_amount' => $validated['principal_amount'],
            'downpayment_amount' => $validated['downpayment_amount'] ?? 0,
            'term_months' => $validated['term_months'],
            'interest_rate_percent' => $interestRate,
            'start_date' => $validated['start_date'],
            'penalty_grace_days' => $validated['penalty_grace_days'] ?? 0,
            'penalty_rate_percent' => $validated['penalty_rate_percent'] ?? 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        $generator->generate($plan);

        return redirect()
            ->route('admin.payments.show', $plan)
            ->with('success', 'Payment plan created.');
    }

    public function show(PaymentPlan $paymentPlan, PaymentPenaltyService $penalties)
    {
        $paymentPlan->load([
            'client.lotOwnerships.lot',
            'contract.lot',
            'lot',
            'installments' => fn ($q) => $q->orderBy('sequence'),
            'transactions' => fn ($q) => $q->orderByDesc('transaction_date'),
            'transactions.allocations.installment',
        ]);

        $penalties->recalculate($paymentPlan, CarbonImmutable::today());

        $totals = [
            'principal_total' => (float) $paymentPlan->principal_amount,
            'downpayment_total' => (float) $paymentPlan->downpayment_amount,
            'interest_total' => (float) $paymentPlan->interest_amount,
            'paid_total' => $paymentPlan->installments->sum(fn (PaymentInstallment $i) => (float) $i->amount_paid + (float) $i->penalty_paid),
            'installment_balance_total' => $paymentPlan->installments->sum(fn (PaymentInstallment $i) => $i->installmentBalance()),
            'penalty_balance_total' => $paymentPlan->installments->sum(fn (PaymentInstallment $i) => $i->penaltyBalance()),
        ];
        $totals['outstanding_total'] = $totals['installment_balance_total'] + $totals['penalty_balance_total'];

        return view('admin.payments.show', [
            'plan' => $paymentPlan,
            'totals' => $totals,
        ]);
    }

    public function notify(Request $request, PaymentPlan $paymentPlan)
    {
        $paymentPlan->load([
            'client',
            'installments' => fn ($q) => $q->orderBy('sequence'),
        ]);

        $client = $paymentPlan->client;
        if (! $client?->email) {
            return redirect()
                ->route('admin.payments.show', $paymentPlan)
                ->with('error', 'Client has no email address.');
        }

        $next = $paymentPlan->installments
            ->where('type', 'installment')
            ->filter(fn (PaymentInstallment $i) => $i->installmentBalance() > 0)
            ->sortBy('due_date')
            ->values()
            ->first();

        $upcoming = $paymentPlan->installments
            ->where('type', 'installment')
            ->filter(fn (PaymentInstallment $i) => $i->installmentBalance() > 0)
            ->sortBy('due_date')
            ->values();

        $instructions = (string) config('payments.instructions');

        Mail::to($client->email)->send(new PaymentScheduleMail(
            plan: $paymentPlan,
            nextInstallment: $next,
            upcomingInstallments: $upcoming,
            instructions: $instructions,
        ));

        $paymentPlan->last_notified_at = now();
        $paymentPlan->save();

        return redirect()
            ->route('admin.payments.show', $paymentPlan)
            ->with('success', 'Payment schedule email sent.');
    }
}
