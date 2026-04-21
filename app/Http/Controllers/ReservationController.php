<?php

namespace App\Http\Controllers;

use App\Mail\ContractPdfMail;
use App\Models\Client;
use App\Models\ClientContract;
use App\Models\Deceased;
use App\Models\Lot;
use App\Models\LotPayment;
use App\Models\PaymentPlan;
use App\Models\Reservation;
use App\Services\Contracts\ContractPaymentPlanSyncService;
use App\Services\Contracts\ContractPdfService;
use App\Services\LotStateService;
use App\Services\Payments\PaymentPlanGenerator;
use App\Services\Reservations\LotReservationService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
                            ->orWhereRaw('CAST(lot_number AS CHAR) LIKE ?', ['%'.$search.'%'])
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

        $prefillLotId = $request->integer('lot_id') ?: null;
        $openCreateModal = (string) ($validated['create'] ?? '') === '1';

        return view('admin.reservations.index', compact(
            'reservations',
            'stats',
            'clients',
            'lots',
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
    ) {
        $validated = $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'lot_id' => 'required|integer|exists:lots,id',
            'reserved_at' => 'required|date',
            'contract_duration_months' => ['nullable', Rule::in([12, 18, 24])],
            'total_amount' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'payment_status' => ['required', Rule::in(Reservation::PAYMENT_STATUSES)],
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

        DB::transaction(function () use ($validated, $lotState, $lotReservations, $generator, $contractPlans, $today, &$contractId) {
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

            $durationMonths = (int) ($validated['contract_duration_months'] ?? 0);
            $expiresAt = null;
            if ($durationMonths > 0) {
                $expiresAt = Carbon::parse($validated['reserved_at'])
                    ->addMonthsNoOverflow($durationMonths)
                    ->toDateString();
            }

            $contract = ClientContract::create([
                'client_id' => $validated['client_id'],
                'created_by_user_id' => auth()->id(),
                'lot_id' => $validated['lot_id'],
                'lot_kind' => $lot->section ?? null,
                'contract_type' => 'reservation',
                'status' => 'active',
                'total_amount' => $validated['total_amount'] ?? null,
                'amount_paid' => $validated['amount_paid'] ?? null,
                'signed_at' => $validated['reserved_at'],
                'contract_duration_months' => $durationMonths ?: null,
                'due_date' => $expiresAt,
                'notes' => null,
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
                'payment_plan_id' => $planId,
                'client_contract_id' => $contract->id,
                'reserved_at' => $validated['reserved_at'],
                'expires_at' => $expiresAt,
                'status' => Reservation::STATUS_ACTIVE,
                'payment_status' => $validated['payment_status'] ?? null,
                'payment_terms' => null,
                'contract_path' => null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $paymentStatus = $validated['payment_status'] ?? null;
            $isCashPayment = $paymentStatus === 'cash';

            if ($isCashPayment) {
                $amount = (float) ($validated['amount_paid'] ?? $validated['total_amount'] ?? 0);
                if ($amount <= 0) {
                    $amount = (float) ($contract->total_amount ?? 0);
                }
                if ($amount > 0) {
                    LotPayment::create([
                        'client_id' => $validated['client_id'],
                        'lot_id' => $validated['lot_id'],
                        'reservation_id' => $reservation->id,
                        'payment_number' => LotPayment::generatePaymentNumber(),
                        'amount' => $amount,
                        'payment_date' => $validated['reserved_at'],
                        'due_date' => $validated['reserved_at'],
                        'method' => 'cash',
                        'status' => LotPayment::STATUS_COMPLETED,
                        'completed_at' => now(),
                        'notes' => 'Payment for reservation - Cash',
                    ]);

                    PaymentPlan::create([
                        'client_id' => $validated['client_id'],
                        'client_contract_id' => $contract->id,
                        'lot_id' => $validated['lot_id'],
                        'plan_number' => PaymentPlan::generatePlanNumber(),
                        'status' => 'completed',
                        'principal_amount' => $amount,
                        'downpayment_amount' => $amount,
                        'term_months' => 0,
                        'interest_rate_percent' => 0,
                        'financed_principal' => 0,
                        'interest_amount' => 0,
                        'start_date' => $validated['reserved_at'],
                        'penalty_grace_days' => 0,
                        'penalty_rate_percent' => 0,
                        'notes' => 'Cash payment - Full settlement',
                    ]);

                    // Mark lot as sold for cash payments
                    $lot->status = 'sold';
                    $lot->is_occupied = true;
                    $lot->save();

                    // Update contract status to completed
                    $contract->status = 'completed';
                    $contract->save();

                    // Update reservation status to fulfilled
                    $reservation->status = Reservation::STATUS_FULFILLED;
                    $reservation->fulfilled_at = now();
                    $reservation->save();
                }
            } else {
                // For installment payments, reserve the lot normally
                $client = Client::query()->find((int) $reservation->client_id);
                if ($client) {
                    $lotReservations->reserve(
                        client: $client,
                        lotId: (int) $reservation->lot_id,
                        startedAt: $validated['reserved_at'],
                        endedAt: $expiresAt,
                        lotKind: null,
                        lotFieldForErrors: 'lot_id',
                    );
                }

                $lotState->sync((int) $lot->id);
            }
        }, 3);

        $emailWarning = null;
        $pdfError = null;
        if ($contractId) {
            $contract = ClientContract::query()->with('client')->find($contractId);
            if ($contract) {
                try {
                    $pdfBinary = $pdfs->renderPdfBinary($contract);
                    $path = 'contracts/contract-'.$contract->id.'.pdf';
                    Storage::disk('local')->put($path, $pdfBinary);

                    $contract->pdf_path = $path;
                    $contract->pdf_generated_at = now();
                } catch (\Throwable $e) {
                    report($e);
                    $pdfError = 'Contract PDF could not be generated: '.$e->getMessage();
                }

                $client = $contract->client;
                if ($emailPdf && ! $pdfError) {
                    if (! $client?->email) {
                        $emailWarning = 'Client has no email on file, so the contract PDF was not emailed.';
                    } else {
                        $filename = 'Contract-'.($contract->contract_number ?? $contract->id).'.pdf';
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
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Reservation created successfully.',
                    'warning' => $emailWarning,
                ]);
            }

            return redirect()
                ->route('admin.reservations.index')
                ->with('warning', $emailWarning)
                ->with('success', 'Reservation created successfully.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reservation created successfully.',
            ]);
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
    ) {
        $validated = $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'reserved_at' => 'required|date',
            'status' => ['required', Rule::in(self::STATUSES)],
            'contract_duration_months' => ['nullable', Rule::in([12, 18, 24])],
            'total_amount' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'payment_status' => ['required', Rule::in(Reservation::PAYMENT_STATUSES)],
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $reservation, $lotState, $lotReservations, $generator, $contractPlans) {
            $status = (string) $validated['status'];
            $fulfilledAt = $reservation->fulfilled_at;
            if ($status === Reservation::STATUS_FULFILLED && ! $fulfilledAt) {
                $fulfilledAt = now();
            }
            if ($status !== Reservation::STATUS_FULFILLED) {
                $fulfilledAt = null;
            }

            $durationMonths = (int) ($validated['contract_duration_months'] ?? 0);
            $expiresAt = null;
            if ($durationMonths > 0) {
                $expiresAt = Carbon::parse($validated['reserved_at'])
                    ->addMonthsNoOverflow($durationMonths)
                    ->toDateString();
            }

            $reservation->update([
                'client_id' => $validated['client_id'],
                'reserved_at' => $validated['reserved_at'],
                'expires_at' => $expiresAt,
                'status' => $status,
                'payment_status' => $validated['payment_status'] ?? null,
                'payment_terms' => null,
                'notes' => $validated['notes'] ?? null,
                'fulfilled_at' => $fulfilledAt,
            ]);

            $paymentStatus = $validated['payment_status'] ?? null;
            $oldPaymentStatus = $reservation->getOriginal('payment_status') ?? null;
            if ($paymentStatus === 'cash' && $oldPaymentStatus !== 'cash') {
                $amount = (float) ($validated['amount_paid'] ?? 0);
                if ($amount > 0) {
                    LotPayment::create([
                        'client_id' => $validated['client_id'],
                        'lot_id' => $reservation->lot_id,
                        'reservation_id' => $reservation->id,
                        'amount' => $amount,
                        'payment_date' => $validated['reserved_at'],
                        'due_date' => $validated['reserved_at'],
                        'method' => 'cash',
                        'status' => LotPayment::STATUS_COMPLETED,
                        'completed_at' => now(),
                        'notes' => 'Payment for reservation update - Cash',
                    ]);
                }
            }

            $contractStatus = match ($status) {
                Reservation::STATUS_FULFILLED => 'completed',
                Reservation::STATUS_EXPIRED => 'cancelled',
                default => 'active',
            };

            if ($reservation->client_contract_id) {
                $contract = ClientContract::query()->find((int) $reservation->client_contract_id);
                if ($contract) {
                    $contract->client_id = $reservation->client_id;
                    $contract->lot_id = $reservation->lot_id;
                    $lot = Lot::query()->find((int) $reservation->lot_id);
                    if ($lot && $lot->section) {
                        $contract->lot_kind = $lot->section;
                    }
                    $contract->status = $contractStatus;
                    $contract->total_amount = $validated['total_amount'] ?? $contract->total_amount;
                    $contract->amount_paid = $validated['amount_paid'] ?? $contract->amount_paid;
                    $contract->signed_at = $validated['reserved_at'];
                    $contract->contract_duration_months = $durationMonths ?: $contract->contract_duration_months;
                    $contract->due_date = $expiresAt;
                    $contract->notes = $contract->notes;
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
                    lotKind: null,
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
