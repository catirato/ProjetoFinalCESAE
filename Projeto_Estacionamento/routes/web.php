<?php

use Illuminate\Support\Facades\Route; // x1
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\Auth\Register;
use App\Http\Controllers\DashboardController; // está 
use App\Http\Controllers\ReservaController; // está x2
use App\Http\Controllers\ListaEsperaController; // adicionei x3
use App\Http\Controllers\PontosController; // adicionei x4
use App\Http\Controllers\ReportController;

// Route::get('/', function () {
//     return view('welcome');
// });

// HOMEPAGE
Route::get('/', function () {
    return view('homepage');
});

// AUTH ROUTES
//LOGIN
Route::get('/login', function () {
    return view('auth.login');
});
Route::post('/login', Login::class);

//REGISTER
Route::view('/register', 'auth.register');
Route::post('/register', Register::class);

//LOGOUT
Route::post('/logout', Logout::class)->name('logout');

// para testar as rotas protegidas
// Route::get('/login-teste', function() {
//     $user = App\Models\User::first(); // ou qualquer utilizador de teste
//     Auth::guard('utilizador')->login($user);
//     return redirect('/dashboard');
// });

// Rotas protegidas (precisam auth)
Route::middleware(['auth:utilizador'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Reservas
    Route::get('/reservas', [ReservaController::class, 'index']);
    Route::get('/reservas/criar', [ReservaController::class, 'create']);
    Route::post('/reservas', [ReservaController::class, 'store']);
    Route::get('/reservas/{id}', [ReservaController::class, 'show']);
    Route::delete('/reservas/{id}/cancelar', [ReservaController::class, 'destroy']);
    
    // Lista de Espera
    Route::get('/lista-espera', [ListaEsperaController::class, 'index']);
    Route::post('/lista-espera', [ListaEsperaController::class, 'store']);
    Route::delete('/lista-espera/{id}', [ListaEsperaController::class, 'destroy']);
    Route::post('/lista-espera/{id}/aceitar', [ListaEsperaController::class, 'accept']);
    
    // Pontos
    Route::get('/pontos', [PontosController::class, 'index']);
    
    // Admin (apenas para admins)
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/relatorios', [ReportController::class, 'index']);
    });
});







// ------------------------ O QUE TINHA -----------------------------
// // HOMEPAGE
// Route::get('/', function () {
//     return view('homepage');
// });

// // REGISTER
// Route::get('/register', function() {
//     return view('auth.register');
// });
// Route::post('/register', Register::class);

// // LOGIN
// Route::get('/login', function() {
//     return view('auth.login');
// });
// Route::get('/login', [AuthController::class, 'login'])->name('login');
// Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');

// // LOGOUT
// Route::post('/logout', [AuthController::class, 'logout'])
//     ->middleware('auth:utilizador')
//     ->name('logout');

// Route::middleware(['auth:utilizador'])->group(function () {
    
//     // Dashboard
//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
//     // Reservas
//     Route::get('/reservas', [ReservaController::class, 'index'])->name('reservas.index');
//     Route::get('/reservas/criar', [ReservaController::class, 'create'])->name('reservas.create');
//     Route::post('/reservas', [ReservaController::class, 'store'])->name('reservas.store');
//     Route::get('/reservas/{id}', [ReservaController::class, 'show'])->name('reservas.show');
//     Route::delete('/reservas/{id}/cancelar', [ReservaController::class, 'destroy'])->name('reservas.destroy');
    
//     // API para lugares disponíveis
//     Route::get('/api/lugares/disponiveis', [ReservaController::class, 'getDisponibilidade']);
    
//     // Pontos
//     Route::get('/pontos', [PontosController::class, 'index'])->name('pontos.index');
    
//     // Lista de Espera
//     Route::get('/lista-espera', [ListaEsperaController::class, 'index'])->name('lista-espera.index');
//     Route::post('/lista-espera', [ListaEsperaController::class, 'store'])->name('lista-espera.store');
//     Route::delete('/lista-espera/{id}', [ListaEsperaController::class, 'destroy'])->name('lista-espera.destroy');
//     Route::post('/lista-espera/{id}/aceitar', [ListaEsperaController::class, 'accept'])->name('lista-espera.accept');
// });

// // ROTAS ADMIN
// Route::middleware(['auth:utilizador', 'isAdmin'])->group(function () {

//     Route::get('/admin/lista-espera', [ListaEsperaController::class, 'index'])->name('admin.lista-espera.index');

// });
// ------------------------ O QUE TINHA -----------------------------






