<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimentoPontos extends Model
{
    use HasFactory;

    protected $table = 'movimento_pontos';

    protected $fillable = [
        'utilizador_id',
        'reserva_id',
        'tipo',
        'pontos'
    ];

    // Relações
    public function utilizador()
    {
        return $this->belongsTo(Utilizador::class);
    }

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }
}
