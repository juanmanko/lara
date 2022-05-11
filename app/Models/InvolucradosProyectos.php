<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvolucradosProyectos extends Model
{
    use HasFactory;

    protected $fillable = ['rol_involucrado', 'nombre', 'tipo', 'proyecto_id']; 

    public $timestamps = false;
 
    protected $primaryKey = 'involucrado_id';




}
