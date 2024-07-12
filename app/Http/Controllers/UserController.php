<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function Dashboard()
    {
        return view('user.dashboard');
    }
    public function Checkout($productName, $price)
    {

        $productName = urldecode($productName);
        return view('user.checkout', ['productName' => $productName, 'price' => $price]);
    }
}
