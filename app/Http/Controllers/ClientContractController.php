<?php

namespace App\Http\Controllers;

use App\Mail\ContractPdfMail;
use App\Models\Client;
use App\Models\ClientContract;
use App\Models\ClientLotOwnership;
use App\Models\Lot;
use App\Models\PaymentPlan;
use App\Models\PaymentInstallment;
use App\Models\PaymentTransaction;
use App\Services\Contracts\ContractPdfService;
use App\Services\Payments\PaymentPlanGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ClientContractController extends Controller
{
    public function store(Request $request, Client $client, PaymentPlanGenerator $generator, ContractPdfService $pdfs)
    {
        $validated = $request->validate([
            'lot_id' => 'nullable|exists:lots,id',
            'contract_lot_id' => 'nullable|string|max:32',
            'lot_kind' => ['nullable', Rule::in(['phase_1', 'phase_2', 'garden_lot', 'back_office_lot', 'mausoleum'])],
            'contract_type' => 'required|in:purchase,reservation,other',
            'status' => 'required|in:active,pending,completed,cancelled,transfered',
            'total_amount' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'signed_at' => 'nullable|date',
            'contract_duration_months' => [
                'nullable',
                'integer',
                Rule::in([12, 18, 24]),
                Rule::requiredIf(fn () => $request->input('contract_type') === 'reservation'),
            ],
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'email_pdf' => 'nullable|boolean',
        ]);

        if (! empty($validated['contract_lot_id']) && ! Lot::parseLotId($validated['contract_lot_id'])) {
            throw ValidationException::withMessages(['contract_lot_id' => 'Invalid Lot ID.']);
        }

        if (($validated['contract_type'] ?? null) !== 'reservation') {
            $validated['contract_duration_months'] = null;
        }

        if (
            ($validated['contract_type'] ?? null) === 'reservation'
            && ! empty($validated['signed_at'])
            && ! empty($validated['contract_duration_months'])
        ) {
            $validated['due_date'] = Carbon::parse($validated['signed_at'])
                ->addMonthsNoOverflow((int) $validated['contract_duration_months'])
                ->toDateString();
        }

        $emailPdf = (bool) ($validated['email_pdf'] ?? false);
        $contractId = null;

        DB::transaction(function () use ($client, $validated, $generator, &$contractId) {
            $contractData = $validated;
            $contractData['created_by_user_id'] = auth()->id();

            $contractLotId = $contractData['contract_lot_id'] ?? null;
            if (empty($contractData['lot_id']) && ! empty($contractLotId)) {
                $parsed = Lot::parseLotId($contractLotId);
                if ($parsed) {
                    $contractData['lot_id'] = Lot::query()
                        ->where('section', $parsed['section'])
                        ->where('lot_number', $parsed['lot_number'])
                        ->value('id');
                }
            }

            unset($contractData['contract_lot_id']);
            unset($contractData['email_pdf']);

            $contract = $client->contracts()->create($contractData);
            if (empty($contract->contract_number)) {
                $contract->contract_number = ClientContract::formatContractNumber($contract->id);
                $contract->save();
            }
            $contractId = $contract->id;

            if (empty($contractData['lot_id'])) {
                $this->syncContractPaymentPlan($contract, $generator);
                return;
            }

            $lot = Lot::query()->lockForUpdate()->findOrFail($contractData['lot_id']);

            $hasExistingOwnership = ClientLotOwnership::query()
                ->where('client_id', $client->id)
                ->where('lot_id', $lot->id)
                ->exists();

            $isAvailable = ($lot->status === 'available') || ($lot->status === null && $lot->is_occupied === false);
            if (! $hasExistingOwnership && ! $isAvailable) {
                $lotField = empty($validated['lot_id']) ? 'contract_lot_id' : 'lot_id';
                throw ValidationException::withMessages([$lotField => 'Selected lot is not available.']);
            }

            ClientLotOwnership::updateOrCreate(
                ['client_id' => $client->id, 'lot_id' => $lot->id],
                [
                    'ownership_type' => 'owner',
                    'started_at' => $validated['signed_at'] ?? null,
                    'ended_at' => $validated['due_date'] ?? null,
                    'notes' => null,
                ]
            );

            $lot->name = $client->full_name;
            $lot->status = 'reserved';
            $lot->is_occupied = false;

            if (! empty($validated['lot_kind']) && empty($lot->section)) {
                $lot->section = $validated['lot_kind'];
            }

            $lot->save();

            $this->syncContractPaymentPlan($contract, $generator);
        });

        if ($contractId) {
            $contract = ClientContract::query()->with('client')->find($contractId);
            if ($contract) {
                $pdfBinary = $pdfs->renderPdfBinary($contract);
                $path = 'contracts/contract-' . $contract->id . '.pdf';
                Storage::disk('local')->put($path, $pdfBinary);

                $contract->pdf_path = $path;
                $contract->pdf_generated_at = now();

                if ($emailPdf && $contract->client?->email) {
                    $filename = 'Contract-' . ($contract->contract_number ?? $contract->id) . '.pdf';
                    Mail::to($contract->client->email)->send(new ContractPdfMail($contract, $pdfBinary, $filename));
                    $contract->pdf_emailed_at = now();
                }

                $contract->save();
            }
        }

        return back()->with('success', 'Contract saved.');
    }

    public function update(Request $request, Client $client, ClientContract $contract, PaymentPlanGenerator $generator, ContractPdfService $pdfs)
    {
        if ($contract->client_id !== $client->id) {
            abort(404);
        }

        $validated = $request->validate([
            'lot_id' => 'nullable|exists:lots,id',
            'contract_lot_id' => 'nullable|string|max:32',
            'lot_kind' => ['nullable', Rule::in(['phase_1', 'phase_2', 'garden_lot', 'back_office_lot', 'mausoleum'])],
            'contract_type' => 'required|in:purchase,reservation,other',
            'status' => 'required|in:active,pending,completed,cancelled,transfered',
            'total_amount' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'signed_at' => 'nullable|date',
            'contract_duration_months' => [
                'nullable',
                'integer',
                Rule::in([12, 18, 24]),
                Rule::requiredIf(fn () => $request->input('contract_type') === 'reservation'),
            ],
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            '_modal' => 'nullable|string',
            '_contract_id' => 'nullable|integer',
            'email_pdf' => 'nullable|boolean',
        ]);

        if (! empty($validated['contract_lot_id']) && ! Lot::parseLotId($validated['contract_lot_id'])) {
            throw ValidationException::withMessages(['contract_lot_id' => 'Invalid Lot ID.']);
        }

        if (($validated['contract_type'] ?? null) !== 'reservation') {
            $validated['contract_duration_months'] = null;
        }

        if (
            ($validated['contract_type'] ?? null) === 'reservation'
            && ! empty($validated['signed_at'])
            && ! empty($validated['contract_duration_months'])
        ) {
            $validated['due_date'] = Carbon::parse($validated['signed_at'])
                ->addMonthsNoOverflow((int) $validated['contract_duration_months'])
                ->toDateString();
        }

        $emailPdf = (bool) ($validated['email_pdf'] ?? false);

        DB::transaction(function () use ($client, $contract, $validated, $generator) {
            $oldLotId = $contract->lot_id;

            $contractData = $validated;
            $contractLotId = $contractData['contract_lot_id'] ?? null;

            if (empty($contractData['lot_id']) && ! empty($contractLotId)) {
                $parsed = Lot::parseLotId($contractLotId);
                if ($parsed) {
                    $contractData['lot_id'] = Lot::query()
                        ->where('section', $parsed['section'])
                        ->where('lot_number', $parsed['lot_number'])
                        ->value('id');
                }
            }

            unset($contractData['contract_lot_id'], $contractData['_modal'], $contractData['_contract_id'], $contractData['email_pdf']);

            $contract->update($contractData);

            $newLotId = $contract->lot_id;

            if ($oldLotId && $oldLotId !== $newLotId) {
                $ownership = ClientLotOwnership::query()
                    ->where('client_id', $client->id)
                    ->where('lot_id', $oldLotId)
                    ->first();

                if ($ownership) {
                    $ownership->delete();
                }

                $oldLot = Lot::query()->find($oldLotId);
                if ($oldLot && $oldLot->status !== 'occupied' && $oldLot->is_occupied === false) {
                    $remaining = ClientLotOwnership::query()
                        ->with('client')
                        ->where('lot_id', $oldLotId)
                        ->latest('id')
                        ->first();

                    if ($remaining && $remaining->client) {
                        $oldLot->name = $remaining->client->full_name;
                        $oldLot->status = 'reserved';
                        $oldLot->save();
                    } else {
                        $oldLot->name = 'Unassigned';
                        $oldLot->status = 'available';
                        $oldLot->save();
                    }
                }
            }

            if (empty($newLotId)) {
                return;
            }

            $lot = Lot::query()->lockForUpdate()->findOrFail($newLotId);

            $hasExistingOwnership = ClientLotOwnership::query()
                ->where('client_id', $client->id)
                ->where('lot_id', $lot->id)
                ->exists();

            $isAvailable = ($lot->status === 'available') || ($lot->status === null && $lot->is_occupied === false);
            if (! $hasExistingOwnership && ! $isAvailable) {
                $lotField = empty($validated['lot_id']) ? 'contract_lot_id' : 'lot_id';
                throw ValidationException::withMessages([$lotField => 'Selected lot is not available.']);
            }

            ClientLotOwnership::updateOrCreate(
                ['client_id' => $client->id, 'lot_id' => $lot->id],
                [
                    'ownership_type' => 'owner',
                    'started_at' => $validated['signed_at'] ?? null,
                    'ended_at' => $validated['due_date'] ?? null,
                    'notes' => null,
                ]
            );

            $lot->name = $client->full_name;
            $lot->status = 'reserved';
            $lot->is_occupied = false;

            if (! empty($validated['lot_kind']) && empty($lot->section)) {
                $lot->section = $validated['lot_kind'];
            }

            $lot->save();

            $this->syncContractPaymentPlan($contract, $generator);
        });

        $contract->refresh();
        $contract->loadMissing('client');

        $pdfBinary = $pdfs->renderPdfBinary($contract);
        $path = 'contracts/contract-' . $contract->id . '.pdf';
        Storage::disk('local')->put($path, $pdfBinary);

        $contract->pdf_path = $path;
        $contract->pdf_generated_at = now();

        if ($emailPdf && $contract->client?->email) {
            $filename = 'Contract-' . ($contract->contract_number ?? $contract->id) . '.pdf';
            Mail::to($contract->client->email)->send(new ContractPdfMail($contract, $pdfBinary, $filename));
            $contract->pdf_emailed_at = now();
        }

        $contract->save();

        return back()->with('success', 'Contract updated.');
    }

    public function pdf(Client $client, ClientContract $contract, ContractPdfService $pdfs)
    {
        if ($contract->client_id !== $client->id) {
            abort(404);
        }

        $contract->loadMissing(['client', 'lot']);

        $filename = 'Contract-' . ($contract->contract_number ?? $contract->id) . '.pdf';

        if ($contract->pdf_path && Storage::disk('local')->exists($contract->pdf_path)) {
            return response()->download(Storage::disk('local')->path($contract->pdf_path), $filename);
        }

        $pdfBinary = $pdfs->renderPdfBinary($contract);
        $path = 'contracts/contract-' . $contract->id . '.pdf';
        Storage::disk('local')->put($path, $pdfBinary);

        $contract->pdf_path = $path;
        $contract->pdf_generated_at = now();
        $contract->save();

        return response()->download(Storage::disk('local')->path($path), $filename);
    }

    public function destroy(Client $client, ClientContract $contract)
    {
        if ($contract->client_id !== $client->id) {
            abort(404);
        }

        $contract->delete();

        return back()->with('success', 'Contract deleted.');
    }

    private function syncContractPaymentPlan(ClientContract $contract, PaymentPlanGenerator $generator): void
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
