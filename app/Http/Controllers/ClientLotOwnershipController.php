<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientLotOwnership;
use App\Models\Deceased;
use App\Models\Lot;
use App\Services\LotStateService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientLotOwnershipController extends Controller
{
    public function store(Request $request, Client $client, LotStateService $lotState)
    {
        $validated = $request->validate([
            'lot_id' => 'required|string|max:32',
            'ownership_type' => 'nullable|in:owner,co-owner,authorized',
            'started_at' => 'nullable|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
            'notes' => 'nullable|string',
        ]);

        $parsed = Lot::parseLotId($validated['lot_id']);
        if (! $parsed) {
            return back()->withErrors(['lot_id' => 'Invalid Lot ID.']);
        }

        $lot = Lot::query()
            ->where('section', $parsed['section'])
            ->where('lot_number', $parsed['lot_number'])
            ->firstOrFail();
        $isAvailable = ($lot->status === 'available') || ($lot->status === null && $lot->is_occupied === false);
        if (! $isAvailable) {
            return back()->withErrors(['lot_id' => 'Selected lot is not available.']);
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

        $lotState->sync((int) $lot->id);

        return back()->with('success', 'Ownership record saved.');
    }

    public function destroy(Client $client, ClientLotOwnership $ownership, LotStateService $lotState)
    {
        if ($ownership->client_id !== $client->id) {
            abort(404);
        }

        $lotId = $ownership->lot_id;
        $ownership->delete();
        $lotState->sync((int) $lotId);

        return back()->with('success', 'Ownership record deleted.');
    }

    public function transfer(Request $request, Client $client, ClientLotOwnership $ownership, LotStateService $lotState)
    {
        if ($ownership->client_id !== $client->id) {
            abort(404);
        }

        $validated = $request->validate([
            'new_client_id' => 'required|integer|exists:clients,id',
            'ownership_type' => 'nullable|in:owner,co-owner,authorized',
            'effective_date' => 'nullable|date|before_or_equal:today',
            'reason' => 'nullable|in:transfer,exhumation',
            'notes' => 'nullable|string',
        ]);

        $newClientId = (int) $validated['new_client_id'];
        if ($newClientId === (int) $client->id) {
            return back()
                ->withErrors(['new_client_id' => 'New owner must be a different client.'])
                ->withInput();
        }

        $newClient = Client::query()->find($newClientId);
        if (! $newClient) {
            return back()
                ->withErrors(['new_client_id' => 'New owner not found.'])
                ->withInput();
        }

        $lot = $ownership->lot()->first();
        if (! $lot) {
            return back()
                ->withErrors(['new_client_id' => 'Cannot transfer: lot record is missing.'])
                ->withInput();
        }

        $hasActiveInterment = Deceased::query()
            ->where('lot_id', $lot->id)
            ->where('status', '!=', 'exhumed')
            ->exists();

        if ($hasActiveInterment) {
            return back()
                ->withErrors(['new_client_id' => 'Cannot transfer ownership while the lot has an active interment. Mark the interment as exhumed first.'])
                ->withInput();
        }

        $effectiveDate = CarbonImmutable::parse($validated['effective_date'] ?? now())->toDateString();
        $newOwnershipType = $validated['ownership_type'] ?? 'owner';
        $reason = $validated['reason'] ?? null;
        $notes = trim((string) ($validated['notes'] ?? ''));

        $transferNote = 'Ownership transferred to '.$newClient->full_name.' (Client #'.$newClientId.') on '.$effectiveDate
            .($reason ? ' ('.$reason.')' : '')
            .($notes !== '' ? ' — '.$notes : '');

        $incomingNote = 'Ownership transferred from '.$client->full_name.' (Client #'.$client->id.') on '.$effectiveDate
            .($reason ? ' ('.$reason.')' : '')
            .($notes !== '' ? ' — '.$notes : '');

        DB::transaction(function () use ($ownership, $effectiveDate, $transferNote, $newClientId, $lot, $newOwnershipType, $incomingNote) {
            $ownership->ended_at = $effectiveDate;
            $ownership->notes = $this->appendNote((string) ($ownership->notes ?? ''), $transferNote);
            $ownership->save();

            $target = ClientLotOwnership::query()
                ->where('client_id', $newClientId)
                ->where('lot_id', $lot->id)
                ->first();

            if ($target) {
                $target->ownership_type = $newOwnershipType;
                $target->started_at = $effectiveDate;
                $target->ended_at = null;
                $target->notes = $this->appendNote((string) ($target->notes ?? ''), $incomingNote);
                $target->save();
            } else {
                ClientLotOwnership::query()->create([
                    'client_id' => $newClientId,
                    'lot_id' => $lot->id,
                    'ownership_type' => $newOwnershipType,
                    'started_at' => $effectiveDate,
                    'ended_at' => null,
                    'notes' => $incomingNote,
                ]);
            }
        }, 3);

        $lotState->sync((int) $lot->id);

        return back()->with('success', 'Ownership transferred.');
    }

    private function appendNote(string $existing, string $note): string
    {
        $existing = trim($existing);
        $note = trim($note);

        if ($note === '') {
            return $existing;
        }

        if ($existing === '') {
            return $note;
        }

        return $existing."\n".$note;
    }
}
