<?php

namespace App\Services\Reservations;

use App\Models\Client;
use App\Models\ClientLotOwnership;
use App\Models\Lot;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class LotReservationService
{
    public function reserve(
        Client $client,
        int $lotId,
        ?string $startedAt,
        ?string $endedAt,
        ?string $lotKind,
        string $lotFieldForErrors = 'lot_id',
    ): void {
        $lot = Lot::query()->lockForUpdate()->findOrFail($lotId);

        $hasExistingOwnership = ClientLotOwnership::query()
            ->where('client_id', $client->id)
            ->where('lot_id', $lot->id)
            ->exists();

        $isAvailable = ($lot->status === 'available') || ($lot->status === null && $lot->is_occupied === false);
        if (! $hasExistingOwnership && ! $isAvailable) {
            throw ValidationException::withMessages([$lotFieldForErrors => 'Selected lot is not available.']);
        }

        ClientLotOwnership::updateOrCreate(
            ['client_id' => $client->id, 'lot_id' => $lot->id],
            [
                'ownership_type' => 'owner',
                'started_at' => $startedAt ? Carbon::parse($startedAt)->toDateString() : null,
                'ended_at' => $endedAt ? Carbon::parse($endedAt)->toDateString() : null,
                'notes' => null,
            ]
        );

        if (! empty($lotKind) && empty($lot->section)) {
            $lot->section = $lotKind;
        }

        $lot->save();
    }

    public function clearOwnershipIfLotChanged(Client $client, ?int $oldLotId, ?int $newLotId): void
    {
        if (! $oldLotId || $oldLotId === $newLotId) {
            return;
        }

        $ownership = ClientLotOwnership::query()
            ->where('client_id', $client->id)
            ->where('lot_id', $oldLotId)
            ->first();

        if ($ownership) {
            $ownership->delete();
        }
    }
}
