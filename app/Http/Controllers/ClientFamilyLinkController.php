<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientFamilyLink;
use Illuminate\Http\Request;

class ClientFamilyLinkController extends Controller
{
    public function store(Request $request, Client $client)
    {
        $validated = $request->validate([
            'related_client_id' => 'required|exists:clients,id',
            'relationship' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $relatedId = (int) $validated['related_client_id'];
        if ($relatedId === (int) $client->id) {
            return back()->withErrors(['related_client_id' => 'Client cannot be linked to themselves.']);
        }

        $a = min((int) $client->id, $relatedId);
        $b = max((int) $client->id, $relatedId);

        ClientFamilyLink::updateOrCreate(
            ['client_id' => $a, 'related_client_id' => $b],
            [
                'relationship' => $validated['relationship'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return back()->with('success', 'Family link saved.');
    }

    public function destroy(Client $client, ClientFamilyLink $link)
    {
        if ($link->client_id !== $client->id && $link->related_client_id !== $client->id) {
            abort(404);
        }

        $link->delete();

        return back()->with('success', 'Family link deleted.');
    }
}
