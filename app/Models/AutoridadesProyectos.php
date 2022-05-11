<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoridadesProyectos extends Model
{
    use HasFactory;

    protected $primaryKey = ['proyecto_id', 'autoridad_id']; 

    public $incrementing = false;

    protected $fillable = ['proyecto_id', 'autoridad_id']; 

    public $timestamps = false;
}
