<?php

namespace Database\Seeders;

use App\Services\LotLayoutService;
use Illuminate\Database\Seeder;

class GardenLotsSeeder extends Seeder
{
    public function run(): void
    {
        app(LotLayoutService::class)->syncSection('garden_lot');
    }
}
