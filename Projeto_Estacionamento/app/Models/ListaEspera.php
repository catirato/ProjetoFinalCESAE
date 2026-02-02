<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListaEspera extends Model
{
    use HasFactory;

    protected $table = 'lista_espera';

    protected $fillable = [
        'utilizador_id',
        'data',
        'estado',
        'prioridade'
    ];

    // Relações
    public function utilizador()
    {
        return $this->belongsTo(Utilizador::class);
    }
}
