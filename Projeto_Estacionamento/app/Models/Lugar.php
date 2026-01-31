<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lugar extends Model
{
    use HasFactory;

    protected $table = 'lugar';

    protected $fillable = [
        'numero',
        'ativo'
    ];

    // Relações
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
}
