<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divipola extends Model
{
    use HasFactory; 

    public $timestamps = false;

    protected $primaryKey = 'codigo';

    public $incrementing = false;

    protected $table = "divipola";

    public function scopeTipo($query,$tipo)
    {
        if($tipo){
            return $query->where('sub_tipo',$tipo);
        }
        
    }

    public function scopeSuperior($query,$superior)
    {
        if($superior){
            return $query->where('codigo_parent',$superior); 
        }
         
    }

    /*
    public function scopeNombre($query,$nombre)
    {
        if($nombre){
            return $query->where('nombre',$nombre);   
        }
        
    }*/

    public function scopeTexto($query,$texto)
    {
        if($texto){
            return $query->where('nombre','LIKE',"%$texto%");  
        }
        
    }



}
