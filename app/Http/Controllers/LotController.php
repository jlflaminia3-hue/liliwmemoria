<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Reservation;
use App\Services\LotStateService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LotController extends Controller
{
    private const LOT_CATEGORIES = [
        'phase_1',
        'phase_2',
        'garden_lot',
        'back_office_lot',
        'narra',
        'mausoleum',
    ];

    public function index(LotStateService $lotState)
    {
        $expiredLotIds = Reservation::expireDue(CarbonImmutable::today());
        foreach ($expiredLotIds as $lotId) {
            $lotState->sync((int) $lotId);
        }

        $request = request();
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'sort' => ['nullable', Rule::in(['lot_id', 'owner', 'category', 'status', 'deceased'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', Rule::in([10, 20, 50, 100])],
        ]);

        $search = trim((string) ($validated['search'] ?? ''));
        $sort = (string) ($validated['sort'] ?? 'lot_id');
        $direction = (string) ($validated['direction'] ?? 'asc');
        $perPage = (int) ($validated['per_page'] ?? 20);

        $query = Lot::query()
            ->with(['deceased:id,lot_id,first_name,last_name'])
            ->withCount('deceased');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('section', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhereRaw("CONCAT(COALESCE(section, ''), '-', COALESCE(lot_number, '')) LIKE ?", ['%'.$search.'%'])
                    ->orWhereHas('deceased', function ($deceasedQuery) use ($search) {
                        $deceasedQuery
                            ->where('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%');
                    });
            });
        }

        match ($sort) {
            'owner' => $query->orderBy('name', $direction),
            'category' => $query->orderBy('section', $direction)->orderBy('lot_number', 'asc'),
            'status' => $query->orderByRaw("
                CASE COALESCE(status, CASE WHEN is_occupied = 1 THEN 'occupied' ELSE 'available' END)
                    WHEN 'available' THEN 1
                    WHEN 'reserved' THEN 2
                    WHEN 'occupied' THEN 3
                    ELSE 4
                END {$direction}
            ")->orderBy('lot_number', 'asc'),
            'deceased' => $query->orderBy('deceased_count', $direction)->orderBy('lot_number', 'asc'),
            default => $query->orderBy('section', $direction)->orderBy('lot_number', $direction),
        };

        $lots = $query->paginate($perPage)->withQueryString();

        return view('admin.lots.index', compact('lots'));
    }

    public function create()
    {
        $defaultCategory = 'phase_1';
        $nextLotNumber = (int) Lot::query()
            ->where('section', $defaultCategory)
            ->max('lot_number') + 1;
        $nextLotId = Lot::categoryPrefix($defaultCategory).'-'.$nextLotNumber;

        return view('admin.lots.create', compact('nextLotNumber', 'nextLotId', 'defaultCategory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lot_number' => 'nullable|integer|min:1',
            'name' => 'nullable|string|max:255',
            'section' => 'required|in:'.implode(',', self::LOT_CATEGORIES),
            'block' => 'nullable|string|max:255',
            'status' => 'nullable|in:available,occupied,reserved',
            'notes' => 'nullable|string',
        ]);

        $status = $validated['status'] ?? null;
        if (! $status) {
            $status = ! empty($validated['is_occupied']) ? 'occupied' : 'available';
        }

        if ($status !== 'available') {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
        }

        $ownerName = trim((string) ($validated['name'] ?? ''));
        if ($ownerName === '') {
            $ownerName = 'Unassigned';
        }

        DB::transaction(function () use ($validated, $status, $ownerName) {
            $category = (string) ($validated['section'] ?? '');
            $maxLotNumber = (int) Lot::query()
                ->where('section', $category)
                ->lockForUpdate()
                ->max('lot_number');
            $requestedLotNumber = (int) ($validated['lot_number'] ?? 0);

            $lotNumber = $requestedLotNumber > 0 ? $requestedLotNumber : ($maxLotNumber + 1);
            if (
                $lotNumber <= $maxLotNumber
                && Lot::query()->where('section', $category)->where('lot_number', $lotNumber)->exists()
            ) {
                $lotNumber = $maxLotNumber + 1;
            }

            Lot::create(array_merge($validated, [
                'lot_number' => $lotNumber,
                'name' => $ownerName,
                'status' => $status,
                'is_occupied' => $status === 'occupied',
            ]));
        }, 3);

        return redirect()->route('admin.lots.index')->with('success', 'Lot created successfully.');
    }

    public function edit(Lot $lot)
    {
        return view('admin.lots.edit', compact('lot'));
    }

    public function update(Request $request, Lot $lot)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'required|in:'.implode(',', self::LOT_CATEGORIES),
            'block' => 'nullable|string|max:255',
            'status' => 'nullable|in:available,occupied,reserved',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'notes' => 'nullable|string',
            'deceased_first_name' => 'nullable|string|max:255',
            'deceased_last_name' => 'nullable|string|max:255',
            'deceased_date_of_birth' => 'nullable|date',
            'deceased_date_of_death' => 'nullable|date',
            'deceased_burial_date' => 'nullable|date',
            'deceased_notes' => 'nullable|string',
        ]);

        $status = $validated['status'] ?? null;
        if (! $status) {
            $status = $lot->status ?? ($lot->is_occupied ? 'occupied' : 'available');
        }

        $hasDeceasedInput = ! empty($validated['deceased_first_name']) || ! empty($validated['deceased_last_name']);
        if ($hasDeceasedInput) {
            $request->validate([
                'deceased_first_name' => 'required|string|max:255',
                'deceased_last_name' => 'required|string|max:255',
            ]);
            $status = 'occupied';
        }

        if ($status === 'occupied' && ! $hasDeceasedInput) {
            $request->validate([
                'deceased_first_name' => 'required|string|max:255',
                'deceased_last_name' => 'required|string|max:255',
            ]);
        }

        $validated['status'] = $status;
        $validated['is_occupied'] = $status === 'occupied';

        $lot->update([
            'name' => $validated['name'],
            'section' => $validated['section'] ?? null,
            'block' => $validated['block'] ?? null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'status' => $validated['status'],
            'is_occupied' => $validated['is_occupied'],
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($status === 'occupied' && ! empty($validated['deceased_first_name']) && ! empty($validated['deceased_last_name'])) {
            $lot->deceased()->create([
                'first_name' => $validated['deceased_first_name'],
                'last_name' => $validated['deceased_last_name'],
                'date_of_birth' => $validated['deceased_date_of_birth'] ?? null,
                'date_of_death' => $validated['deceased_date_of_death'] ?? null,
                'burial_date' => $validated['deceased_burial_date'] ?? null,
                'notes' => $validated['deceased_notes'] ?? null,
            ]);
        }

        return redirect()->route('admin.lots.index')->with('success', 'Lot updated successfully.');
    }

    public function destroy(Lot $lot)
    {
        $lot->delete();

        return redirect()->route('admin.lots.index')->with('success', 'Lot deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'lot_ids' => 'required|array|min:1',
            'lot_ids.*' => 'integer|exists:lots,id',
        ]);

        $deletedCount = Lot::query()
            ->whereIn('id', $validated['lot_ids'])
            ->delete();

        return redirect()
            ->route('admin.lots.index')
            ->with('success', $deletedCount.' lot(s) deleted successfully.');
    }

    public function map(LotStateService $lotState)
    {
        $expiredLotIds = Reservation::expireDue(CarbonImmutable::today());
        foreach ($expiredLotIds as $lotId) {
            $lotState->sync((int) $lotId);
        }

        $lots = Lot::with('deceased')->get();

        return response()
            ->view('admin.lots.map', compact('lots'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function snapshot(Lot $lot, LotStateService $lotState)
    {
        $lotState->sync((int) $lot->id);

        $fresh = Lot::query()
            ->with([
                'deceased' => function ($query) {
                    $query
                        ->select(['id', 'lot_id', 'first_name', 'last_name', 'date_of_birth', 'date_of_death', 'status'])
                        ->where('status', '!=', 'exhumed')
                        ->latest('burial_date')
                        ->latest('id');
                },
            ])
            ->findOrFail($lot->id);

        return response()->json([
            'lot' => $fresh,
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function nextLotNumber()
    {
        $request = request();
        $categoryValue = $request->query('category');

        if ($categoryValue !== null && $categoryValue !== '') {
            $request->validate([
                'category' => 'in:'.implode(',', self::LOT_CATEGORIES),
            ]);
        } else {
            $categoryValue = '';
        }

        $nextLotNumber = DB::transaction(function () use ($categoryValue) {
            return (int) Lot::query()
                ->where('section', $categoryValue)
                ->lockForUpdate()
                ->max('lot_number') + 1;
        }, 3);

        $lotId = Lot::categoryPrefix((string) $categoryValue).'-'.$nextLotNumber;

        return response()->json([
            'lot_number' => $nextLotNumber,
            'lot_id' => $lotId,
        ]);
    }

    public function storeWithDeceased(Request $request)
    {
        $validated = $request->validate([
            'lot_number' => 'nullable|integer|min:1',
            'name' => 'nullable|string|max:255',
            'section' => 'required|in:'.implode(',', self::LOT_CATEGORIES),
            'block' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'geometry_type' => 'nullable|in:rect,poly',
            'geometry' => 'nullable|json',
            'status' => 'required|in:available,occupied,reserved',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'date_of_death' => 'nullable|date',
            'burial_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validated['status'] !== 'available') {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
        }

        if ($validated['status'] === 'occupied') {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
            ]);
        }

        $ownerName = trim((string) ($validated['name'] ?? ''));
        if ($ownerName === '') {
            $ownerName = 'Unassigned';
        }

        $lot = DB::transaction(function () use ($validated, $ownerName) {
            $category = (string) ($validated['section'] ?? '');
            $maxLotNumber = (int) Lot::query()
                ->where('section', $category)
                ->lockForUpdate()
                ->max('lot_number');
            $requestedLotNumber = (int) ($validated['lot_number'] ?? 0);

            $lotNumber = $requestedLotNumber > 0 ? $requestedLotNumber : ($maxLotNumber + 1);
            if (
                $lotNumber <= $maxLotNumber
                && Lot::query()->where('section', $category)->where('lot_number', $lotNumber)->exists()
            ) {
                $lotNumber = $maxLotNumber + 1;
            }

            $lot = Lot::create([
                'lot_number' => $lotNumber,
                'name' => $ownerName,
                'section' => $validated['section'] ?? null,
                'block' => $validated['block'] ?? null,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'geometry_type' => $validated['geometry_type'] ?? null,
                'geometry' => isset($validated['geometry']) ? json_decode($validated['geometry'], true) : null,
                'status' => $validated['status'],
                'is_occupied' => $validated['status'] === 'occupied',
                'notes' => $validated['notes'] ?? null,
            ]);

            if ($validated['status'] === 'occupied') {
                $lot->deceased()->create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'date_of_birth' => $validated['date_of_birth'] ?? null,
                    'date_of_death' => $validated['date_of_death'] ?? null,
                    'burial_date' => $validated['burial_date'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]);
            }

            return $lot;
        }, 3);

        return redirect()
            ->route('admin.lots.map')
            ->with('success', 'Burial lot added successfully.')
            ->with('new_lot_id', $lot->id);
    }
}
