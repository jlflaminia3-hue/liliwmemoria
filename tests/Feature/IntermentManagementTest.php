<?php

use App\Models\Client;
use App\Models\Deceased;
use App\Models\Lot;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('lets an admin create a confirmed interment and marks the lot occupied', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    $client = Client::create([
        'first_name' => 'Maria',
        'last_name' => 'Santos',
    ]);

    $lot = Lot::create([
        'lot_number' => 12,
        'name' => 'Unassigned',
        'section' => 'phase_1',
        'status' => 'available',
        'is_occupied' => false,
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('admin.interments.store'), [
            'client_id' => $client->id,
            'lot_id' => $lot->id,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'date_of_death' => '2026-04-01',
            'burial_date' => '2026-04-03',
            'status' => 'confirmed',
            'death_certificate' => UploadedFile::fake()->create('death-certificate.pdf', 64, 'application/pdf'),
            'burial_permit' => UploadedFile::fake()->create('burial-permit.pdf', 64, 'application/pdf'),
        ]);

    $response->assertRedirect(route('admin.interments.index'));

    $this->assertDatabaseHas('deceased', [
        'client_id' => $client->id,
        'lot_id' => $lot->id,
        'first_name' => 'Juan',
        'last_name' => 'Dela Cruz',
        'status' => 'confirmed',
    ]);

    expect($lot->fresh()->status)->toBe('occupied')
        ->and($lot->fresh()->is_occupied)->toBeTrue()
        ->and($lot->fresh()->name)->toBe($client->full_name);
});

it('requires a burial permit before confirming an interment', function () {
    $user = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    $lot = Lot::create([
        'lot_number' => 13,
        'name' => 'Unassigned',
        'section' => 'phase_1',
        'status' => 'available',
        'is_occupied' => false,
    ]);

    $response = $this
        ->actingAs($user)
        ->from(route('admin.interments.index'))
        ->post(route('admin.interments.store'), [
            'lot_id' => $lot->id,
            'first_name' => 'Ana',
            'last_name' => 'Reyes',
            'date_of_death' => '2026-04-01',
            'burial_date' => '2026-04-03',
            'status' => 'confirmed',
        ]);

    $response->assertRedirect(route('admin.interments.index'));
    $response->assertSessionHasErrors('burial_permit');
});

it('returns the lot to available when the only interment is marked exhumed', function () {
    $user = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    $lot = Lot::create([
        'lot_number' => 14,
        'name' => 'Occupied Lot',
        'section' => 'phase_1',
        'status' => 'occupied',
        'is_occupied' => true,
    ]);

    $deceased = Deceased::create([
        'lot_id' => $lot->id,
        'first_name' => 'Jose',
        'last_name' => 'Garcia',
        'burial_date' => '2026-04-02',
        'status' => 'pending',
    ]);

    $response = $this
        ->actingAs($user)
        ->put(route('admin.interments.update', $deceased), [
            'lot_id' => $lot->id,
            'first_name' => 'Jose',
            'last_name' => 'Garcia',
            'burial_date' => '2026-04-02',
            'status' => 'exhumed',
        ]);

    $response->assertRedirect(route('admin.interments.index'));

    expect($lot->fresh()->status)->toBe('available')
        ->and($lot->fresh()->is_occupied)->toBeFalse()
        ->and($lot->fresh()->name)->toBe('Unassigned');
});
