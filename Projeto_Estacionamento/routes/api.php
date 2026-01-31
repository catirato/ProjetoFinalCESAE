<?php

use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ListaEsperaController;
use App\Http\Controllers\PontosController;
use App\Http\Controllers\ReportController;


Route::get('/reservas', [ReservaController::class,'index']); // lista reservas do dia
Route::get('/reservas/{id}', [ReservaController::class,'show']); // detalhes de uma reserva
Route::post('/reservas', [ReservaController::class,'store']); // criar reserva
Route::patch('/reservas/{id}/cancelar', [ReservaController::class,'cancelar']); // cancelar
Route::get('/disponibilidade', [ReservaController::class,'disponibilidade']); // lugares livres

Route::get('/lista-espera', [ListaEsperaController::class,'index']);
Route::post('/lista-espera/notificar', [ListaEsperaController::class,'notificar']);

Route::get('/movimento-pontos', [PontosController::class,'index']);
Route::patch('/users/{id}/pontos', [PontosController::class,'ajustar']);

//Report
// Submeter report
Route::post('/reports', [ReportController::class,'store']);

// Listar reports pendentes (ADMIN)
Route::get('/reports/pendentes', [ReportController::class,'pendentes']);

// Validar report (ADMIN)
Route::patch('/reports/{id}/validar', [ReportController::class,'validar']);

// Rejeitar report (ADMIN)
Route::patch('/reports/{id}/rejeitar', [ReportController::class,'rejeitar']);


