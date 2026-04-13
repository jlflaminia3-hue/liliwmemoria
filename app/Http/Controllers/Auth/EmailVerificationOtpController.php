<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmailVerificationOtpController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            if (in_array($user->role, ['admin', 'master_admin'], true)) {
                return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
            }

            return redirect()->route('approval.pending');
        }

        if (! $user->email_verification_otp_hash || ! $user->email_verification_otp_expires_at) {
            return back()->withErrors([
                'otp' => 'Please request a new verification code.',
            ]);
        }

        if (now()->greaterThan($user->email_verification_otp_expires_at)) {
            return back()->withErrors([
                'otp' => 'This verification code has expired. Please request a new one.',
            ]);
        }

        if (! Hash::check($request->string('otp')->toString(), $user->email_verification_otp_hash)) {
            return back()->withErrors([
                'otp' => 'Invalid verification code.',
            ]);
        }

        $user->forceFill([
            'email_verification_otp_hash' => null,
            'email_verification_otp_expires_at' => null,
            'email_verification_otp_sent_at' => null,
        ]);

        if ($user->markEmailAsVerified()) {
            $user->save();
            event(new Verified($user));
        }

        if (in_array($user->role, ['admin', 'master_admin'], true)) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        return redirect()->route('approval.pending');
    }
}
