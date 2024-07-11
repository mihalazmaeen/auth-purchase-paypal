<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('home');
});
Route::get('dashboard', [UserController::class, 'Dashboard'])->name('dashboard');
Route::get('login', [AuthController::class, 'Login'])->name('login.show');
Route::get('register', [AuthController::class, 'Register'])->name('register.show');
Route::get('verify-email', [AuthController::class, 'VerifyEmail'])->name('verify_email.show');
Route::get('otp', [AuthController::class, 'ShowOtp'])->name('otp.show');

Route::post('register-new', [AuthController::class, 'Registration'])->name('register.new');
Route::post('login-user', [AuthController::class, 'LoginUser'])->name('login.user');
Route::get('verify-email/{token}', [VerificationController::class, 'verify'])->name('verify-email');
Route::post('verify-otp', [VerificationController::class, 'otp'])->name('verify-otp');

