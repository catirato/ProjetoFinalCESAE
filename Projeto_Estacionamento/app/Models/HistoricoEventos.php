<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoEventos extends Model
{
    use HasFactory;

    protected $table = 'historico_eventos';

    protected $fillable = [
        'utilizador_id',
        'tipo_evento',
        'entidade_id',
        'acao',
        'descricao',
        'created_at'
    ];

    public function utilizador()
    {
        return $this->belongsTo(Utilizador::class);
    }
}
