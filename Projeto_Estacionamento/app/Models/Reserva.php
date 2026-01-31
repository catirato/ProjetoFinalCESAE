<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reserva';

    protected $fillable = [
        'utilizador_id',
        'lugar_id',
        'data',
        'estado',
        'validada_por'
    ];

    // Relações
    public function utilizador()
    {
        return $this->belongsTo(Utilizador::class);
    }

    public function lugar()
    {
        return $this->belongsTo(Lugar::class);
    }

    public function validadaPor()
    {
        return $this->belongsTo(Utilizador::class, 'validada_por');
    }
}
