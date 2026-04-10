<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientFamilyLink;
use App\Models\Lot;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Dompdf\Dompdf;
use Dompdf\Options;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $validated = $this->validateClientIndexFilters($request);

        $perPage = (int) ($validated['per_page'] ?? 20);

        $query = $this->applyClientIndexFilters(Client::query(), $validated);

        $query
            ->select('clients.*')
            ->selectSub(function ($q) {
                $q->from('client_communications')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('client_communications.client_id', 'clients.id');
            }, 'last_communication_at')
            ->selectSub(function ($q) {
                $q->from('reservations')
                    ->selectRaw('MAX(updated_at)')
                    ->whereColumn('reservations.client_id', 'clients.id');
            }, 'last_reservation_at')
            ->selectSub(function ($q) {
                $q->from('maintenance_records')
                    ->selectRaw('MAX(updated_at)')
                    ->whereColumn('maintenance_records.client_id', 'clients.id');
            }, 'last_maintenance_at');

        $clients = $query
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate($perPage)
            ->withQueryString();

        $activeCutoff = now()->subDays(30);
        $inactiveCutoff = now()->subMonths(6);
        $monthStart = CarbonImmutable::today()->startOfMonth();
        $monthEnd = $monthStart->endOfMonth();

        $statsBase = Client::query();
        $stats = [
            'total' => (clone $statsBase)->count(),
            'active' => (clone $statsBase)->where(function ($builder) use ($activeCutoff) {
                $builder
                    ->where('created_at', '>=', $activeCutoff)
                    ->orWhere('updated_at', '>=', $activeCutoff)
                    ->orWhereHas('communications', fn ($q) => $q->where('created_at', '>=', $activeCutoff))
                    ->orWhereHas('reservations', fn ($q) => $q->where('updated_at', '>=', $activeCutoff))
                    ->orWhereHas('maintenanceRecords', fn ($q) => $q->where('updated_at', '>=', $activeCutoff));
            })->count(),
            'new_this_month' => (clone $statsBase)->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            'inactive' => (clone $statsBase)
                ->where('created_at', '<', $inactiveCutoff)
                ->where('updated_at', '<', $inactiveCutoff)
                ->whereDoesntHave('communications', fn ($q) => $q->where('created_at', '>=', $inactiveCutoff))
                ->whereDoesntHave('reservations', fn ($q) => $q->where('updated_at', '>=', $inactiveCutoff))
                ->whereDoesntHave('maintenanceRecords', fn ($q) => $q->where('updated_at', '>=', $inactiveCutoff))
                ->count(),
        ];

        $clients->getCollection()->transform(function (Client $client) use ($inactiveCutoff) {
            $lastActivity = collect([
                $client->created_at,
                $client->updated_at,
                $client->last_communication_at ?? null,
                $client->last_reservation_at ?? null,
                $client->last_maintenance_at ?? null,
            ])->filter()->max();

            $client->last_activity_at = $lastActivity ? CarbonImmutable::parse($lastActivity) : null;
            $client->activity_status = ($client->last_activity_at && $client->last_activity_at->lt(CarbonImmutable::parse($inactiveCutoff)))
                ? 'inactive'
                : 'active';

            return $client;
        });

        $growthMonths = [];
        $growthCounts = [];
        return view('admin.clients.index', compact('clients', 'stats'));
    }

    public function analytics()
    {
        return redirect()->route('admin.analytics.clients');
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

    public function exportCsv(Request $request)
    {
        $validated = $this->validateClientIndexFilters($request);
        $query = $this->applyClientIndexFilters(Client::query(), $validated)
            ->select('clients.*')
            ->selectSub(function ($q) {
                $q->from('client_communications')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('client_communications.client_id', 'clients.id');
            }, 'last_communication_at')
            ->selectSub(function ($q) {
                $q->from('reservations')
                    ->selectRaw('MAX(updated_at)')
                    ->whereColumn('reservations.client_id', 'clients.id');
            }, 'last_reservation_at')
            ->selectSub(function ($q) {
                $q->from('maintenance_records')
                    ->selectRaw('MAX(updated_at)')
                    ->whereColumn('maintenance_records.client_id', 'clients.id');
            }, 'last_maintenance_at')
            ->orderBy('last_name')
            ->orderBy('first_name');

        $filename = 'clients_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Name',
                'Email',
                'Phone',
                'Address',
                'Date Added',
                'Last Activity',
                'Status',
            ]);

            $query->chunk(500, function ($clients) use ($out) {
                foreach ($clients as $client) {
                    $address = collect([
                        $client->address_line1,
                        $client->address_line2,
                        $client->barangay,
                        $client->city,
                        $client->province,
                        $client->postal_code,
                        $client->country,
                    ])->filter()->implode(', ');

                    $lastActivity = collect([
                        $client->created_at,
                        $client->updated_at,
                        $client->last_communication_at ?? null,
                        $client->last_reservation_at ?? null,
                        $client->last_maintenance_at ?? null,
                    ])->filter()->max();

                    $inactiveCutoff = now()->subMonths(6);
                    $status = ($lastActivity && CarbonImmutable::parse($lastActivity)->lt(CarbonImmutable::parse($inactiveCutoff)))
                        ? 'inactive'
                        : 'active';

                    fputcsv($out, [
                        $client->full_name,
                        $client->email ?? '',
                        $client->phone ?? '',
                        $address,
                        optional($client->created_at)->toDateString(),
                        $lastActivity ? CarbonImmutable::parse($lastActivity)->toDateString() : '',
                        $status,
                    ]);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $validated = $this->validateClientIndexFilters($request);
        $clients = $this->applyClientIndexFilters(Client::query(), $validated)
            ->select('clients.*')
            ->selectSub(function ($q) {
                $q->from('client_communications')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('client_communications.client_id', 'clients.id');
            }, 'last_communication_at')
            ->selectSub(function ($q) {
                $q->from('reservations')
                    ->selectRaw('MAX(updated_at)')
                    ->whereColumn('reservations.client_id', 'clients.id');
            }, 'last_reservation_at')
            ->selectSub(function ($q) {
                $q->from('maintenance_records')
                    ->selectRaw('MAX(updated_at)')
                    ->whereColumn('maintenance_records.client_id', 'clients.id');
            }, 'last_maintenance_at')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(2000)
            ->get();

        $html = view('admin.clients.exports.pdf', [
            'clients' => $clients,
            'generatedAt' => now(),
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'clients_'.now()->format('Ymd_His').'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function validateClientIndexFilters(Request $request): array
    {
        return $request->validate([
            'search' => 'nullable|string|max:255',
            'has_lots' => ['nullable', Rule::in(['yes', 'no'])],
            'activity' => ['nullable', Rule::in(['active', 'inactive', 'new'])],
            'per_page' => ['nullable', Rule::in([10, 20, 50, 100])],
        ]);
    }

    private function applyClientIndexFilters($query, array $validated)
    {
        $search = trim((string) ($validated['search'] ?? ''));
        $hasLots = (string) ($validated['has_lots'] ?? '');
        $activity = (string) ($validated['activity'] ?? '');

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

        if ($activity === 'active') {
            $cutoff = now()->subDays(30);
            $query->where(function ($builder) use ($cutoff) {
                $builder
                    ->where('created_at', '>=', $cutoff)
                    ->orWhere('updated_at', '>=', $cutoff)
                    ->orWhereHas('communications', fn ($q) => $q->where('created_at', '>=', $cutoff))
                    ->orWhereHas('reservations', fn ($q) => $q->where('updated_at', '>=', $cutoff))
                    ->orWhereHas('maintenanceRecords', fn ($q) => $q->where('updated_at', '>=', $cutoff));
            });
        }

        if ($activity === 'inactive') {
            $cutoff = now()->subMonths(6);
            $query
                ->where('created_at', '<', $cutoff)
                ->where('updated_at', '<', $cutoff)
                ->whereDoesntHave('communications', fn ($q) => $q->where('created_at', '>=', $cutoff))
                ->whereDoesntHave('reservations', fn ($q) => $q->where('updated_at', '>=', $cutoff))
                ->whereDoesntHave('maintenanceRecords', fn ($q) => $q->where('updated_at', '>=', $cutoff));
        }

        if ($activity === 'new') {
            $start = CarbonImmutable::today()->startOfMonth();
            $end = $start->endOfMonth();
            $query->whereBetween('created_at', [$start, $end]);
        }

        return $query;
    }
}
