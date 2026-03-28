<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientCommunication;
use Illuminate\Http\Request;

class ClientCommunicationController extends Controller
{
    public function store(Request $request, Client $client)
    {
        $validated = $request->validate([
            'channel' => 'required|in:phone,email,sms,in_person,other',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'occurred_at' => 'nullable|date',
        ]);

        $client->communications()->create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Communication log saved.');
    }

    public function destroy(Client $client, ClientCommunication $communication)
    {
        if ($communication->client_id !== $client->id) {
            abort(404);
        }

        $communication->delete();

        return back()->with('success', 'Communication log deleted.');
    }
}

