<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Services\LotStateService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class ExpireReservations extends Command
{
    protected $signature = 'reservations:expire';

    protected $description = 'Expire reservations that passed their expiry date and free up lots.';

    public function handle(LotStateService $lotState): int
    {
        $today = CarbonImmutable::today();
        $lotIds = Reservation::expireDue($today);

        foreach ($lotIds as $lotId) {
            $lotState->sync((int) $lotId);
        }

        $this->info('Expired reservations processed: '.count($lotIds).' lot(s) updated.');

        return Command::SUCCESS;
    }
}

