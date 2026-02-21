<?php

use Illuminate\Support\Facades\Route; // x1
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\Auth\Register;
use App\Http\Controllers\Auth\FirstAccessPasswordController;
use App\Http\Controllers\DashboardController; // está
use App\Http\Controllers\ReservaController; // está x2
use App\Http\Controllers\ListaEsperaController; // adicionei x3
use App\Http\Controllers\PontosController; // adicionei x4
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SegurancaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HistoricoController;

// Route::get('/', function () {
//     return view('welcome');
// });

// HOMEPAGE
Route::get('/', function () {
    return view('homepage');
});
Route::view('/regras-sistema', 'regras-sistema')->name('regras.sistema');

// AUTH ROUTES
//LOGIN
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', Login::class);

// PRIMEIRO ACESSO (definição de password)
Route::get('/primeiro-acesso/{token}', [FirstAccessPasswordController::class, 'showResetForm'])
    ->name('first-access.form');
Route::post('/primeiro-acesso', [FirstAccessPasswordController::class, 'store'])
    ->name('first-access.store');

//LOGOUT
Route::post('/logout', Logout::class)->name('logout');

// Rotas protegidas (precisam auth)
Route::middleware(['auth:utilizador'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil (todos os utilizadores autenticados)
    Route::get('/perfil', [ProfileController::class, 'show'])->name('perfil.show');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('perfil.password');

    // Área de Segurança
    Route::middleware(['seguranca'])->group(function () {
        Route::get('/seguranca/reservas-hoje', [SegurancaController::class, 'index'])->name('seguranca.reservas.hoje');
        Route::get('/seguranca/reservas-hoje/pendentes', [SegurancaController::class, 'pendentes'])->name('seguranca.reservas.pendentes');
        Route::get('/seguranca/reservas-hoje/validadas', [SegurancaController::class, 'validadas'])->name('seguranca.reservas.validadas');
        Route::post('/seguranca/reservas/{id}/validar', [SegurancaController::class, 'validarChegada'])->name('seguranca.reservas.validar');
        Route::post('/seguranca/reports', [SegurancaController::class, 'storeReport'])->name('seguranca.reports.store');
    });

    // Área de Colaborador/Admin (exceto Segurança)
    Route::middleware(['notSeguranca'])->group(function () {
        // Reservas
        Route::get('/reservas', [ReservaController::class, 'index']);
        Route::get('/reservas/criar', [ReservaController::class, 'create']);
        Route::post('/reservas', [ReservaController::class, 'store']);
        Route::get('/reservas/{id}', [ReservaController::class, 'show']);
        Route::delete('/reservas/{id}/cancelar', [ReservaController::class, 'destroy']);
        Route::get('/api/lugares/disponiveis', [ReservaController::class, 'getDisponibilidade']);

        // Lista de Espera
        Route::get('/lista-espera', [ListaEsperaController::class, 'index']);
        Route::post('/lista-espera', [ListaEsperaController::class, 'store']);
        Route::delete('/lista-espera/{id}', [ListaEsperaController::class, 'destroy']);
        Route::post('/lista-espera/{id}/aceitar', [ListaEsperaController::class, 'accept']);
        Route::get('/lista-espera/{id}/confirmar/{token}', [ListaEsperaController::class, 'confirmFromEmail'])->name('lista-espera.confirmar');

        // Pontos
        Route::get('/pontos', [PontosController::class, 'index']);

        // Relatórios (submissão por colaborador/admin)
        Route::get('/reports/submeter', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    });

    // Admin (apenas para admins)
    Route::middleware(['admin'])->group(function () {
        // Gestão de utilizadores (apenas admin)
        Route::view('/register', 'auth.register');
        Route::post('/register', Register::class);

        // Gestão de pontos (apenas admin)
        Route::get('/admin/pontos', [PontosController::class, 'adminIndex'])->name('admin.pontos.index');
        Route::patch('/admin/pontos/{id}', [PontosController::class, 'adminAdjust'])->name('admin.pontos.adjust');

        // Perfis (apenas admin)
        Route::get('/admin/perfis', [ProfileController::class, 'listAll'])->name('admin.perfis.index');
        Route::get('/admin/perfis/{id}', [ProfileController::class, 'showById'])->name('admin.perfis.show');
        Route::delete('/admin/perfis/{id}', [ProfileController::class, 'deleteUser'])->name('admin.perfis.delete');

        // Gestão global de reservas (apenas admin)
        Route::get('/admin/reservas/{id}/editar', [ReservaController::class, 'edit'])->name('admin.reservas.edit');
        Route::put('/admin/reservas/{id}', [ReservaController::class, 'update'])->name('admin.reservas.update');
        Route::delete('/admin/reservas/{id}/apagar', [ReservaController::class, 'delete'])->name('admin.reservas.delete');

        Route::get('/admin/relatorios', [ReportController::class, 'index'])->name('admin.relatorios.index');
        Route::get('/admin/relatorios/{id}', [ReportController::class, 'show'])->name('admin.relatorios.show');
        Route::patch('/admin/relatorios/{id}/validar', [ReportController::class, 'validar'])->name('admin.relatorios.validar');
        Route::patch('/admin/relatorios/{id}/rejeitar', [ReportController::class, 'rejeitar'])->name('admin.relatorios.rejeitar');

        // Histórico de Utilizador (Graça Neves)
        Route::get('/historico', [HistoricoController::class, 'index']);
        Route::get('/historico/evento/{id}/edit', [HistoricoController::class, 'editEvento']);
        Route::put('/historico/evento/{id}', [HistoricoController::class, 'updateEvento']);
        Route::delete('/historico/evento/{id}', [HistoricoController::class, 'destroyEvento']);
        Route::get('/historico/{id}', [HistoricoController::class, 'show']);
    });
});
