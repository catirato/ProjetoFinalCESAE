<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; // Para autenticação
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Utilizador extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'utilizador';

    protected $fillable = [
        'nome',
        'email',
        'password_hash',
        'obrigar_mudar_password',
        'role',
        'pontos'
    ];

    protected $hidden = [
        'password_hash',
    ];

    // Relações
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
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
