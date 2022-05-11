<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbicacionesProyecto extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'ubicacion_id';

    protected $fillable = ['proyecto_id']; 

}
