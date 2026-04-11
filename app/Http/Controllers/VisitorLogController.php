<?php

namespace App\Http\Controllers;

use App\Models\Deceased;
use App\Models\VisitorLog;
use Illuminate\Http\Request;

class VisitorLogController extends Controller
{
    public function create(Request $request)
    {
        $deceased = Deceased::query()
            ->with('lot:id,lot_number,section,name,geometry_type,geometry,latitude,longitude')
            ->where('status', '!=', 'exhumed')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'lot_id', 'first_name', 'last_name', 'status']);

        $deceasedIndex = $deceased->map(function ($person) {
            return [
                'id' => $person->id,
                'name' => trim(($person->last_name ?? '').', '.($person->first_name ?? '')),
                'lot' => $person->lot ? ($person->lot->lot_id ?? '') : '',
            ];
        })->values();

        return view('visit.create', compact('deceased', 'deceasedIndex'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'deceased_id' => 'required|exists:deceased,id',
            'visitor_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
        ]);

        $log = VisitorLog::create([
            'deceased_id' => (int) $validated['deceased_id'],
            'visitor_name' => $validated['visitor_name'],
            'contact_number' => $validated['contact_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'purpose' => $validated['purpose'] ?? null,
            'visited_at' => now(),
        ]);

        return redirect()->route('public.visit.locator', $log);
    }

    public function locator(VisitorLog $visitorLog)
    {
        $visitorLog->load([
            'deceased:id,lot_id,first_name,last_name,status',
            'deceased.lot:id,lot_number,section,block,name,geometry_type,geometry,latitude,longitude,status,is_occupied',
        ]);

        abort_if(! $visitorLog->deceased?->lot, 404);

        return view('visit.locator', [
            'log' => $visitorLog,
            'deceased' => $visitorLog->deceased,
            'lot' => $visitorLog->deceased->lot,
            'mapImageUrl' => asset(config('cemetery.map_image')),
            'entrance' => [
                'x' => (float) config('cemetery.entrance_x'),
                'y' => (float) config('cemetery.entrance_y'),
                'label' => (string) config('cemetery.entrance_label'),
            ],
        ]);
    }
}
