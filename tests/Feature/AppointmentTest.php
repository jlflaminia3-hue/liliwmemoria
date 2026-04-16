<?php

use Illuminate\Support\Facades\Mail;

it('accepts valid appointment form submissions', function () {
    Mail::fake();

    $response = $this->post(route('appointment.store'), [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane@example.com',
        'phone' => '555-1234',
        'appointment_date' => now()->addDays(3)->format('Y-m-d'),
        'appointment_time' => '10:00',
        'appointment_type' => 'lot_viewing',
        'subject' => 'Test Appointment',
        'message' => 'I would like to schedule a lot viewing.',
        'consent' => '1',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('appointment_status');

    $this->assertDatabaseHas('appointments', [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane@example.com',
        'phone' => '555-1234',
        'appointment_type' => 'lot_viewing',
        'status' => 'pending',
    ]);
});

it('validates required fields for appointment form', function () {
    $response = $this->post(route('appointment.store'), []);

    $response->assertSessionHasErrors([
        'first_name',
        'last_name',
        'email',
        'phone',
        'appointment_date',
        'appointment_time',
        'appointment_type',
        'consent',
    ], '', 'appointment');
});

it('validates appointment date is not in the past', function () {
    $response = $this->post(route('appointment.store'), [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane@example.com',
        'phone' => '555-1234',
        'appointment_date' => now()->subDays(1)->format('Y-m-d'),
        'appointment_time' => '10:00',
        'appointment_type' => 'lot_viewing',
        'consent' => '1',
    ]);

    $response->assertSessionHasErrors(['appointment_date'], '', 'appointment');
});

it('validates appointment type is valid', function () {
    $response = $this->post(route('appointment.store'), [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane@example.com',
        'phone' => '555-1234',
        'appointment_date' => now()->addDays(3)->format('Y-m-d'),
        'appointment_time' => '10:00',
        'appointment_type' => 'invalid_type',
        'consent' => '1',
    ]);

    $response->assertSessionHasErrors(['appointment_type'], '', 'appointment');
});
