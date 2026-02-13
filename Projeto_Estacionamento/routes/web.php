<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\Auth\Register;  // registo de user
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservaController;

// Route::get('/', function () {
//     return view('welcome');
// });

// home page
Route::get('/', function () {
    return view('homepage');
});

// Registration routes
Route::get('/register', function() {
    return view('auth.register');
});

Route::post('/register', Register::class);

// LOGIN
Route::view('/login', 'auth.login');
    // ->middleware('guest')
    // ->name('login');

Route::post('/login', Login::class)
    ->middleware('guest');

// Logout route
Route::post('/logout', Logout::class)
    ->middleware('auth')
    ->name('logout');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// RESERVAS
// Route::get('/reservas', [ReservaController::class, 'index'])->name('reservas.index');
Route::get('/reservas', [ReservaController::class, 'index']);
Route::get('/reservas/criar', [ReservaController::class, 'create'])->name('reservas.create');
Route::post('/reservas', [ReservaController::class, 'store'])->name('reservas.store');
Route::get('/reservas/{id}', [ReservaController::class, 'show'])->name('reservas.show');
Route::delete('/reservas/{id}/cancelar', [ReservaController::class, 'destroy'])->name('reservas.destroy');

// API para lugares disponíveis
Route::get('/api/lugares/disponiveis', [ReservaController::class, 'getDisponibilidade']);

