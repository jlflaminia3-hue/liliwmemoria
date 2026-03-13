<?php

namespace App\Http\Controllers;

use App\Mail\ContactUsMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'reason' => ['required', 'string', 'in:question,burial_plots,mausoleum'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        Mail::to(config('mail.from.address'))->send(new ContactUsMail($validated));

        return back()->with('status', 'Thanks for reaching out! We will contact you shortly.');
    }
}
