<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/register', [AuthController::class,'showRegister'])->name('register');
Route::post('/register',[AuthController::class,'register']);


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');  // Only accessible when logged in

// Keep your test routes
Route::get('/count', function (Illuminate\Http\Request $request) {
    $count = $request->session()->get('visit_count', 0);
    $count++;
    $request->session()->put('visit_count', $count);
    return "You've visited this page {$count} times";
});

// Login routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/session-debug', function (Illuminate\Http\Request $request) {
    return [
        'cookie_value' => $request->cookie('laravel_session'),
        'session_id' => $request->session()->getId(),
        'session_name' => config('session.cookie'),
        'session_driver' => config('session.driver'),
        'session_files_path' => storage_path('framework/sessions'),
        'auth_user_id' => $request->session()->get('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'),
    ];
});