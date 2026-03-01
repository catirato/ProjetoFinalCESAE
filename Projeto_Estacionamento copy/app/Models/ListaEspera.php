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
        'prioridade',
        'notification_token',
        'notificado_em',
        'expira_em',
    ];

    public $timestamps = false;

    protected $casts = [
        'notificado_em' => 'datetime',
        'expira_em' => 'datetime',
    ];

    // Relações
    public function utilizador()
    {
        return $this->belongsTo(Utilizador::class);
    }
}
