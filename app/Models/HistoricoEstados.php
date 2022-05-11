<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoEstados extends Model
{
    use HasFactory;

    protected $fillable = ['tiempo_inicio', 'estado_antes', 'estado_despues', 'comentario', 'proyecto_id', 'autor']; 

    public $timestamps = false;

    protected $primaryKey = 'historico_id'; 
}
