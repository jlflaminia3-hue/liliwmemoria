<?php

namespace App\Services;

use App\Models\ClientLotOwnership;
use App\Models\Deceased;
use App\Models\Lot;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class LotStateService
{
    public function sync(int $lotId): void
    {
        DB::transaction(function () use ($lotId) {
            $lot = Lot::query()->lockForUpdate()->find($lotId);
            if (! $lot) {
                return;
            }

            $today = CarbonImmutable::today();

            $activeInterment = Deceased::query()
                ->with('client')
                ->where('lot_id', $lotId)
                ->where('status', '!=', 'exhumed')
                ->latest('burial_date')
                ->latest('id')
                ->first();

            if ($activeInterment) {
                Reservation::query()
                    ->where('lot_id', $lotId)
                    ->where('status', Reservation::STATUS_ACTIVE)
                    ->update([
                        'status' => Reservation::STATUS_FULFILLED,
                        'fulfilled_at' => now(),
                    ]);

                $lot->status = 'occupied';
                $lot->is_occupied = true;

                if ($activeInterment->client) {
                    $lot->name = $activeInterment->client->full_name;
                }

                $lot->save();

                return;
            }

            $activeReservation = Reservation::query()
                ->with('client')
                ->active($today)
                ->where('lot_id', $lotId)
                ->latest('reserved_at')
                ->latest('id')
                ->first();

            if ($activeReservation?->client) {
                $lot->status = 'reserved';
                $lot->is_occupied = false;
                $lot->name = $activeReservation->client->full_name;
                $lot->save();

                return;
            }

            $latestOwnership = ClientLotOwnership::query()
                ->with('client')
                ->where('lot_id', $lotId)
                ->where(function ($q) use ($today) {
                    $q->whereNull('ended_at')->orWhere('ended_at', '>=', $today->toDateString());
                })
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->first();

            $lot->is_occupied = false;

            if ($latestOwnership?->client) {
                $lot->status = 'reserved';
                $lot->name = $latestOwnership->client->full_name;
            } else {
                $lot->status = 'available';
                $lot->name = 'Unassigned';
            }

            $lot->save();
        }, 3);
    }
}
