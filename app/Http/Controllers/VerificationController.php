<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Otp;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class VerificationController extends Controller
{
    public function verify($token)
    {
        $user = User::where('remember_token', $token)->firstOrFail();

        $user->email_verified_at = now();
        $user->remember_token = null;
        $user->save();

        return redirect()->route('login.show')->with('status', 'Your email has been verified.');
    }
    public function otp(Request $request)
    {
   
            $otp = Otp::where('otp', $request->otp)
                       ->where('expires_at', '>', Carbon::now())
                       ->first();
    
            if (!$otp) {
                return response()->json(['error' => 'Invalid or expired OTP!'], 404);
            }
    
            $user = $otp->user;
            $otp->delete();
    
            // Log the user in
           
    
            return redirect()->route('dashboard');
     
    }
}
