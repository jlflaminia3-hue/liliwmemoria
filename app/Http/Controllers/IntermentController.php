<?php

namespace App\Http\Controllers;

use App\Mail\IntermentContractMail;
use App\Models\Client;
use App\Models\ClientLotOwnership;
use App\Models\Deceased;
use App\Models\IntermentPayment;
use App\Models\Lot;
use App\Models\Reservation;
use App\Services\Contracts\IntermentPdfService;
use App\Services\LotStateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class IntermentController extends Controller
{
    private const STATUSES = ['pending', 'confirmed', 'exhumed'];

    private const DOCUMENT_FIELDS = [
        'death_certificate' => 'death_certificate_path',
        'burial_permit' => 'burial_permit_path',
        'interment_form' => 'interment_form_path',
    ];

    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in(self::STATUSES)],
            'compliance' => ['nullable', Rule::in(['all', 'missing', 'ready'])],
            'payment_status' => ['nullable', Rule::in(['all', 'unpaid', 'partial', 'fully_paid'])],
            'per_page' => ['nullable', Rule::in([10, 20, 50, 100])],
        ]);

        $search = trim((string) ($validated['search'] ?? ''));
        $status = (string) ($validated['status'] ?? '');
        $compliance = (string) ($validated['compliance'] ?? 'all');
        $paymentStatus = (string) ($validated['payment_status'] ?? 'all');
        $perPage = (int) ($validated['per_page'] ?? 20);

        $query = Deceased::query()
            ->with([
                'lot:id,lot_number,section,name,status,is_occupied',
                'client:id,first_name,last_name,email',
                'latestExhumation',
                'payments',
            ]);

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%')
                    ->orWhere('interment_number', 'like', '%'.$search.'%')
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery
                            ->where('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('lot', function ($lotQuery) use ($search) {
                        $lotQuery
                            ->where('name', 'like', '%'.$search.'%')
                            ->orWhere('section', 'like', '%'.$search.'%')
                            ->orWhereRaw('CAST(lot_number AS CHAR) LIKE ?', ['%'.$search.'%']);
                    });
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($compliance === 'missing') {
            $query->where(function ($builder) {
                $builder
                    ->whereNull('client_id')
                    ->orWhereNull('burial_date')
                    ->orWhereNull('death_certificate_path')
                    ->orWhere(function ($confirmed) {
                        $confirmed
                            ->where('status', 'confirmed')
                            ->whereNull('burial_permit_path');
                    });
            });
        }

        if ($compliance === 'ready') {
            $query
                ->whereNotNull('client_id')
                ->whereNotNull('burial_date')
                ->whereNotNull('death_certificate_path')
                ->whereNotNull('burial_permit_path');
        }

        if ($paymentStatus !== 'all') {
            $query->where('payment_status', $paymentStatus);
        }

        $interments = $query
            ->orderByRaw('CASE WHEN burial_date IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('burial_date')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        $statsBase = Deceased::query();
        $stats = [
            'total' => (clone $statsBase)->count(),
            'pending' => (clone $statsBase)->where('status', 'pending')->count(),
            'confirmed' => (clone $statsBase)->where('status', 'confirmed')->count(),
            'exhumed' => (clone $statsBase)->where('status', 'exhumed')->count(),
            'missing_docs' => (clone $statsBase)->where(function ($builder) {
                $builder
                    ->whereNull('client_id')
                    ->orWhereNull('burial_date')
                    ->orWhereNull('death_certificate_path')
                    ->orWhere(function ($confirmed) {
                        $confirmed
                            ->where('status', 'confirmed')
                            ->whereNull('burial_permit_path');
                    });
            })->count(),
            'unpaid' => (clone $statsBase)->where('payment_status', Deceased::PAYMENT_STATUS_UNPAID)->count(),
            'partial' => (clone $statsBase)->where('payment_status', Deceased::PAYMENT_STATUS_PARTIAL)->count(),
            'fully_paid' => (clone $statsBase)->where('payment_status', Deceased::PAYMENT_STATUS_FULLY_PAID)->count(),
        ];

        $lots = Lot::query()
            ->orderBy('section')
            ->orderBy('lot_number')
            ->get();

        $clients = Client::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('admin.interments.index', compact('interments', 'stats', 'lots', 'clients'));
    }

    public function store(Request $request, IntermentPdfService $pdfService)
    {
        $validated = $this->validateInterment($request);
        $this->validateIntermentRules($request, $validated);

        DB::transaction(function () use ($request, $validated) {
            $lotId = (int) ($validated['lot_id'] ?? 0);

            $canInter = Deceased::canAddInterment($lotId);
            if (! $canInter['allowed']) {
                throw ValidationException::withMessages([
                    'lot_id' => $canInter['reason'],
                ]);
            }

            $intermentNumber = Deceased::generateIntermentNumber();

            $deceased = Deceased::create(array_merge(
                $this->buildPayload($request, $validated),
                [
                    'interment_fee' => (float) ($validated['interment_fee'] ?? Deceased::INTERMENT_FEE_TOTAL),
                    'payment_status' => Deceased::PAYMENT_STATUS_UNPAID,
                    'excavation_scheduled' => ($validated['excavation_scheduled'] ?? false),
                    'excavation_date' => $validated['excavation_date'] ?? null,
                    'interment_number' => $intermentNumber,
                ]
            ));

            $this->syncLotState((int) $deceased->lot_id);
        });

        return redirect()
            ->route('admin.interments.index')
            ->with('success', 'Interment record created successfully.');
    }

    public function update(Request $request, Deceased $deceased, IntermentPdfService $pdfService)
    {
        $validated = $this->validateInterment($request, $deceased);
        $this->validateIntermentRules($request, $validated, $deceased);

        DB::transaction(function () use ($request, $validated, $deceased, $pdfService) {
            $oldLotId = (int) $deceased->lot_id;

            if ((int) ($validated['lot_id'] ?? 0) !== $oldLotId) {
                $canInter = Deceased::canAddInterment((int) $validated['lot_id']);
                if (! $canInter['allowed']) {
                    throw ValidationException::withMessages([
                        'lot_id' => $canInter['reason'],
                    ]);
                }
            }

            $paymentBefore = (float) ($deceased->payment_before_excavation ?? 0);
            $paymentAfter = (float) ($deceased->payment_after_interment ?? 0);
            $newPaymentBefore = (float) ($validated['payment_before_excavation'] ?? 0);
            $newPaymentAfter = (float) ($validated['payment_after_interment'] ?? 0);
            $totalFee = (float) ($validated['interment_fee'] ?? $deceased->interment_fee ?? Deceased::INTERMENT_FEE_TOTAL);

            if ($newPaymentBefore > $paymentBefore || $newPaymentAfter > $paymentAfter) {
                $paymentBefore = max($paymentBefore, $newPaymentBefore);
                $paymentAfter = max($paymentAfter, $newPaymentAfter);
            }

            $paymentStatus = Deceased::PAYMENT_STATUS_UNPAID;
            if ($paymentBefore >= Deceased::INTERMENT_FEE_BEFORE_EXCAVATION && $paymentAfter >= Deceased::INTERMENT_FEE_AFTER_INTERMENT) {
                $paymentStatus = Deceased::PAYMENT_STATUS_FULLY_PAID;
            } elseif ($paymentBefore > 0 || $paymentAfter > 0) {
                $paymentStatus = Deceased::PAYMENT_STATUS_PARTIAL;
            }

            $updatePayload = $this->buildPayload($request, $validated, $deceased);

            if ($validated['payment_before_excavation_date'] ?? null) {
                $updatePayload['payment_before_excavation_date'] = $validated['payment_before_excavation_date'];
            }
            if ($validated['payment_after_interment_date'] ?? null) {
                $updatePayload['payment_after_interment_date'] = $validated['payment_after_interment_date'];
            }

            $deceased->update(array_merge($updatePayload, [
                'interment_fee' => $totalFee,
                'payment_before_excavation' => $paymentBefore ?: null,
                'payment_after_interment' => $paymentAfter ?: null,
                'payment_status' => $paymentStatus,
                'excavation_scheduled' => ($validated['excavation_scheduled'] ?? false),
                'excavation_date' => $validated['excavation_date'] ?? null,
            ]));

            if ($paymentStatus === Deceased::PAYMENT_STATUS_FULLY_PAID && ! $deceased->contract_path) {
                $pdfBinary = $pdfService->renderPdfBinary($deceased);
                $path = 'interments/contracts/interment-contract-'.$deceased->id.'.pdf';
                Storage::disk('local')->put($path, $pdfBinary);
                $deceased->contract_path = $path;
                $deceased->save();
            }

            $this->syncLotState($oldLotId);
            if ((int) $deceased->lot_id !== $oldLotId) {
                $this->syncLotState((int) $deceased->lot_id);
            }
        });

        return redirect()
            ->route('admin.interments.index')
            ->with('success', 'Interment record updated successfully.');
    }

    public function destroy(Deceased $deceased)
    {
        DB::transaction(function () use ($deceased) {
            $lotId = (int) $deceased->lot_id;

            foreach (self::DOCUMENT_FIELDS as $column) {
                if ($deceased->{$column}) {
                    Storage::disk('public')->delete($deceased->{$column});
                }
            }

            if ($deceased->contract_path) {
                Storage::disk('local')->delete($deceased->contract_path);
            }

            $deceased->delete();
            $this->syncLotState($lotId);
        });

        return redirect()
            ->route('admin.interments.index')
            ->with('success', 'Interment record deleted successfully.');
    }

    public function show(Deceased $deceased)
    {
        $deceased->load(['lot', 'client', 'payments', 'latestExhumation']);

        $totalFee = (float) ($deceased->interment_fee ?? Deceased::INTERMENT_FEE_TOTAL);

        return view('admin.interments.show', [
            'deceased' => $deceased,
            'totalFee' => $totalFee,
            'totalPaid' => $deceased->total_paid,
            'remainingBalance' => $deceased->remaining_balance,
        ]);
    }

    public function downloadDocument(Deceased $deceased, string $document)
    {
        $column = self::DOCUMENT_FIELDS[$document] ?? null;
        abort_unless($column, 404);

        $path = $deceased->{$column};
        abort_if(! $path || ! Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path, basename($path));
    }

    public function downloadContract(Deceased $deceased)
    {
        if (! $deceased->contract_path) {
            abort(404, 'Contract not available. Complete payment to generate contract.');
        }

        $path = $deceased->contract_path;
        abort_if(! Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path, 'Interment-Contract-'.($deceased->interment_number ?? $deceased->id).'.pdf');
    }

    public function pdf(Deceased $interment, IntermentPdfService $pdfService)
    {
        $pdfBinary = $pdfService->renderPdfBinary($interment);
        $filename = 'Interment-Contract-'.($interment->interment_number ?? $interment->id).'.pdf';

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Length' => strlen($pdfBinary),
        ]);
    }

    public function sendContract(Deceased $deceased, IntermentPdfService $pdfService)
    {
        if (! $deceased->contract_path) {
            $pdfBinary = $pdfService->renderPdfBinary($deceased);
            $path = 'interments/contracts/interment-contract-'.$deceased->id.'.pdf';
            Storage::disk('local')->put($path, $pdfBinary);
            $deceased->contract_path = $path;
            $deceased->save();
        }

        $client = $deceased->client;
        if (! $client?->email) {
            return back()->with('warning', 'Client has no email on file.');
        }

        try {
            $pdfBinary = Storage::disk('local')->get($deceased->contract_path);
            Mail::to($client->email)->send(new IntermentContractMail($deceased, $pdfBinary));

            $deceased->contract_sent_at = now();
            $deceased->save();

            return back()->with('success', 'Contract sent to '.$client->email.' successfully.');
        } catch (\Exception $e) {
            report($e);

            return back()->with('warning', 'Failed to send contract email. Please check your mail server settings.');
        }
    }

    public function storePayment(Request $request, Deceased $deceased, IntermentPdfService $pdfService)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'method' => 'required|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        DB::transaction(function () use ($validated, $deceased, $pdfService, $request) {
            $payment = IntermentPayment::create([
                'deceased_id' => $deceased->id,
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'method' => $validated['method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            if ($request->hasFile('receipt')) {
                $path = Storage::disk('public')->putFile("interment-receipts/{$payment->id}", $request->file('receipt'));
                $payment->receipt_path = $path;
                $payment->save();
            }

            $deceased->refresh();
            $totalPaid = $deceased->total_paid;
            $totalFee = (float) ($deceased->interment_fee ?? Deceased::INTERMENT_FEE_TOTAL);

            if (in_array($deceased->status, ['pending', 'draft'])) {
                $deceased->status = 'confirmed';
                $deceased->save();
            }

            if ($totalPaid >= $totalFee && ! $deceased->contract_path) {
                $pdfBinary = $pdfService->renderPdfBinary($deceased);
                $path = 'interments/contracts/interment-contract-'.$deceased->id.'.pdf';
                Storage::disk('local')->put($path, $pdfBinary);
                $deceased->contract_path = $path;
                $deceased->save();
            }

            $this->syncLotState((int) $deceased->lot_id);
        });

        return redirect()
            ->route('admin.interments.show', $deceased)
            ->with('success', 'Payment recorded successfully.');
    }

    public function paymentInvoice(Deceased $deceased, IntermentPayment $payment, Request $request)
    {
        abort_unless($payment->deceased_id === $deceased->id, 404);

        $view = view('admin.interments.payment-invoice', [
            'deceased' => $deceased,
            'payment' => $payment,
        ]);

        if ($request->boolean('download')) {
            $filename = "interment-invoice-{$payment->id}.html";

            return response()->streamDownload(function () use ($view) {
                echo $view->render();
            }, $filename, ['Content-Type' => 'text/html; charset=UTF-8']);
        }

        return $view;
    }

    public function paymentReceipt(Deceased $deceased, IntermentPayment $payment)
    {
        abort_unless($payment->deceased_id === $deceased->id, 404);
        abort_unless($payment->receipt_path, 404);

        return Storage::disk('public')->download($payment->receipt_path, "Interment-Receipt-{$payment->id}." . pathinfo($payment->receipt_path, PATHINFO_EXTENSION));
    }

    public function checkLotEligibility(Request $request)
    {
        $lotId = $request->input('lot_id');
        abort_unless($lotId, 400);

        $canInter = Deceased::canAddInterment((int) $lotId);
        $nextEligible = Deceased::getNextEligibleDate((int) $lotId);

        return response()->json([
            'eligible' => $canInter['allowed'],
            'reason' => $canInter['reason'] ?? null,
            'interment_count' => $canInter['interment_count'] ?? 0,
            'max_interments' => Deceased::MAX_INTERMENTS_PER_LOT,
            'next_eligible_date' => $nextEligible?->format('Y-m-d'),
            'min_years_gap' => Deceased::MIN_YEARS_BETWEEN_INTERMENTS,
        ]);
    }

    public function lotInfo(Request $request)
    {
        $lotId = $request->input('lot_id');
        abort_unless($lotId, 400);

        $lot = Lot::find($lotId);
        abort_unless($lot, 404);

        $client = null;

        // Get the client from lot ownerships first
        $ownership = ClientLotOwnership::with('client')
            ->where('lot_id', $lotId)
            ->whereNull('ended_at')
            ->first();

        if ($ownership && $ownership->client) {
            $client = $ownership->client;
        } else {
            // Fallback: get the client from the lot's active/fulfilled reservation
            $reservation = Reservation::with('client')
                ->where('lot_id', $lotId)
                ->whereIn('status', ['active', 'fulfilled'])
                ->first();

            $client = $reservation?->client;
        }

        $canInter = Deceased::canAddInterment((int) $lotId);
        $nextEligible = Deceased::getNextEligibleDate((int) $lotId);

        return response()->json([
            'lot_id' => $lot->id,
            'lot_number' => $lot->lot_id,
            'section' => $lot->section,
            'lot_category_label' => $lot->lot_category_label,
            'client' => $client ? [
                'id' => $client->id,
                'full_name' => $client->full_name,
                'email' => $client->email,
                'phone' => $client->phone,
                'address' => $client->address,
            ] : null,
            'eligible' => $canInter['allowed'],
            'reason' => $canInter['reason'] ?? null,
            'interment_count' => $canInter['interment_count'] ?? 0,
            'max_interments' => Deceased::MAX_INTERMENTS_PER_LOT,
            'next_eligible_date' => $nextEligible?->format('Y-m-d'),
            'min_years_gap' => Deceased::MIN_YEARS_BETWEEN_INTERMENTS,
        ]);
    }

    public function clientLots(Client $client)
    {
        $ownedLots = $client
            ->lotOwnerships()
            ->with('lot:id,lot_number,section,name')
            ->get()
            ->map(function ($ownership) {
                $lot = $ownership->lot;

                return [
                    'id' => $lot->id,
                    'lot_id' => $lot->lot_id,
                    'label' => $lot->lot_id.' - '.self::lotCategoryLabel($lot),
                ];
            });

        $reservedLots = $client
            ->reservations()
            ->whereIn('status', ['active', 'fulfilled'])
            ->with('lot:id,lot_number,section,name')
            ->get()
            ->map(function ($reservation) {
                $lot = $reservation->lot;

                return [
                    'id' => $lot->id,
                    'lot_id' => $lot->lot_id,
                    'label' => $lot->lot_id.' - '.self::lotCategoryLabel($lot),
                ];
            });

        $contractLots = $client
            ->contracts()
            ->whereIn('status', ['active', 'pending'])
            ->whereNotNull('lot_id')
            ->with('lot:id,lot_number,section,name')
            ->get()
            ->map(function ($contract) {
                $lot = $contract->lot;

                return [
                    'id' => $lot->id,
                    'lot_id' => $lot->lot_id,
                    'label' => $lot->lot_id.' - '.self::lotCategoryLabel($lot),
                ];
            });

        $lots = collect()
            ->merge($ownedLots)
            ->merge($reservedLots)
            ->merge($contractLots)
            ->unique('id')
            ->sortBy('lot_id')
            ->values();

        return response()->json($lots);
    }

    private static function lotCategoryLabel($lot): string
    {
        return match ($lot->section ?? '') {
            'phase_1' => 'Phase 1',
            'phase_2' => 'Phase 2',
            'garden_lot' => 'Garden Lot',
            'back_office_lot' => 'Back Office',
            'narra' => 'Narra',
            'mausoleum' => 'Mausoleum',
            default => 'Lot',
        };
    }

    private function validateInterment(Request $request, ?Deceased $deceased = null): array
    {
        $rules = [
            'client_id' => 'nullable|exists:clients,id',
            'lot_id' => 'required|exists:lots,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'date_of_death' => 'nullable|date|after_or_equal:date_of_birth',
            'burial_date' => 'nullable|date|after_or_equal:date_of_death',
            'status' => ['required', Rule::in(self::STATUSES)],
            'notes' => 'nullable|string',
            'death_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'burial_permit' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'interment_form' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'interment_fee' => 'nullable|numeric|min:0',
            'payment_before_excavation' => 'nullable|numeric|min:0',
            'payment_after_interment' => 'nullable|numeric|min:0',
            'payment_before_excavation_date' => 'nullable|date',
            'payment_after_interment_date' => 'nullable|date',
            'excavation_scheduled' => 'nullable|boolean',
            'excavation_date' => 'nullable|date',
            'email_contract' => 'nullable|boolean',
            '_modal' => 'nullable|string',
            '_record_id' => 'nullable|integer',
        ];

        $validated = $request->validate($rules);

        $burialPermitPresent = $request->hasFile('burial_permit')
            || (bool) ($deceased?->burial_permit_path);

        if (($validated['status'] ?? null) === 'confirmed' && ! $burialPermitPresent) {
            $request->validate([
                'burial_permit' => 'required',
            ], [
                'burial_permit.required' => 'A burial permit is required before an interment can be confirmed.',
            ]);
        }

        if (($validated['status'] ?? null) === 'confirmed' && empty($validated['burial_date'])) {
            $request->validate([
                'burial_date' => 'required|date',
            ]);
        }

        return $validated;
    }

    private function validateIntermentRules(Request $request, array $validated, ?Deceased $deceased = null): void
    {
        $lotId = (int) ($validated['lot_id'] ?? 0);

        $canInter = Deceased::canAddInterment($lotId);
        if (! $canInter['allowed']) {
            throw ValidationException::withMessages([
                'lot_id' => $canInter['reason'],
            ]);
        }
    }

    private function buildPayload(Request $request, array $validated, ?Deceased $deceased = null): array
    {
        $payload = [
            'client_id' => $validated['client_id'] ?? null,
            'lot_id' => $validated['lot_id'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'date_of_death' => $validated['date_of_death'] ?? null,
            'burial_date' => $validated['burial_date'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ];

        foreach (self::DOCUMENT_FIELDS as $input => $column) {
            if (! $request->hasFile($input)) {
                continue;
            }

            if ($deceased?->{$column}) {
                Storage::disk('public')->delete($deceased->{$column});
            }

            $payload[$column] = $request->file($input)->store('interments/documents', 'public');
        }

        return $payload;
    }

    private function syncLotState(int $lotId): void
    {
        app(LotStateService::class)->sync($lotId);
    }
}
