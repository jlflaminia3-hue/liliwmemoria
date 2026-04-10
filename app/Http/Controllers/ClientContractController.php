<?php

namespace App\Http\Controllers;

use App\Mail\ContractPdfMail;
use App\Models\Client;
use App\Models\ClientContract;
use App\Models\Lot;
use App\Services\Contracts\ContractPdfService;
use App\Services\Contracts\ContractPaymentPlanSyncService;
use App\Services\LotStateService;
use App\Services\Payments\PaymentPlanGenerator;
use App\Services\Reservations\LotReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ClientContractController extends Controller
{
    public function store(
        Request $request,
        Client $client,
        PaymentPlanGenerator $generator,
        ContractPdfService $pdfs,
        ContractPaymentPlanSyncService $contractPlans,
        LotReservationService $lotReservations,
        LotStateService $lotState,
    )
    {
        $validated = $request->validate([
            'lot_id' => 'nullable|exists:lots,id',
            'contract_lot_id' => 'nullable|string|max:32',
            'lot_kind' => ['nullable', Rule::in(['phase_1', 'phase_2', 'garden_lot', 'back_office_lot', 'narra', 'mausoleum'])],
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

        DB::transaction(function () use ($client, $validated, $generator, $contractPlans, $lotReservations, $lotState, &$contractId) {
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
                $contractPlans->sync($contract, $generator);
                return;
            }

            $lotField = empty($validated['lot_id']) ? 'contract_lot_id' : 'lot_id';
            $lotReservations->reserve(
                client: $client,
                lotId: (int) $contractData['lot_id'],
                startedAt: $validated['signed_at'] ?? null,
                endedAt: $validated['due_date'] ?? null,
                lotKind: $validated['lot_kind'] ?? null,
                lotFieldForErrors: $lotField,
            );

            $contractPlans->sync($contract, $generator);
            $lotState->sync((int) $contractData['lot_id']);
        });

        if ($contractId) {
            $contract = ClientContract::query()->with('client')->find($contractId);
            if ($contract) {
                $emailError = null;
                $pdfBinary = $pdfs->renderPdfBinary($contract);
                $path = 'contracts/contract-' . $contract->id . '.pdf';
                Storage::disk('local')->put($path, $pdfBinary);

                $contract->pdf_path = $path;
                $contract->pdf_generated_at = now();

                if ($emailPdf && $contract->client?->email) {
                    $filename = 'Contract-' . ($contract->contract_number ?? $contract->id) . '.pdf';
                    try {
                        Mail::to($contract->client->email)->send(new ContractPdfMail($contract, $pdfBinary, $filename));
                        $contract->pdf_emailed_at = now();
                    } catch (TransportExceptionInterface $e) {
                        report($e);
                        $emailError = 'Email could not be sent. Please check your mail server/DNS settings.';
                    }
                }

                $contract->save();

                if ($emailError) {
                    return back()->with('warning', $emailError)->with('success', 'Contract saved.');
                }
            }
        }

        return back()->with('success', 'Contract saved.');
    }

    public function update(
        Request $request,
        Client $client,
        ClientContract $contract,
        PaymentPlanGenerator $generator,
        ContractPdfService $pdfs,
        ContractPaymentPlanSyncService $contractPlans,
        LotReservationService $lotReservations,
        LotStateService $lotState,
    )
    {
        if ($contract->client_id !== $client->id) {
            abort(404);
        }

        $validated = $request->validate([
            'lot_id' => 'nullable|exists:lots,id',
            'contract_lot_id' => 'nullable|string|max:32',
            'lot_kind' => ['nullable', Rule::in(['phase_1', 'phase_2', 'garden_lot', 'back_office_lot', 'narra', 'mausoleum'])],
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

        DB::transaction(function () use ($client, $contract, $validated, $generator, $contractPlans, $lotReservations, $lotState) {
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

            $lotReservations->clearOwnershipIfLotChanged($client, $oldLotId ? (int) $oldLotId : null, $newLotId ? (int) $newLotId : null);
            if ($oldLotId && $oldLotId !== $newLotId) {
                $lotState->sync((int) $oldLotId);
            }

            if (empty($newLotId)) {
                return;
            }

            $lotField = empty($validated['lot_id']) ? 'contract_lot_id' : 'lot_id';
            $lotReservations->reserve(
                client: $client,
                lotId: (int) $newLotId,
                startedAt: $validated['signed_at'] ?? null,
                endedAt: $validated['due_date'] ?? null,
                lotKind: $validated['lot_kind'] ?? null,
                lotFieldForErrors: $lotField,
            );

            $contractPlans->sync($contract, $generator);
            $lotState->sync((int) $newLotId);
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
            try {
                Mail::to($contract->client->email)->send(new ContractPdfMail($contract, $pdfBinary, $filename));
                $contract->pdf_emailed_at = now();
            } catch (TransportExceptionInterface $e) {
                report($e);
                $contract->save();
                return back()->with('warning', 'Email could not be sent. Please check your mail server/DNS settings.')->with('success', 'Contract updated.');
            }
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

    // Contract payment plans now sync via ContractPaymentPlanSyncService.
}
