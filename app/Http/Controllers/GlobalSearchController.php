<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Deceased;
use App\Models\Lot;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = trim($request->input('q', ''));

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];
        $searchTerm = '%'.$query.'%';

        $clients = $this->searchClients($searchTerm, $query);
        $deceased = $this->searchDeceased($searchTerm, $query);
        $lots = $this->searchLots($searchTerm, $query);
        $reservations = $this->searchReservations($searchTerm, $query);

        $results = array_merge($clients, $deceased, $lots, $reservations);

        usort($results, fn ($a, $b) => $b['relevance'] <=> $a['relevance']);

        return response()->json([
            'results' => array_slice($results, 0, 15),
            'count' => count($results),
        ]);
    }

    private function searchClients(string $searchTerm, string $query): array
    {
        $clients = Client::where(function ($q) use ($searchTerm) {
            $q->where('first_name', 'LIKE', $searchTerm)
                ->orWhere('last_name', 'LIKE', $searchTerm)
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$searchTerm])
                ->orWhere('email', 'LIKE', $searchTerm)
                ->orWhere('phone', 'LIKE', $searchTerm);
        })
            ->with('lots')
            ->limit(5)
            ->get();

        return $clients->map(function ($client) use ($query) {
            $relevance = $this->calculateRelevance($client->full_name, $query);
            $lotsCount = $client->lots->count();

            return [
                'type' => 'client',
                'type_label' => 'Client',
                'id' => $client->id,
                'title' => $client->full_name,
                'subtitle' => $client->email ?? ($client->phone ?? 'No contact info'),
                'meta' => $lotsCount > 0 ? "{$lotsCount} lot(s) owned" : null,
                'url' => route('admin.clients.show', $client),
                'relevance' => $relevance,
            ];
        })->toArray();
    }

    private function searchDeceased(string $searchTerm, string $query): array
    {
        $deceased = Deceased::where(function ($q) use ($searchTerm) {
            $q->where('first_name', 'LIKE', $searchTerm)
                ->orWhere('last_name', 'LIKE', $searchTerm)
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$searchTerm]);
        })
            ->with(['client', 'lot'])
            ->limit(5)
            ->get();

        return $deceased->map(function ($record) use ($query) {
            $relevance = $this->calculateRelevance($record->full_name, $query);
            $lotInfo = $record->lot ? "Lot {$record->lot->lot_number}" : null;

            return [
                'type' => 'deceased',
                'type_label' => 'Deceased',
                'id' => $record->id,
                'title' => $record->full_name,
                'subtitle' => $record->client ? $record->client->full_name : 'No client',
                'meta' => $lotInfo,
                'url' => route('admin.interment-payments.show', $record),
                'relevance' => $relevance,
            ];
        })->toArray();
    }

    private function searchLots(string $searchTerm, string $query): array
    {
        $lots = Lot::where(function ($q) use ($searchTerm) {
            $q->where('lot_number', 'LIKE', $searchTerm)
                ->orWhere('name', 'LIKE', $searchTerm)
                ->orWhere('section', 'LIKE', $searchTerm);
        })
            ->limit(5)
            ->get();

        return $lots->map(function ($lot) use ($query) {
            $relevance = $this->calculateRelevance($lot->lot_number.' '.$lot->name, $query);
            $statusLabel = match (true) {
                $lot->is_occupied => 'Occupied',
                $lot->status === 'reserved' => 'Reserved',
                default => 'Available',
            };

            return [
                'type' => 'lot',
                'type_label' => 'Lot',
                'id' => $lot->id,
                'title' => "{$lot->lot_number} - {$lot->name}",
                'subtitle' => ucfirst(str_replace('_', ' ', $lot->section)),
                'meta' => $statusLabel,
                'url' => route('admin.lots.edit', $lot),
                'relevance' => $relevance,
            ];
        })->toArray();
    }

    private function searchReservations(string $searchTerm, string $query): array
    {
        $reservations = Reservation::where(function ($q) use ($searchTerm) {
            $q->where('reservation_number', 'LIKE', $searchTerm)
                ->orWhere('notes', 'LIKE', $searchTerm)
                ->orWhereHas('client', function ($cq) use ($searchTerm) {
                    $cq->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$searchTerm]);
                });
        })
            ->with('client')
            ->limit(3)
            ->get();

        return $reservations->map(function ($reservation) use ($query) {
            $relevance = $this->calculateRelevance($reservation->reservation_number, $query);
            $statusLabel = ucfirst($reservation->status ?? 'pending');

            return [
                'type' => 'reservation',
                'type_label' => 'Reservation',
                'id' => $reservation->id,
                'title' => $reservation->reservation_number,
                'subtitle' => $reservation->client ? $reservation->client->full_name : 'No client',
                'meta' => $statusLabel,
                'url' => route('admin.reservations.index', ['search' => $reservation->reservation_number]),
                'relevance' => $relevance,
            ];
        })->toArray();
    }

    private function calculateRelevance(string $text, string $query): int
    {
        $text = strtolower($text);
        $query = strtolower($query);

        if ($text === $query) {
            return 100;
        }

        if (str_starts_with($text, $query)) {
            return 80;
        }

        if (str_contains($text, $query)) {
            return 60;
        }

        return 20;
    }
}
