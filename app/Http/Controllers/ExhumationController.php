<?php

namespace App\Http\Controllers;

use App\Models\ClientCommunication;
use App\Models\ClientLotOwnership;
use App\Models\Deceased;
use App\Models\Exhumation;
use App\Models\Lot;
use App\Services\Exhumations\TransferCertificateService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ExhumationController extends Controller
{
    private const DOCUMENT_FIELDS = [
        'exhumation_permit' => 'exhumation_permit_path',
        'transfer_permit' => 'transfer_permit_path',
    ];

    private const DELETABLE_DOCUMENTS = [
        'exhumation_permit' => 'exhumation_permit_path',
        'transfer_permit' => 'transfer_permit_path',
        'transfer_certificate' => 'transfer_certificate_path',
    ];

    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'workflow_status' => ['nullable', Rule::in(Exhumation::STATUSES)],
            'per_page' => ['nullable', Rule::in([10, 20, 50, 100])],
        ]);

        $search = trim((string) ($validated['search'] ?? ''));
        $workflowStatus = (string) ($validated['workflow_status'] ?? '');
        $perPage = (int) ($validated['per_page'] ?? 20);

        $query = Exhumation::query()
            ->with([
                'deceased:id,lot_id,client_id,first_name,last_name,status',
                'deceased.lot:id,lot_number,section,name,status,is_occupied',
                'deceased.client:id,first_name,last_name,email,phone',
            ]);

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('requested_by_name', 'like', '%'.$search.'%')
                    ->orWhere('destination_cemetery_name', 'like', '%'.$search.'%')
                    ->orWhereHas('deceased', function ($d) use ($search) {
                        $d->where('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('deceased.client', function ($c) use ($search) {
                        $c->where('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%');
                    });
            });
        }

        if ($workflowStatus !== '') {
            $query->where('workflow_status', $workflowStatus);
        }

        $exhumations = $query
            ->orderByRaw('CASE WHEN requested_at IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('requested_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.exhumations.index', compact('exhumations'));
    }

    public function show(Exhumation $exhumation)
    {
        $exhumation->loadMissing([
            'deceased.lot',
            'deceased.client',
        ]);

        return view('admin.exhumations.show', compact('exhumation'));
    }

    public function store(Request $request, Deceased $deceased)
    {
        $validated = $this->validateExhumation($request);

        $exhumation = DB::transaction(function () use ($request, $validated, $deceased) {
            $exhumation = $deceased->exhumations()->create($this->buildPayload($request, $validated, null, $deceased));
            $this->applyWorkflowEffects($exhumation);

            return $exhumation;
        });

        return redirect()
            ->route('admin.exhumations.show', $exhumation)
            ->with('success', 'Exhumation request created.');
    }

    public function update(Request $request, Exhumation $exhumation)
    {
        $validated = $this->validateExhumation($request, $exhumation);

        DB::transaction(function () use ($request, $validated, $exhumation) {
            $oldStatus = (string) ($exhumation->workflow_status ?? Exhumation::STATUS_SUBMITTED);

            $exhumation->update($this->buildPayload($request, $validated, $exhumation));
            $this->applyWorkflowEffects($exhumation, $oldStatus);
        });

        return redirect()
            ->route('admin.exhumations.show', $exhumation)
            ->with('success', 'Exhumation updated.');
    }

    public function downloadDocument(Exhumation $exhumation, string $document)
    {
        $column = self::DOCUMENT_FIELDS[$document] ?? null;
        abort_unless($column, 404);

        $path = $exhumation->{$column};
        abort_if(! $path || ! Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path, basename($path));
    }

    public function destroyDocument(Request $request, Exhumation $exhumation, string $document)
    {
        $column = self::DELETABLE_DOCUMENTS[$document] ?? null;
        abort_unless($column, 404);

        $status = (string) ($exhumation->workflow_status ?? Exhumation::STATUS_SUBMITTED);

        if ($document === 'exhumation_permit' && $status !== Exhumation::STATUS_DRAFT && $status !== Exhumation::STATUS_SUBMITTED) {
            return back()->withErrors([
                'document' => 'Cannot delete the exhumation permit while the case status is '.$this->humanStatus($status).'. Upload a new permit to replace the existing file.',
            ]);
        }

        if ($document === 'transfer_permit' && $status === Exhumation::STATUS_COMPLETED) {
            return back()->withErrors([
                'document' => 'Cannot delete the transfer permit while the case status is '.$this->humanStatus($status).'. Upload a new permit to replace the existing file.',
            ]);
        }

        DB::transaction(function () use ($exhumation, $column, $document) {
            $path = (string) ($exhumation->{$column} ?? '');
            if ($path !== '' && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            $exhumation->{$column} = null;
            if ($document === 'transfer_certificate') {
                $exhumation->transfer_certificate_generated_at = null;
            }
            $exhumation->save();
        });

        return redirect()
            ->route('admin.exhumations.show', $exhumation)
            ->with('success', 'Document deleted.');
    }

    public function generateTransferCertificate(Exhumation $exhumation, TransferCertificateService $pdfs)
    {
        $exhumation->loadMissing(['deceased.lot', 'deceased.client']);

        $binary = $pdfs->renderPdfBinary($exhumation);
        $path = 'exhumations/transfer-certificates/transfer-certificate-'.$exhumation->id.'.pdf';

        Storage::disk('public')->put($path, $binary);

        $exhumation->transfer_certificate_path = $path;
        $exhumation->transfer_certificate_generated_at = now();
        $exhumation->save();

        return redirect()
            ->route('admin.exhumations.show', $exhumation)
            ->with('success', 'Transfer certificate generated.');
    }

    public function downloadTransferCertificate(Exhumation $exhumation)
    {
        $path = $exhumation->transfer_certificate_path;
        abort_if(! $path || ! Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path, basename($path));
    }

    private function validateExhumation(Request $request, ?Exhumation $exhumation = null): array
    {
        $validated = $request->validate([
            'workflow_status' => ['required', Rule::in(Exhumation::STATUSES)],
            'requested_by_name' => 'nullable|string|max:255',
            'requested_by_relationship' => 'nullable|string|max:255',
            'requested_at' => 'nullable|date',
            'approved_at' => 'nullable|date',
            'exhumed_at' => 'nullable|date',
            'notes' => 'nullable|string',

            'destination_cemetery_name' => 'nullable|string|max:255',
            'destination_address' => 'nullable|string|max:255',
            'destination_city' => 'nullable|string|max:255',
            'destination_province' => 'nullable|string|max:255',
            'destination_contact_person' => 'nullable|string|max:255',
            'destination_contact_phone' => 'nullable|string|max:255',
            'destination_contact_email' => 'nullable|email|max:255',

            'transport_company' => 'nullable|string|max:255',
            'transport_vehicle_plate' => 'nullable|string|max:64',
            'transport_driver_name' => 'nullable|string|max:255',
            'transport_log' => 'nullable|string',

            'exhumation_permit' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'transfer_permit' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($exhumation) {
            $from = (string) ($exhumation->workflow_status ?? Exhumation::STATUS_SUBMITTED);
            $to = (string) ($validated['workflow_status'] ?? Exhumation::STATUS_SUBMITTED);

            $order = array_values(Exhumation::STATUSES);
            $fromIdx = array_search($from, $order, true);
            $toIdx = array_search($to, $order, true);

            // Keep it professional: do not allow going backwards in the workflow.
            if ($fromIdx !== false && $toIdx !== false && $toIdx < $fromIdx) {
                return $request->validate([
                    'workflow_status' => 'prohibited',
                ], [
                    'workflow_status.prohibited' => 'Workflow status cannot move backwards ('.$this->humanStatus($from).' -> '.$this->humanStatus($to).').',
                ]);
            }
        }

        // Basic workflow requirements: don't allow leaving Submitted without the exhumation permit.
        $hasExhumationPermit = $request->hasFile('exhumation_permit') || (bool) ($exhumation?->exhumation_permit_path);
        if (($validated['workflow_status'] ?? null) !== Exhumation::STATUS_DRAFT && ($validated['workflow_status'] ?? null) !== Exhumation::STATUS_SUBMITTED && ! $hasExhumationPermit) {
            $request->validate([
                'exhumation_permit' => 'required',
            ], [
                'exhumation_permit.required' => 'An exhumation permit is required before moving past Submitted.',
            ]);
        }

        // Require transfer permit and destination details once we complete the exhumation (scheduled status).
        $hasTransferPermit = $request->hasFile('transfer_permit') || (bool) ($exhumation?->transfer_permit_path);
        if (($validated['workflow_status'] ?? null) === Exhumation::STATUS_COMPLETED) {
            if (! $hasTransferPermit) {
                $request->validate([
                    'transfer_permit' => 'required',
                ], [
                    'transfer_permit.required' => 'A transfer permit is required before marking the case as Completed.',
                ]);
            }

            $request->validate([
                'destination_cemetery_name' => 'required|string|max:255',
            ], [
                'destination_cemetery_name.required' => 'Destination cemetery is required for transfer.',
            ]);
        }

        return $validated;
    }

    private function buildPayload(Request $request, array $validated, ?Exhumation $exhumation = null, ?Deceased $deceased = null): array
    {
        // Auto-populate requested_by_name from the deceased's client if not provided
        $requestedByName = $validated['requested_by_name'] ?? null;
        if (! $requestedByName) {
            // Use the passed deceased, or lazy-load from exhumation
            $deceasedModel = $deceased ?? ($exhumation ? $exhumation->deceased()->with('client')->first() : null);
            if ($deceasedModel && $deceasedModel->client) {
                $requestedByName = $deceasedModel->client->full_name;
            }
        }

        $payload = [
            'workflow_status' => $validated['workflow_status'],
            'requested_by_name' => $requestedByName,
            'requested_by_relationship' => $validated['requested_by_relationship'] ?? null,
            'requested_at' => $validated['requested_at'] ?? null,
            'approved_at' => $validated['approved_at'] ?? null,
            'exhumed_at' => $validated['exhumed_at'] ?? null,
            'notes' => $validated['notes'] ?? null,

            'destination_cemetery_name' => $validated['destination_cemetery_name'] ?? null,
            'destination_address' => $validated['destination_address'] ?? null,
            'destination_city' => $validated['destination_city'] ?? null,
            'destination_province' => $validated['destination_province'] ?? null,
            'destination_contact_person' => $validated['destination_contact_person'] ?? null,
            'destination_contact_phone' => $validated['destination_contact_phone'] ?? null,
            'destination_contact_email' => $validated['destination_contact_email'] ?? null,

            'transport_company' => $validated['transport_company'] ?? null,
            'transport_vehicle_plate' => $validated['transport_vehicle_plate'] ?? null,
            'transport_driver_name' => $validated['transport_driver_name'] ?? null,
            'transport_log' => $validated['transport_log'] ?? null,
        ];

        foreach (self::DOCUMENT_FIELDS as $input => $column) {
            if (! $request->hasFile($input)) {
                continue;
            }

            if ($exhumation?->{$column}) {
                Storage::disk('public')->delete($exhumation->{$column});
            }

            $payload[$column] = $request->file($input)->store('exhumations/documents', 'public');
        }

        return $payload;
    }

    private function applyWorkflowEffects(Exhumation $exhumation, ?string $oldStatus = null): void
    {
        $deceased = $exhumation->deceased()->with(['lot', 'client'])->first();
        if (! $deceased) {
            return;
        }

        // Auto-fill checkpoint timestamps when status changes forward.
        if ($oldStatus !== $exhumation->workflow_status) {
            if ($exhumation->workflow_status === Exhumation::STATUS_SUBMITTED && ! $exhumation->requested_at) {
                $exhumation->requested_at = now();
                $exhumation->save();
            }
            if ($exhumation->workflow_status === Exhumation::STATUS_APPROVED && ! $exhumation->approved_at) {
                $exhumation->approved_at = now();
                $exhumation->save();
            }
            if ($exhumation->workflow_status === Exhumation::STATUS_SCHEDULED && ! $exhumation->exhumed_at) {
                $exhumation->exhumed_at = now();
                $exhumation->save();
            }
        }

        // Lot reassignment: once remains are removed, mark the interment record as exhumed.
        if ($exhumation->isRemainsRemovedFromLot() && $deceased->status !== 'exhumed') {
            $deceased->status = 'exhumed';
            $deceased->save();
        }

        // Write a communication log for the family, if we have a client link.
        if ($deceased->client_id && $oldStatus !== $exhumation->workflow_status) {
            ClientCommunication::query()->create([
                'client_id' => $deceased->client_id,
                'channel' => 'other',
                'subject' => 'Exhumation status update',
                'message' => 'Exhumation for '.$deceased->full_name.' updated to '.$this->humanStatus($exhumation->workflow_status).'.',
                'occurred_at' => now(),
                'created_by' => auth()->id(),
            ]);
        }

        // Sync the lot state based on the deceased status + current ownership.
        $this->syncLotState((int) $deceased->lot_id);
    }

    private function syncLotState(int $lotId): void
    {
        $lot = Lot::query()->find($lotId);
        if (! $lot) {
            return;
        }

        // Consider any deceased record still not exhumed as occupying the lot.
        $active = Deceased::query()
            ->with('client')
            ->where('lot_id', $lotId)
            ->where('status', '!=', 'exhumed')
            ->latest('burial_date')
            ->latest('id')
            ->first();

        if ($active) {
            $lot->status = 'occupied';
            $lot->is_occupied = true;
            if ($active->client) {
                $lot->name = $active->client->full_name;
            }
            $lot->save();

            return;
        }

        // Fallback to reserved/available based on ownership records.
        $today = CarbonImmutable::today();
        $latestOwnership = ClientLotOwnership::query()
            ->with('client')
            ->where('lot_id', $lotId)
            ->where(function ($q) use ($today) {
                $q->whereNull('ended_at')->orWhere('ended_at', '>=', $today->toDateString());
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();

        $lot->is_occupied = false;
        if ($latestOwnership?->client) {
            $lot->status = 'reserved';
            $lot->name = $latestOwnership->client->full_name;
        } else {
            $lot->status = 'available';
            $lot->name = 'Unassigned';
        }
        $lot->save();
    }

    private function humanStatus(string $status): string
    {
        return match ($status) {
            Exhumation::STATUS_DRAFT => 'Draft',
            Exhumation::STATUS_SUBMITTED => 'Submitted',
            Exhumation::STATUS_APPROVED => 'Approved',
            Exhumation::STATUS_SCHEDULED => 'Scheduled',
            Exhumation::STATUS_COMPLETED => 'Completed',
            Exhumation::STATUS_ARCHIVED => 'Archived',
            default => $status,
        };
    }
}
