<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

class AdminController extends Controller
{
        public function AdminLogout(Request $request){
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }


        public function AdminLogin(Request $request){
            $credentials = $request->only('email','password');
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $verifivationCode = random_int(100000,999999);
                session(['verification_code' => $verifivationCode, 'user_id' => $user->id]);
                Mail::to($user->email)->send(new VerificationCodeMail($verifivationCode));
                Auth::logout();
                return redirect()->route('custom.verification.form')->with('status','Verification code sent to your Email');
            }
            return redirect()->back()->withErrors(['email' => 'Invalid Credentials Provided']);
        }

        public function ShowVerification(){
            return view('auth.verify');
        }

        public function VerificationVerify(Request $request){
            $request->validate([
                'code' => 'required|digits:6',
            ]);
            $verificationCode = session('verification_code');
            $userId = session('user_id');
            if ($request->code == $verificationCode) {
                $user = User::find($userId);
                Auth::login($user);
                session()->forget(['verification_code', 'user_id']);
                return redirect()->route('dashboard');
            }
            return redirect()->back()->withErrors(['code' => 'Invalid verification code']);
        }
}
