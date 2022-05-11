<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccionesPsaProyectos extends Model
{
    use HasFactory; 

    public $timestamps = false;
 
    protected $primaryKey = ['tipo','proyecto_id']; 

    public $incrementing = false;


}
