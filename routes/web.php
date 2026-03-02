<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Register routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Login routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require authentication)
Route::middleware('auth')->group(function () {

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('auth');  // Only accessible when logged in

    // Notes routes
    Route::resource('notes', NoteController::class);
});

// Keep your test routes
Route::get('/count', function (Illuminate\Http\Request $request) {
    $count = $request->session()->get('visit_count', 0);
    $count++;
    $request->session()->put('visit_count', $count);

    return "You've visited this page {$count} times";
});

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
