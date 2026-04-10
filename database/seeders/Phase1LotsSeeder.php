<?php

namespace Database\Seeders;

use App\Services\LotLayoutService;
use Illuminate\Database\Seeder;

class Phase1LotsSeeder extends Seeder
{
    public function run(): void
    {
        app(LotLayoutService::class)->syncSection('phase_1');
    }
}
