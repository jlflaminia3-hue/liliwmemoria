<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientContract;
use Illuminate\Http\Request;

class ClientContractController extends Controller
{
    public function store(Request $request, Client $client)
    {
        $validated = $request->validate([
            'lot_id' => 'nullable|exists:lots,id',
            'contract_number' => 'nullable|string|max:255',
            'contract_type' => 'required|in:purchase,reservation,other',
            'status' => 'required|in:draft,active,completed,cancelled,past_due',
            'total_amount' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'signed_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $client->contracts()->create($validated);

        return back()->with('success', 'Contract saved.');
    }

    public function destroy(Client $client, ClientContract $contract)
    {
        if ($contract->client_id !== $client->id) {
            abort(404);
        }

        $contract->delete();

        return back()->with('success', 'Contract deleted.');
    }
}

