<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentMail;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validateWithBag('appointment', [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required'],
            'appointment_type' => ['required', 'string', 'in:reservation_inquiry,lot_viewing,contract_signing,interment_consultation,payment_arrangement,document_submission,maintenance_service,other'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
            'consent' => ['accepted'],
        ]);

        Appointment::create($validated);

        Mail::to(config('mail.from.address'))->send(new AppointmentMail($validated));

        return back()->with('appointment_status', 'Your appointment request has been submitted. We will contact you shortly to confirm.');
    }
}
