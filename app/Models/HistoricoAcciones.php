<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoAcciones extends Model
{
    use HasFactory; 

    protected $fillable = ['proyecto_id', 'accion_id', 'tiempo', 'hecha_por', 'parametros']; 

    public $timestamps = false;

    protected $primaryKey = 'historico_id';

}
