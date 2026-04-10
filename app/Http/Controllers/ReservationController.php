<?php

namespace App\Http\Controllers;

use App\Mail\ContractPdfMail;
use App\Models\Client;
use App\Models\ClientContract;
use App\Models\Deceased;
use App\Models\Lot;
use App\Models\PaymentPlan;
use App\Models\Reservation;
use App\Services\Contracts\ContractPaymentPlanSyncService;
use App\Services\Contracts\ContractPdfService;
use App\Services\LotStateService;
use App\Services\Payments\PaymentPlanGenerator;
use App\Services\Reservations\LotReservationService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ReservationController extends Controller
{
    private const STATUSES = [
        Reservation::STATUS_ACTIVE,
        Reservation::STATUS_EXPIRED,
        Reservation::STATUS_FULFILLED,
    ];

    public function index(Request $request, LotStateService $lotState)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in(self::STATUSES)],
            'payment_status' => ['nullable', Rule::in(Reservation::PAYMENT_STATUSES)],
            'per_page' => ['nullable', Rule::in([10, 20, 50, 100])],
            'lot_id' => 'nullable|integer|exists:lots,id',
            'create' => 'nullable|in:1,0',
        ]);

        $today = CarbonImmutable::today();
        $expiredLotIds = Reservation::expireDue($today);
        foreach ($expiredLotIds as $lotId) {
            $lotState->sync((int) $lotId);
        }

        $search = trim((string) ($validated['search'] ?? ''));
        $status = (string) ($validated['status'] ?? '');
        $paymentStatus = (string) ($validated['payment_status'] ?? '');
        $perPage = (int) ($validated['per_page'] ?? 20);

        $query = Reservation::query()
            ->with([
                'client:id,first_name,last_name,email,phone',
                'lot:id,lot_number,section,block,name,status,is_occupied',
                'paymentPlan:id,plan_number,status,client_id,lot_id',
                'contract:id,contract_number,client_id,lot_id,contract_type,status',
            ]);

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery
                            ->where('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%')
                            ->orWhere('phone', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('lot', function ($lotQuery) use ($search) {
                        $lotQuery
                            ->where('section', 'like', '%'.$search.'%')
                            ->orWhere('block', 'like', '%'.$search.'%')
                            ->orWhereRaw("CAST(lot_number AS CHAR) LIKE ?", ['%'.$search.'%'])
                            ->orWhere('name', 'like', '%'.$search.'%');
                    });
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($paymentStatus !== '') {
            $query->where('payment_status', $paymentStatus);
        }

        $reservations = $query
            ->orderByRaw("CASE status WHEN 'active' THEN 1 WHEN 'expired' THEN 2 WHEN 'fulfilled' THEN 3 ELSE 4 END")
            ->orderByDesc('reserved_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        $statsBase = Reservation::query();
        $stats = [
            'total' => (clone $statsBase)->count(),
            'active' => (clone $statsBase)->active($today)->count(),
            'expired' => (clone $statsBase)->where('status', Reservation::STATUS_EXPIRED)->count(),
            'fulfilled' => (clone $statsBase)->where('status', Reservation::STATUS_FULFILLED)->count(),
        ];

        $clients = Client::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'email', 'phone']);

        $lots = Lot::query()
            ->where(function ($q) {
                $q->where('status', 'available')->orWhereNull('status');
            })
            ->where('is_occupied', false)
            ->orderBy('section')
            ->orderBy('block')
            ->orderBy('lot_number')
            ->get(['id', 'lot_number', 'section', 'block', 'name', 'status', 'is_occupied']);

        $paymentPlans = PaymentPlan::query()
            ->orderByDesc('id')
            ->get(['id', 'plan_number', 'client_id', 'lot_id', 'status']);

        $prefillLotId = $request->integer('lot_id') ?: null;
        $openCreateModal = (string) ($validated['create'] ?? '') === '1';

        return view('admin.reservations.index', compact(
            'reservations',
            'stats',
            'clients',
            'lots',
            'paymentPlans',
            'prefillLotId',
            'openCreateModal',
        ));
    }

    public function store(
        Request $request,
        LotStateService $lotState,
        LotReservationService $lotReservations,
        PaymentPlanGenerator $generator,
        ContractPaymentPlanSyncService $contractPlans,
        ContractPdfService $pdfs,
    )
    {
        $validated = $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'lot_id' => 'required|integer|exists:lots,id',
            'reserved_at' => 'required|date',
            'expires_at' => 'nullable|date|after_or_equal:reserved_at',
            'contract_duration_months' => ['nullable', Rule::in([12, 18, 24])],
            'total_amount' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'lot_kind' => ['nullable', Rule::in(['phase_1', 'phase_2', 'garden_lot', 'back_office_lot', 'narra', 'mausoleum'])],
            'payment_status' => ['nullable', Rule::in(Reservation::PAYMENT_STATUSES)],
            'payment_terms' => 'nullable|string',
            'payment_plan_id' => 'nullable|integer|exists:payment_plans,id',
            'contract' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string',
            'email_pdf' => 'nullable|boolean',
            '_modal' => 'nullable|string',
        ]);

        $today = CarbonImmutable::today();
        $expiredLotIds = Reservation::expireDue($today);
        foreach ($expiredLotIds as $lotId) {
            $lotState->sync((int) $lotId);
        }

        $contractId = null;
        $emailPdf = (bool) ($validated['email_pdf'] ?? false);

        DB::transaction(function () use ($request, $validated, $lotState, $lotReservations, $generator, $contractPlans, $today, &$contractId) {
            $lot = Lot::query()->lockForUpdate()->findOrFail((int) $validated['lot_id']);

            $hasInterment = Deceased::query()
                ->where('lot_id', $lot->id)
                ->where('status', '!=', 'exhumed')
                ->exists();
            if ($hasInterment) {
                throw ValidationException::withMessages([
                    'lot_id' => 'Selected lot already has an interment.',
                ]);
            }

            $isAvailable = ($lot->status === 'available') || ($lot->status === null && $lot->is_occupied === false);
            if (! $isAvailable) {
                throw ValidationException::withMessages([
                    'lot_id' => 'Selected lot is not available.',
                ]);
            }

            $hasActiveReservation = Reservation::query()
                ->active($today)
                ->where('lot_id', $lot->id)
                ->exists();
            if ($hasActiveReservation) {
                throw ValidationException::withMessages([
                    'lot_id' => 'Selected lot already has an active reservation.',
                ]);
            }

            $expiresAt = $validated['expires_at'] ?? null;
            $durationMonths = (int) ($validated['contract_duration_months'] ?? 0);
            if (! $expiresAt && in_array($durationMonths, [12, 18, 24], true)) {
                $expiresAt = \Illuminate\Support\Carbon::parse($validated['reserved_at'])
                    ->addMonthsNoOverflow($durationMonths)
                    ->toDateString();
            }

            $contractPath = null;
            if ($request->hasFile('contract')) {
                $contractPath = $request->file('contract')->store('reservations/contracts', 'public');
            }

            $paymentPlanId = $validated['payment_plan_id'] ?? null;
            if ($paymentPlanId) {
                $plan = PaymentPlan::query()->find($paymentPlanId);
                if ($plan && (int) $plan->client_id !== (int) $validated['client_id']) {
                    throw ValidationException::withMessages([
                        'payment_plan_id' => 'Selected payment plan does not match the client.',
                    ]);
                }
                if ($plan && $plan->lot_id && (int) $plan->lot_id !== (int) $validated['lot_id']) {
                    throw ValidationException::withMessages([
                        'payment_plan_id' => 'Selected payment plan does not match the lot.',
                    ]);
                }
            }

            $contract = ClientContract::create([
                'client_id' => $validated['client_id'],
                'created_by_user_id' => auth()->id(),
                'lot_id' => $validated['lot_id'],
                'lot_kind' => $validated['lot_kind'] ?? null,
                'contract_type' => 'reservation',
                'status' => 'active',
                'total_amount' => $validated['total_amount'] ?? null,
                'amount_paid' => $validated['amount_paid'] ?? null,
                'signed_at' => $validated['reserved_at'],
                'contract_duration_months' => $durationMonths ?: null,
                'due_date' => $expiresAt,
                'notes' => $validated['payment_terms'] ?? null,
            ]);

            if (empty($contract->contract_number)) {
                $contract->contract_number = ClientContract::formatContractNumber($contract->id);
                $contract->save();
            }

            $contractId = $contract->id;

            $contractPlans->sync($contract, $generator);
            $planId = PaymentPlan::query()
                ->where('client_contract_id', $contract->id)
                ->latest('id')
                ->value('id');

            $reservation = Reservation::create([
                'client_id' => $validated['client_id'],
                'lot_id' => $validated['lot_id'],
                'payment_plan_id' => $paymentPlanId ?? $planId,
                'client_contract_id' => $contract->id,
                'reserved_at' => $validated['reserved_at'],
                'expires_at' => $expiresAt,
                'status' => Reservation::STATUS_ACTIVE,
                'payment_status' => $validated['payment_status'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? null,
                'contract_path' => $contractPath,
                'notes' => $validated['notes'] ?? null,
            ]);

            $client = Client::query()->find((int) $reservation->client_id);
            if ($client) {
                $lotReservations->reserve(
                    client: $client,
                    lotId: (int) $reservation->lot_id,
                    startedAt: $validated['reserved_at'],
                    endedAt: $expiresAt,
                    lotKind: $validated['lot_kind'] ?? null,
                    lotFieldForErrors: 'lot_id',
                );
            }

            $lotState->sync((int) $lot->id);
        }, 3);

        $emailWarning = null;
        if ($contractId) {
            $contract = ClientContract::query()->with('client')->find($contractId);
            if ($contract) {
                $pdfBinary = $pdfs->renderPdfBinary($contract);
                $path = 'contracts/contract-' . $contract->id . '.pdf';
                Storage::disk('local')->put($path, $pdfBinary);

                $contract->pdf_path = $path;
                $contract->pdf_generated_at = now();

                $client = $contract->client;
                if ($emailPdf) {
                    if (! $client?->email) {
                        $emailWarning = 'Client has no email on file, so the contract PDF was not emailed.';
                    } else {
                        $filename = 'Contract-' . ($contract->contract_number ?? $contract->id) . '.pdf';
                        try {
                            Mail::to($client->email)->send(new ContractPdfMail($contract, $pdfBinary, $filename));
                            $contract->pdf_emailed_at = now();
                        } catch (TransportExceptionInterface $e) {
                            report($e);
                            $emailWarning = 'Contract email could not be sent. Please check your mail server/DNS settings.';
                        }
                    }
                }

                $contract->save();
            }
        }

        if ($emailWarning) {
            return redirect()
                ->route('admin.reservations.index')
                ->with('warning', $emailWarning)
                ->with('success', 'Reservation created successfully.');
        }

        return redirect()
            ->route('admin.reservations.index')
            ->with('success', 'Reservation created successfully.');
    }

    public function update(
        Request $request,
        Reservation $reservation,
        LotStateService $lotState,
        LotReservationService $lotReservations,
        PaymentPlanGenerator $generator,
        ContractPaymentPlanSyncService $contractPlans,
    )
    {
        $validated = $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'reserved_at' => 'required|date',
            'expires_at' => 'nullable|date|after_or_equal:reserved_at',
            'status' => ['required', Rule::in(self::STATUSES)],
            'contract_duration_months' => ['nullable', Rule::in([12, 18, 24])],
            'total_amount' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'lot_kind' => ['nullable', Rule::in(['phase_1', 'phase_2', 'garden_lot', 'back_office_lot', 'narra', 'mausoleum'])],
            'payment_status' => ['nullable', Rule::in(Reservation::PAYMENT_STATUSES)],
            'payment_terms' => 'nullable|string',
            'payment_plan_id' => 'nullable|integer|exists:payment_plans,id',
            'contract' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $validated, $reservation, $lotState, $lotReservations, $generator, $contractPlans) {
            $contractPath = $reservation->contract_path;
            if ($request->hasFile('contract')) {
                if ($contractPath) {
                    Storage::disk('public')->delete($contractPath);
                }
                $contractPath = $request->file('contract')->store('reservations/contracts', 'public');
            }

            $paymentPlanId = $validated['payment_plan_id'] ?? null;
            if ($paymentPlanId) {
                $plan = PaymentPlan::query()->find($paymentPlanId);
                if ($plan && (int) $plan->client_id !== (int) $validated['client_id']) {
                    throw ValidationException::withMessages([
                        'payment_plan_id' => 'Selected payment plan does not match the client.',
                    ]);
                }
                if ($plan && $plan->lot_id && (int) $plan->lot_id !== (int) $reservation->lot_id) {
                    throw ValidationException::withMessages([
                        'payment_plan_id' => 'Selected payment plan does not match the lot.',
                    ]);
                }
            }

            $status = (string) $validated['status'];
            $fulfilledAt = $reservation->fulfilled_at;
            if ($status === Reservation::STATUS_FULFILLED && ! $fulfilledAt) {
                $fulfilledAt = now();
            }
            if ($status !== Reservation::STATUS_FULFILLED) {
                $fulfilledAt = null;
            }

            $reservation->update([
                'client_id' => $validated['client_id'],
                'reserved_at' => $validated['reserved_at'],
                'expires_at' => $validated['expires_at'] ?? null,
                'status' => $status,
                'payment_status' => $validated['payment_status'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? null,
                'payment_plan_id' => $paymentPlanId,
                'contract_path' => $contractPath,
                'notes' => $validated['notes'] ?? null,
                'fulfilled_at' => $fulfilledAt,
            ]);

            $contractStatus = match ($status) {
                Reservation::STATUS_FULFILLED => 'completed',
                Reservation::STATUS_EXPIRED => 'cancelled',
                default => 'active',
            };

            $durationMonths = (int) ($validated['contract_duration_months'] ?? 0);
            $expiresAt = $validated['expires_at'] ?? null;
            if (! $expiresAt && in_array($durationMonths, [12, 18, 24], true)) {
                $expiresAt = \Illuminate\Support\Carbon::parse($validated['reserved_at'])
                    ->addMonthsNoOverflow($durationMonths)
                    ->toDateString();
                $reservation->expires_at = $expiresAt;
                $reservation->save();
            }

            if ($reservation->client_contract_id) {
                $contract = ClientContract::query()->find((int) $reservation->client_contract_id);
                if ($contract) {
                    $contract->client_id = $reservation->client_id;
                    $contract->lot_id = $reservation->lot_id;
                    $contract->lot_kind = $validated['lot_kind'] ?? $contract->lot_kind;
                    $contract->status = $contractStatus;
                    $contract->total_amount = $validated['total_amount'] ?? $contract->total_amount;
                    $contract->amount_paid = $validated['amount_paid'] ?? $contract->amount_paid;
                    $contract->signed_at = $validated['reserved_at'];
                    $contract->contract_duration_months = $durationMonths ?: $contract->contract_duration_months;
                    $contract->due_date = $expiresAt;
                    $contract->notes = $validated['payment_terms'] ?? $contract->notes;
                    $contract->save();

                    $contractPlans->sync($contract, $generator);
                }
            }

            $client = Client::query()->find((int) $reservation->client_id);
            if ($client) {
                $lotReservations->reserve(
                    client: $client,
                    lotId: (int) $reservation->lot_id,
                    startedAt: $validated['reserved_at'],
                    endedAt: $expiresAt,
                    lotKind: $validated['lot_kind'] ?? null,
                    lotFieldForErrors: 'lot_id',
                );
            }

            $lotState->sync((int) $reservation->lot_id);
        }, 3);

        return redirect()
            ->route('admin.reservations.index')
            ->with('success', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation, LotStateService $lotState)
    {
        DB::transaction(function () use ($reservation, $lotState) {
            $lotId = (int) $reservation->lot_id;

            if ($reservation->contract_path) {
                Storage::disk('public')->delete($reservation->contract_path);
            }

            $reservation->delete();
            $lotState->sync($lotId);
        }, 3);

        return redirect()
            ->route('admin.reservations.index')
            ->with('success', 'Reservation deleted successfully.');
    }

    public function downloadContract(Reservation $reservation)
    {
        $path = $reservation->contract_path;
        abort_if(! $path || ! Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path, basename($path));
    }
}
