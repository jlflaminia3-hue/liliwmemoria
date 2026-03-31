<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientFamilyLink;
use App\Models\Lot;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:clients,email',
            'phone' => 'nullable|string|max:50',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $client = Client::create($validated);

        return redirect()->route('admin.clients.show', $client)->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        $client->load([
            'lotOwnerships.lot',
            'contracts.lot',
            'communications.creator',
            'maintenanceRecords.lot',
            'maintenanceRecords.contract',
            'maintenanceRecords.creator',
        ]);

        $familyLinks = ClientFamilyLink::query()
            ->with(['client', 'relatedClient'])
            ->where('client_id', $client->id)
            ->orWhere('related_client_id', $client->id)
            ->get();

        $lots = Lot::query()
            ->orderBy('lot_number')
            ->get(['id', 'lot_number', 'name', 'section']);

        $availableLots = Lot::query()
            ->where(function ($query) {
                $query
                    ->where('status', 'available')
                    ->orWhere(function ($query) {
                        $query->whereNull('status')->where('is_occupied', false);
                    });
            })
            ->orderBy('lot_number')
            ->get(['id', 'lot_number', 'name', 'section']);

        $otherClients = Client::query()
            ->whereKeyNot($client->id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('admin.clients.show', compact('client', 'lots', 'availableLots', 'otherClients', 'familyLinks'));
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:clients,email,'.$client->id,
            'phone' => 'nullable|string|max:50',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect()->route('admin.clients.show', $client)->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('admin.clients.index')->with('success', 'Client deleted successfully.');
    }
}
