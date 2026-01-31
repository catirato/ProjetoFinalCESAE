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
        'estado'
    ];

    public function utilizador()
    {
        return $this->belongsTo(Utilizador::class);
    }
}
