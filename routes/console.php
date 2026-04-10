<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:sync-lot-layout {section}', function (string $section) {
    $count = app(\App\Services\LotLayoutService::class)->syncSection($section);

    $this->info("Synced {$count} lot markers for [{$section}].");
})->purpose('Sync traced lot marker layouts from config.');
