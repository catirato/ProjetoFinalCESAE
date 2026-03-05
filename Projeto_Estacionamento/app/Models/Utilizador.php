<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Para autenticação
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Reserva;
use App\Models\ListaEspera;
use App\Models\MovimentoPontos;
use App\Models\HistoricoEventos;
use App\Models\Report;

class Utilizador extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'utilizador';

    protected $fillable = [
        'nome',
        'email',
        'telemovel',
        'foto_perfil_path',
        'password',
        'obrigar_mudar_password',
        'role',
        'pontos'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];


    // Relações
    public function reservas()
{
    return $this->hasMany(Reserva::class);
}

public function listaEspera()
{
    return $this->hasMany(\App\Models\ListaEspera::class, 'utilizador_id');
}

public function reservasValidadas()
{
    return $this->hasMany(Reserva::class, 'validada_por');
}

public function movimentosPontos()
{
    return $this->hasMany(MovimentoPontos::class);
}

public function reports()
{
    return $this->hasMany(Report::class);
}

public function historicoEventos()
{
    return $this->hasMany(HistoricoEventos::class);
}
}
