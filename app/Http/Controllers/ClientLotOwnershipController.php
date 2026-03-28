<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientLotOwnership;
use App\Models\Lot;
use Illuminate\Http\Request;

class ClientLotOwnershipController extends Controller
{
    public function store(Request $request, Client $client)
    {
        $validated = $request->validate([
            'lot_number' => 'required|integer|min:1|exists:lots,lot_number',
            'ownership_type' => 'nullable|in:owner,co-owner,authorized',
            'started_at' => 'nullable|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
            'notes' => 'nullable|string',
        ]);

        $lot = Lot::query()->where('lot_number', $validated['lot_number'])->firstOrFail();
        $isAvailable = ($lot->status === 'available') || ($lot->status === null && $lot->is_occupied === false);
        if (! $isAvailable) {
            return back()->withErrors(['lot_number' => 'Selected lot is not available.']);
        }

        $validated['ownership_type'] = $validated['ownership_type'] ?? 'owner';

        ClientLotOwnership::updateOrCreate(
            ['client_id' => $client->id, 'lot_id' => $lot->id],
            [
                'ownership_type' => $validated['ownership_type'],
                'started_at' => $validated['started_at'] ?? null,
                'ended_at' => $validated['ended_at'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        $lot->name = $client->full_name;
        $lot->status = 'reserved';
        $lot->is_occupied = false;
        $lot->save();

        return back()->with('success', 'Ownership record saved.');
    }

    public function destroy(Client $client, ClientLotOwnership $ownership)
    {
        if ($ownership->client_id !== $client->id) {
            abort(404);
        }

        $lotId = $ownership->lot_id;
        $ownership->delete();

        $lot = Lot::query()->find($lotId);
        if ($lot && $lot->status !== 'occupied' && $lot->is_occupied === false) {
            $remaining = ClientLotOwnership::query()
                ->with('client')
                ->where('lot_id', $lotId)
                ->latest('id')
                ->first();

            if ($remaining && $remaining->client) {
                $lot->name = $remaining->client->full_name;
                $lot->status = 'reserved';
                $lot->save();
            } else {
                $lot->name = 'Unassigned';
                $lot->status = 'available';
                $lot->save();
            }
        }

        return back()->with('success', 'Ownership record deleted.');
    }
}
