<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\Auth\Register;  // registo de user
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\DashboardController; // está
use App\Http\Controllers\ReservaController; // está
use App\Http\Controllers\PontosController; // adicionei 
use App\Http\Controllers\ListaEsperaController; // adicionei

// Route::get('/', function () {
//     return view('welcome');
// });

// home page
Route::get('/', function () {
    return view('homepage');
});

// REGISTER
Route::get('/register', function() {
    return view('auth.register');
});
Route::post('/register', Register::class);

// LOGIN
Route::get('/login', function() {
    return view('auth.login');
});
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');

// LOGOUT
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:utilizador')
    ->name('logout');

Route::middleware(['auth:utilizador'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Reservas
    Route::get('/reservas', [ReservaController::class, 'index'])->name('reservas.index');
    Route::get('/reservas/criar', [ReservaController::class, 'create'])->name('reservas.create');
    Route::post('/reservas', [ReservaController::class, 'store'])->name('reservas.store');
    Route::get('/reservas/{id}', [ReservaController::class, 'show'])->name('reservas.show');
    Route::delete('/reservas/{id}/cancelar', [ReservaController::class, 'destroy'])->name('reservas.destroy');
    
    // API para lugares disponíveis
    Route::get('/api/lugares/disponiveis', [ReservaController::class, 'getDisponibilidade']);
    
    // Pontos
    Route::get('/pontos', [PontosController::class, 'index'])->name('pontos.index');
    
    // Lista de Espera
    Route::get('/lista-espera', [ListaEsperaController::class, 'index'])->name('lista-espera.index');
    Route::post('/lista-espera', [ListaEsperaController::class, 'store'])->name('lista-espera.store');
    Route::delete('/lista-espera/{id}', [ListaEsperaController::class, 'destroy'])->name('lista-espera.destroy');
    Route::post('/lista-espera/{id}/aceitar', [ListaEsperaController::class, 'accept'])->name('lista-espera.accept');
});

// ROTAS ADMIN
Route::middleware(['auth:utilizador', 'isAdmin'])->group(function () {

    Route::get('/admin/lista-espera', [ListaEsperaController::class, 'index'])->name('admin.lista-espera.index');

});

