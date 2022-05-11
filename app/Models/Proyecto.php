<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    protected $fillable = [
 

         'nom_proyecto_psa',
         'anno_implementacion',
         'fecha_registro',
         'etapa_proyecto_psa',
         'tipo_proyecto_psa',
         'modalidad_proyecto_psa',
         'gasto_administrativo_psa',
         'gasto_monitoreo_psa',
         'valor_total_psa',
         'beneficiado_directo_psa',
         'area_predio_ecosistema_psa',
         'metodo_estimacion_valor_incentivo',
         'otro_metodo_estimacion',
         'metodo_pago_psa',
         'periodicidad_pago_psa',
         'otra_periodicidad_psa',
         'termino_duracion_acuerdo',
         'tipos_acuerdo_psa',
         'num_acuerdo_celebrado',
         'fuente_shapes'
         
        
    ];

    protected $primaryKey = "proyecto_id";
    //protected $incrementing = true;        

    public $timestamps = false;




   
    
}
