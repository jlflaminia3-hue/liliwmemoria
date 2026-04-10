<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Deceased;
use App\Models\Lot;
use App\Services\LotStateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
            'per_page' => ['nullable', Rule::in([10, 20, 50, 100])],
        ]);

        $search = trim((string) ($validated['search'] ?? ''));
        $status = (string) ($validated['status'] ?? '');
        $compliance = (string) ($validated['compliance'] ?? 'all');
        $perPage = (int) ($validated['per_page'] ?? 20);

        $query = Deceased::query()
            ->with([
                'lot:id,lot_number,section,name,status,is_occupied',
                'client:id,first_name,last_name',
            ]);

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%')
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery
                            ->where('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('lot', function ($lotQuery) use ($search) {
                        $lotQuery
                            ->where('name', 'like', '%'.$search.'%')
                            ->orWhere('section', 'like', '%'.$search.'%')
                            ->orWhereRaw("CAST(lot_number AS CHAR) LIKE ?", ['%'.$search.'%']);
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
        ];

        $lots = Lot::query()
            ->orderBy('section')
            ->orderBy('lot_number')
            ->get(['id', 'lot_number', 'section', 'name']);

        $clients = Client::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('admin.interments.index', compact('interments', 'stats', 'lots', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateInterment($request);

        DB::transaction(function () use ($request, $validated) {
            $deceased = Deceased::create($this->buildPayload($request, $validated));
            $this->syncLotState((int) $deceased->lot_id);
        });

        return redirect()
            ->route('admin.interments.index')
            ->with('success', 'Interment record created successfully.');
    }

    public function update(Request $request, Deceased $deceased)
    {
        $validated = $this->validateInterment($request, $deceased);

        DB::transaction(function () use ($request, $validated, $deceased) {
            $oldLotId = (int) $deceased->lot_id;

            $deceased->update($this->buildPayload($request, $validated, $deceased));

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

            $deceased->delete();
            $this->syncLotState($lotId);
        });

        return redirect()
            ->route('admin.interments.index')
            ->with('success', 'Interment record deleted successfully.');
    }

    public function downloadDocument(Deceased $deceased, string $document)
    {
        $column = self::DOCUMENT_FIELDS[$document] ?? null;
        abort_unless($column, 404);

        $path = $deceased->{$column};
        abort_if(! $path || ! Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path, basename($path));
    }

    private function validateInterment(Request $request, ?Deceased $deceased = null): array
    {
        $validated = $request->validate([
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
            '_modal' => 'nullable|string',
            '_record_id' => 'nullable|integer',
        ]);

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
