<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientFamilyLink;
use App\Models\Lot;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'has_lots' => ['nullable', Rule::in(['yes', 'no'])],
            'per_page' => ['nullable', Rule::in([10, 20, 50, 100])],
        ]);

        $search = trim((string) ($validated['search'] ?? ''));
        $hasLots = (string) ($validated['has_lots'] ?? '');
        $perPage = (int) ($validated['per_page'] ?? 20);

        $query = Client::query();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%')
                    ->orWhere('address_line1', 'like', '%'.$search.'%')
                    ->orWhere('address_line2', 'like', '%'.$search.'%')
                    ->orWhere('barangay', 'like', '%'.$search.'%')
                    ->orWhere('city', 'like', '%'.$search.'%')
                    ->orWhere('province', 'like', '%'.$search.'%')
                    ->orWhere('postal_code', 'like', '%'.$search.'%')
                    ->orWhere('country', 'like', '%'.$search.'%');
            });
        }

        if ($hasLots === 'yes') {
            $query->whereHas('lotOwnerships');
        }

        if ($hasLots === 'no') {
            $query->whereDoesntHave('lotOwnerships');
        }

        $clients = $query
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate($perPage)
            ->withQueryString();

        $statsBase = Client::query();
        $stats = [
            'total' => (clone $statsBase)->count(),
            'with_email' => (clone $statsBase)->whereNotNull('email')->where('email', '!=', '')->count(),
            'with_phone' => (clone $statsBase)->whereNotNull('phone')->where('phone', '!=', '')->count(),
            'with_lots' => (clone $statsBase)->whereHas('lotOwnerships')->count(),
        ];

        return view('admin.clients.index', compact('clients', 'stats'));
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
