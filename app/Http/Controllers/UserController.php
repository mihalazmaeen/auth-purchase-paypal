<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Setting;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function Dashboard()
    {
        if(Auth::user()->name == 'admin'){
            $users=User::where('name','!=', Auth::user()->name)->get();
            $settings = Setting::all()->pluck('value', 'key')->toArray();
            return view('user.dashboard', compact('users','settings'));
        }else
        return view('user.dashboard');
        
    }
    public function Checkout($productName, $price)
    {

        $productName = urldecode($productName);
        return view('user.checkout', ['productName' => $productName, 'price' => $price]);
    }
    public function SetSetting(Request $request)
    {
        $request->validate([
            'paypal_client_id' => 'required|string',
            'paypal_secret' => 'required|string',
        ]);

        Setting::updateOrCreate(['key' => 'paypal_client_id'], ['value' => $request->paypal_client_id]);
        Setting::updateOrCreate(['key' => 'paypal_secret'], ['value' => $request->paypal_secret]);

        return redirect()->route('dashboard')->with('success', 'Settings saved successfully.');
    }
}
