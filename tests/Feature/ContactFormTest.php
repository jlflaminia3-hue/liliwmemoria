<?php

use App\Mail\ContactUsMail;
use Illuminate\Support\Facades\Mail;

it('accepts valid contact form submissions and sends an email', function () {
    Mail::fake();
    $this->withoutMiddleware();

    $response = $this->post(route('contact.submit'), [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane@example.com',
        'phone' => '555-1234',
        'reason' => 'burial_plots',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('status');

    Mail::assertSent(ContactUsMail::class, function (ContactUsMail $mail) {
        return $mail->hasTo(config('mail.from.address'))
            && $mail->data['first_name'] === 'Jane'
            && $mail->data['reason'] === 'burial_plots';
    });
});

it('validates required fields', function () {
    $this->withoutMiddleware();

    $response = $this->post(route('contact.submit'), []);

    $response->assertSessionHasErrors(['first_name', 'last_name', 'email', 'reason']);
});
