<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientContract;
use App\Models\Lot;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ClientMaintenanceController extends Controller
{
    public function store(Request $request, Client $client)
    {
        $validated = $request->validate([
            'lot_id' => 'nullable|string|max:32',
            'client_contract_id' => 'nullable|exists:client_contracts,id',
            'service_type' => ['required', 'string', 'max:50', Rule::in(['general', 'cleaning', 'landscaping', 'repair', 'flowers', 'other'])],
            'status' => ['required', 'string', Rule::in(['scheduled', 'completed', 'cancelled'])],
            'service_date' => 'required|date',
            'amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $lotId = null;
        if (! empty($validated['lot_id'])) {
            $parsed = Lot::parseLotId($validated['lot_id']);
            if (! $parsed) {
                throw ValidationException::withMessages(['lot_id' => 'Invalid Lot ID.']);
            }

            $lotId = Lot::query()
                ->where('section', $parsed['section'])
                ->where('lot_number', $parsed['lot_number'])
                ->value('id');

            if (! $lotId) {
                throw ValidationException::withMessages(['lot_id' => 'Lot not found.']);
            }
        }

        if (! empty($validated['client_contract_id'])) {
            $contract = ClientContract::query()->findOrFail($validated['client_contract_id']);
            if ($contract->client_id !== $client->id) {
                throw ValidationException::withMessages(['client_contract_id' => 'Selected contract does not belong to this client.']);
            }
        }

        $client->maintenanceRecords()->create([
            'lot_id' => $lotId,
            'client_contract_id' => $validated['client_contract_id'] ?? null,
            'service_type' => $validated['service_type'],
            'status' => $validated['status'],
            'service_date' => $validated['service_date'],
            'amount' => $validated['amount'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Maintenance record saved.');
    }

    public function destroy(Client $client, MaintenanceRecord $maintenanceRecord)
    {
        if ($maintenanceRecord->client_id !== $client->id) {
            abort(404);
        }

        $maintenanceRecord->delete();

        return back()->with('success', 'Maintenance record deleted.');
    }
}
