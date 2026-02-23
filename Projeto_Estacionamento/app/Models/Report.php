<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'report';

    protected $fillable = [
        'utilizador_id',
        'tipo',
        'descricao',
        'estado',
        'ajuste_pontos_necessario',
        'ajuste_pontos_concluido',
    ];

    protected $casts = [
        'ajuste_pontos_necessario' => 'boolean',
        'ajuste_pontos_concluido' => 'boolean',
    ];

    public function utilizador()
    {
        return $this->belongsTo(Utilizador::class);
    }
}
