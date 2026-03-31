<?php

use App\Models\Client;
use App\Models\MaintenanceRecord;
use App\Models\User;

it('forbids non-master admins from creating maintenance records', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $client = Client::create(['first_name' => 'Juan', 'last_name' => 'Dela Cruz']);

    $this
        ->actingAs($admin)
        ->post(route('admin.clients.maintenance.store', $client), [
            'service_type' => 'general',
            'status' => 'scheduled',
            'service_date' => '2026-03-31',
            'amount' => '100.00',
            'notes' => 'Test',
        ])
        ->assertForbidden();

    expect(MaintenanceRecord::query()->count())->toBe(0);
});

it('allows master admin to create maintenance records', function () {
    $master = User::factory()->create(['role' => 'master_admin']);
    $client = Client::create(['first_name' => 'Maria', 'last_name' => 'Santos']);

    $this
        ->actingAs($master)
        ->post(route('admin.clients.maintenance.store', $client), [
            'service_type' => 'cleaning',
            'status' => 'scheduled',
            'service_date' => '2026-03-31',
            'amount' => '250.00',
            'notes' => 'Cleaning service',
        ])
        ->assertRedirect();

    $record = MaintenanceRecord::query()->first();
    expect($record)->not->toBeNull();
    expect($record->client_id)->toBe($client->id);
    expect($record->service_type)->toBe('cleaning');
    expect((string) $record->amount)->toBe('250.00');
    expect($record->created_by)->toBe($master->id);
});

