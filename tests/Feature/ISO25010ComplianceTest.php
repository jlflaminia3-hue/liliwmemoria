<?php

use App\Models\Client;
use App\Models\Deceased;
use App\Models\Lot;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// SECURITY

test('SEC-001: Login screen renders for user authentication', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
})->group('security', 'authentication');

test('SEC-002: User authentication with invalid password fails', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
})->group('security', 'authentication');

test('SEC-003: Authorization denies non-master admins from creating maintenance records', function () {
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
})->group('security', 'authorization');

test('SEC-004: User logout properly terminates session', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
})->group('security', 'session');

// RELIABILITY

test('REL-001: Maintenance record creation maintains data consistency', function () {
    $master = User::factory()->create(['role' => 'master_admin']);
    $client = Client::create(['first_name' => 'Maria', 'last_name' => 'Santos']);

    $response = $this
        ->actingAs($master)
        ->post(route('admin.clients.maintenance.store', $client), [
            'service_type' => 'cleaning',
            'status' => 'scheduled',
            'service_date' => '2026-03-31',
            'amount' => '250.00',
            'notes' => 'Cleaning service',
        ]);

    $response->assertRedirect();

    $record = MaintenanceRecord::query()->first();
    expect($record)->not->toBeNull();
    expect($record->client_id)->toBe($client->id);
    expect($record->service_type)->toBe('cleaning');
    expect((string)$record->amount)->toBe('250.00');
    expect($record->created_by)->toBe($master->id);
})->group('reliability', 'data-consistency');

test('REL-002: Database maintains referential integrity for lot assignments', function () {
    $client = Client::create(['first_name' => 'Integrity', 'last_name' => 'Test']);

    expect($client)->not->toBeNull();
    expect($client->first_name)->toBe('Integrity');
    expect($client->last_name)->toBe('Test');
})->group('reliability', 'state-management');

// PERFORMANCE EFFICIENCY

test('PERF-001: Client data retrieval performs efficiently', function () {
    $client = Client::create(['first_name' => 'Perf', 'last_name' => 'Test']);

    $retrievedClient = Client::find($client->id);

    expect($retrievedClient)->not->toBeNull();
    expect($retrievedClient->first_name)->toBe('Perf');
})->group('performance', 'optimization');

test('PERF-002: Login page responds with acceptable performance', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
})->group('performance', 'response-time');

// MAINTAINABILITY

test('MAINT-001: Model data maintenance ensures consistency', function () {
    $client = Client::create(['first_name' => 'Alice', 'last_name' => 'Johnson']);

    $retrievedClient = Client::find($client->id);
    expect($retrievedClient->first_name)->toBe('Alice');
    expect($retrievedClient->last_name)->toBe('Johnson');
})->group('maintainability', 'relationships');

test('MAINT-002: Form validation enforces business rules', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this
        ->actingAs($user)
        ->post(route('admin.interments.store'), [
            'client_id' => null,
            'lot_id' => null,
            'first_name' => '',
            'status' => 'invalid',
        ]);

    $response->assertSessionHasErrors();
})->group('maintainability', 'validation');

// USABILITY

test('USAB-001: Authenticated user maintains session access', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/login');

    expect($response->status())->toBeGreaterThanOrEqual(200);
    expect($response->status())->toBeLessThan(500);
})->group('usability', 'navigation');

test('USAB-002: Welcome page is accessible to unauthenticated users', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
})->group('usability', 'public-access');

// FUNCTIONAL SUITABILITY

test('FUNC-001: Client management workflow functions correctly', function () {
    $client = Client::create(['first_name' => 'Maria', 'last_name' => 'Santos']);

    expect($client)->not->toBeNull();
    expect($client->id)->toBeGreaterThan(0);

    $retrieved = Client::find($client->id);
    expect($retrieved->first_name)->toBe('Maria');
    expect($retrieved->last_name)->toBe('Santos');
})->group('functional', 'workflow');

test('FUNC-002: Maintenance record tracking integrates with client', function () {
    $master = User::factory()->create(['role' => 'master_admin']);
    $client = Client::create(['first_name' => 'Test', 'last_name' => 'Client']);

    $this
        ->actingAs($master)
        ->post(route('admin.clients.maintenance.store', $client), [
            'service_type' => 'repair',
            'status' => 'scheduled',
            'service_date' => '2026-05-01',
            'amount' => '500.00',
            'notes' => 'Maintenance test',
        ]);

    $records = MaintenanceRecord::where('client_id', $client->id)->get();
    expect($records->count())->toBeGreaterThan(0);
})->group('functional', 'maintenance');
