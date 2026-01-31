<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ListaEsperaController;
use App\Http\Controllers\PontosController;
use App\Http\Controllers\ReportController;

// Rotas Públicas

Route::post('/login', [AuthController::class, 'login']);

// Rotas Protegidas

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Reservas
    Route::get('/reservas', [ReservaController::class, 'index']);
    Route::get('/reservas/{id}', [ReservaController::class, 'show']);
    Route::post('/reservas', [ReservaController::class, 'store']);
    Route::patch('/reservas/{id}/cancelar', [ReservaController::class, 'cancelar']);
    Route::get('/disponibilidade', [ReservaController::class, 'disponibilidade']);

    // Lista de espera
    Route::get('/lista-espera', [ListaEsperaController::class, 'index']);
    Route::post('/lista-espera/notificar', [ListaEsperaController::class, 'notificar']);

    // Pontos
    Route::get('/movimento-pontos', [PontosController::class, 'index']);
    Route::patch('/users/{id}/pontos', [PontosController::class, 'ajustar']);

    // Reports
    Route::post('/reports', [ReportController::class, 'store']);
    Route::get('/reports/pendentes', [ReportController::class, 'pendentes']);
    Route::patch('/reports/{id}/validar', [ReportController::class, 'validar']);
    Route::patch('/reports/{id}/rejeitar', [ReportController::class, 'rejeitar']);

});
