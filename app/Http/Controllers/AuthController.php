<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use App\Mail\VerifyOtp;
use Illuminate\Support\Facades\Redis;
use App\Models\Otp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function Register()
    {
        return view('auth.register');
    }
    public function Registration(Request $request){

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email), // Convert email to lowercase
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(32),
        ]);

        Mail::to($user->email)->send(new VerifyEmail($user));
        return redirect()->route('verify_email.show');

    }

    public function LoginUser(Request $request){

        
        $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();
        if($user->email_verified_at == null){
            return redirect()->route('verify_email.show');
        }
       

        if (!$user) {
            return response()->json(['error' => 'User not Exist!!!'], 404);
        }

        $otp = rand(100000, 999999);

        $otp=Otp::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(2),
        ]);

       

        Mail::to($user->email)->send(new VerifyOtp($otp));
        return redirect()->route('otp.show');
    }
    public function Login()
    {
        return view('auth.login');
    }
    public function VerifyEmail()
    {
        return view('auth.verify-email');
    }
    public function ShowOtp()
    {
        return view('auth.otp');
    }
    public function Logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.show');
    }
}
