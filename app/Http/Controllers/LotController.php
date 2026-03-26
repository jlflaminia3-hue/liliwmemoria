<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LotController extends Controller
{
    public function index()
    {
        $lots = Lot::with('deceased')->get();

        return view('admin.lots.index', compact('lots'));
    }

    public function create()
    {
        $nextLotNumber = (int) Lot::max('lot_number') + 1;

        return view('admin.lots.create', compact('nextLotNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lot_number' => 'nullable|integer|min:1|unique:lots,lot_number',
            'name' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
            'status' => 'nullable|in:available,occupied,reserved',
            'notes' => 'nullable|string',
        ]);

        $status = $validated['status'] ?? null;
        if (! $status) {
            $status = ! empty($validated['is_occupied']) ? 'occupied' : 'available';
        }

        DB::transaction(function () use ($validated, $status) {
            $maxLotNumber = (int) Lot::query()->lockForUpdate()->max('lot_number');
            $requestedLotNumber = (int) ($validated['lot_number'] ?? 0);

            $lotNumber = $requestedLotNumber > 0 ? $requestedLotNumber : ($maxLotNumber + 1);
            if ($lotNumber <= $maxLotNumber && Lot::query()->where('lot_number', $lotNumber)->exists()) {
                $lotNumber = $maxLotNumber + 1;
            }

            Lot::create(array_merge($validated, [
                'lot_number' => $lotNumber,
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
            'section' => 'nullable|string|max:255',
            'is_occupied' => 'boolean',
            'status' => 'nullable|in:available,occupied,reserved',
            'notes' => 'nullable|string',
        ]);

        $status = $validated['status'] ?? null;
        if (! $status) {
            $status = ! empty($validated['is_occupied']) ? 'occupied' : 'available';
        }

        $validated['status'] = $status;
        $validated['is_occupied'] = $status === 'occupied';

        $lot->update($validated);

        return redirect()->route('admin.lots.index')->with('success', 'Lot updated successfully.');
    }

    public function destroy(Lot $lot)
    {
        $lot->delete();

        return redirect()->route('admin.lots.index')->with('success', 'Lot deleted successfully.');
    }

    public function map()
    {
        $lots = Lot::with('deceased')->get();

        return view('admin.lots.map', compact('lots'));
    }

    public function nextLotNumber()
    {
        $nextLotNumber = (int) Lot::max('lot_number') + 1;

        return response()->json([
            'lot_number' => $nextLotNumber,
        ]);
    }

    public function storeWithDeceased(Request $request)
    {
        $validated = $request->validate([
            'lot_number' => 'nullable|integer|min:1|unique:lots,lot_number',
            'name' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|in:available,occupied,reserved',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'date_of_death' => 'nullable|date',
            'burial_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'occupied') {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
            ]);
        }

        $lot = DB::transaction(function () use ($validated) {
            $maxLotNumber = (int) Lot::query()->lockForUpdate()->max('lot_number');
            $requestedLotNumber = (int) ($validated['lot_number'] ?? 0);

            $lotNumber = $requestedLotNumber > 0 ? $requestedLotNumber : ($maxLotNumber + 1);
            if ($lotNumber <= $maxLotNumber && Lot::query()->where('lot_number', $lotNumber)->exists()) {
                $lotNumber = $maxLotNumber + 1;
            }

            $lot = Lot::create([
                'lot_number' => $lotNumber,
                'name' => $validated['name'],
                'section' => $validated['section'] ?? null,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
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
