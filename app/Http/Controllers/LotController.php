<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use Illuminate\Http\Request;

class LotController extends Controller
{
    public function index()
    {
        $lots = Lot::with('deceased')->get();

        return view('admin.lots.index', compact('lots'));
    }

    public function create()
    {
        return view('admin.lots.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        Lot::create($validated);

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
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'is_occupied' => 'boolean',
            'notes' => 'nullable|string',
        ]);

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

    public function storeWithDeceased(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'date_of_death' => 'nullable|date',
            'burial_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $lot = Lot::create([
            'name' => $validated['name'],
            'section' => $validated['section'] ?? null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'is_occupied' => true,
            'notes' => $validated['notes'] ?? null,
        ]);

        $lot->deceased()->create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'date_of_death' => $validated['date_of_death'] ?? null,
            'burial_date' => $validated['burial_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.lots.map')->with('success', 'Burial lot added successfully.');
    }
}
