<?php

namespace App\Http\Controllers;

use App\Enums\Proyectos\Etapa;
use App\Enums\Proyectos\Tipo;
use App\Enums\Proyectos\Modalidad;
use App\Enums\Proyectos\Metodo;
use App\Enums\Proyectos\Pago;
use App\Enums\Proyectos\Periodicidad;
use App\Enums\Proyectos\Acuerdo;
use App\Enums\Proyectos\Shape;
use App\Enums\Proyectos\Accion;
use App\Enums\Entidades\Entidad;

use Illuminate\Http\Request;
use App\Models\Proyecto;
use App\Models\HistoricoEstados;
use App\Models\HistoricoAcciones;
use App\Models\AutoridadesProyectos;
use App\Models\UbicacionesProyecto;
use App\Models\Municipio;
use App\Models\InvolucradosProyectos;

use Illuminate\Support\Facades\DB; 

class ProyectoController extends Controller
{
    //Ver listado Proyectos
    //http://127.0.0.1:8000/api/proyectos?page=1&user_id=1
    public function index(){

        if(isset($_GET['user_id'])){
            
            $usuario_logueado_id = $_GET['user_id'];

            $fechaMax = DB::table('historico_estados')
                ->select(DB::raw('max(tiempo_inicio)','proyecto_id'))
                ->where('estado_despues', '<>', 'Eliminado')
                ->groupBy('proyecto_id')
                ->get();
   
            $array = json_decode(json_encode($fechaMax), true);
            
            return $projects = DB::table('historico_estados')
            ->select('proyectos.proyecto_id',  'nom_proyecto_psa', 'fecha_registro', 'tiempo_inicio','estado_despues AS estado_proyecto')
            ->join('proyectos','proyectos.proyecto_id','historico_estados.proyecto_id') 
            ->where('historico_estados.autor','=',$usuario_logueado_id)
            ->whereIn('tiempo_inicio', $array)
            ->paginate(10);
            
        } 

        //TODO: Tranlate to Elocuent

        /* Full Query Builder
        SELECT tiempo_inicio,proyectos.proyecto_id,nom_proyecto_psa,fecha_registro,IF(estado = 'Abierto','Tiene comentarios abiertos','No tiene comentarios abiertos'), estado_despues AS 'estado_proyecto' FROM `comentarios` LEFT JOIN proyectos ON proyectos.proyecto_id = comentarios.proyecto_id INNER JOIN historico_estados ON historico_estados.proyecto_id = proyectos.proyecto_id WHERE tiempo_inicio IN (SELECT MAX(tiempo_inicio) FROM `historico_estados` WHERE `estado_despues` <> 'Eliminado' GROUP BY historico_estados.proyecto_id) GROUP BY comentarios.proyecto_id
        */

        /* Ultimate Query Builder Example
            (SELECT `estado_despues` AS 'estado_proyecto', proyectos.proyecto_id,`tiempo_inicio`, nom_proyecto_psa,fecha_registro 
            FROM historico_estados INNER JOIN proyectos ON proyectos.proyecto_id = historico_estados.proyecto_id WHERE tiempo_inicio 
            IN (SELECT MAX(tiempo_inicio) FROM `historico_estados` WHERE `estado_despues` <> 'Eliminado' GROUP BY historico_estados.proyecto_id))
        */
        }

    //Ver Detalles Proyecto
    //http://127.0.0.1:8000/api/proyectos/1
    public function show($proyecto_id){
        return Proyecto::where('proyecto_id',$proyecto_id)->get();
    }
  

    //Extraer datos necesarios para crear proyecto 
    //http://127.0.0.1:8000/api/proyectos/registrar/nuevo-proyecto
    public function create(){

        //Cargar la lista de autoridades ambientales disponibles
        $autoridades_ambientales = DB::table('autoridades_ambientales')->select('autoridad_id','nombre','cod_autoridad_ambiental')->paginate(10);
        //Aquí el proyecto no existe; debido a esto no existen involucrados
        //$involucrados_proyecto = DB::table('involucrados_proyectos')->select('*')->get();
        $etapas_proyecto         = Etapa::getEnumOptions();
        $tipos_proyecto          = Tipo::getEnumOptions();
        $modalidades_proyecto    = Modalidad::getEnumOptions();
        $metodos_estimacion      = Metodo::getEnumOptions();
        $metodos_pago            = Pago::getEnumOptions();
        $periodicidades_pago     = Periodicidad::getEnumOptions();
        $tipos_acuerdo           = Acuerdo::getEnumOptions();
        $shapes                  = Shape::getEnumOptions();
        $acciones_reconocimiento = Accion::getEnumOptions();
        $tipos_entidad           = Entidad::getEnumOptions();

        return response()->json([
            'etapas_proyecto '          => $etapas_proyecto ,
            'tipos_proyecto '           => $tipos_proyecto ,
            'modalidades_proyecto '     => $modalidades_proyecto ,
            'metodos_estimacion '       => $metodos_estimacion ,
            'metodos_pago '             => $metodos_pago ,
            'periodicidades_pago '      => $periodicidades_pago,
            'tipos_acuerdo'             => $tipos_acuerdo ,
            'acciones_reconocimiento'   => $acciones_reconocimiento,
            'tipos_entidad'             => $tipos_entidad,
            'autoridades_ambientales'   => $autoridades_ambientales
            //El proyecto aún no existe, por lo tanta no hay involucrados
            //'involucrados_proyecto'     => $involucrados_proyecto

        ]);
    }

    //Municipios por departamento
    //http://localhost:8000/api/proyectos/agregar/ubicaciones/2
    //Asumiendo que se cuenta aon un listado de departamentos
    public function seleccionarMunicipios($departamento_id){
        $municipios_x_departamento = Municipio::select('*')->where('departamento_id',$departamento_id)->get();
        return $municipios_x_departamento;
    }



    //
    public function guardarUbicaciones(Request $request){

        $request->validate([
            'nom_municipio' => 'required|string|max:100',

        ]);
        
        $ubicacion = new UbicacionesProyecto();
        $ubicacion->nom_departamento = $request->input('nom_departamento');
        $ubicacion->nom_municipio = $request->input('nom_municipio');
        $ubicacion->area_psa_restauracion = $request->input('area_psa_restauracion');
        $ubicacion->area_psa_preservacion = $request->input('area_psa_preservacion');
        $ubicacion->num_familias_beneficiadas = $request->input('num_familias_beneficiadas');
        $ubicacion->costo_oportunidad = $request->input('costo_oportunidad');
        $ubicacion->valor_incentivo_psa = $request->input('valor_incentivo_psa');
        $ubicacion->save();

    }

        
    //Ruta para crear proyecto | POST Request
    //http://127.0.0.1:8000/api/proyectos/
    public function store(Request $request){

        $request->validate([
            //Campos requeridos para la inserción del proyecto
            'nom_proyecto_psa'          => 'required|string|max:128',
            'etapa_proyecto_psa'        => 'required|string|max:400',
            'tipo_proyecto_psa'         => 'required|string',
            'fecha_registro'            => 'required|date',  //Debe ser unica
            'anno_implementacion'       => 'required|integer',
            'autor_id'                  => 'required|integer',
            //'autoridades_ambientales.*' => 'required|array',
            //'involucrados_proyecto.*'   => 'array',
            
        ]);

        
        $proyectoBuscado = Proyecto::select('proyecto_id')
        ->where('autor_id',$request->input('autor_id'))
        ->where('fecha_registro', $request->input('fecha_registro'))->get();
        
        if($proyectoBuscado->count() == 0){
            //Crear el proyecto
            $proyecto = new Proyecto();

            if(!empty($request->input('nom_proyecto_psa'))){
                $proyecto->nom_proyecto_psa = $request->input('nom_proyecto_psa');
            }

            if(!empty($request->input('anno_implementacion'))){
                $proyecto->anno_implementacion = $request->input('anno_implementacion');
            }

            if(!empty($request->input('fecha_registro'))){
                $proyecto->fecha_registro = $request->input('fecha_registro');
            }

            if(!empty($request->input('etapa_proyecto_psa'))){
                $proyecto->etapa_proyecto_psa = $request->input('etapa_proyecto_psa');
            }

            if(!empty($request->input('tipo_proyecto_psa'))){
                $proyecto->tipo_proyecto_psa = $request->input('tipo_proyecto_psa');
            }

            if(!empty($request->input('autor_id'))){
                $proyecto->autor_id = $request->input('autor_id');
            }

            /* Addons */
            if(!empty($request->input('modalidad_proyecto_psa'))){
                $proyecto->modalidad_proyecto_psa = $request->input('modalidad_proyecto_psa');
            }
            if(!empty($request->input('gasto_administrativo_psa'))){
                $proyecto->gasto_administrativo_psa = $request->input('gasto_administrativo_psa');
            }
            if(!empty($request->input('gasto_monitoreo_psa'))){
                $proyecto->gasto_monitoreo_psa = $request->input('gasto_monitoreo_psa');
            }
            if(!empty($request->input('valor_total_psa'))){
                $proyecto->valor_total_psa = $request->input('valor_total_psa');
            }
            if(!empty($request->input('beneficiado_directo_psa'))){
                $proyecto->beneficiado_directo_psa = $request->input('beneficiado_directo_psa');
            }
            if(!empty($request->input('area_predio_ecosistema_psa'))){
                $proyecto->area_predio_ecosistema_psa = $request->input('area_predio_ecosistema_psa');
            }
            if(!empty($request->input('metodo_estimacion_valor_incentivo'))){
                $proyecto->metodo_estimacion_valor_incentivo = $request->input('metodo_estimacion_valor_incentivo');
            }
            if(!empty($request->input('otro_metodo_estimacion'))){
                $proyecto->otro_metodo_estimacion = $request->input('otro_metodo_estimacion');
            }
            if(!empty($request->input('metodo_pago_psa'))){
                $proyecto->metodo_pago_psa = $request->input('metodo_pago_psa');
            }
            if(!empty($request->input('periodicidad_pago_psa'))){
                $proyecto->periodicidad_pago_psa = $request->input('periodicidad_pago_psa');
            }
            if(!empty($request->input('otra_periodicidad_psa'))){
                $proyecto->otra_periodicidad_psa = $request->input('otra_periodicidad_psa');
            }
            if(!empty($request->input('termino_duracion_acuerdo'))){
                $proyecto->termino_duracion_acuerdo = $request->input('termino_duracion_acuerdo');
            }
            if(!empty($request->input('tipos_acuerdo_psa'))){
                $proyecto->tipos_acuerdo_psa = $request->input('tipos_acuerdo_psa');
            }
            if(!empty($request->input('num_acuerdo_celebrado'))){
                $proyecto->num_acuerdo_celebrado = $request->input('num_acuerdo_celebrado');
            }
            if(!empty($request->input('fuente_shapes'))){
                $proyecto->fuente_shapes = $request->input('fuente_shapes');
            }
            
            $proyecto->save();

            //Se refresta el modelo a través de Elocuent

            //TODO: Agregar autoridades ambientales 

                 
            //Autoridades ambientales asociadas al proyecto | enviar listado de autoridades asociadas al proyecto
            if(!empty($request->input('autoridades_ambientales'))){

                $autoridades_ambientales = json_decode($request->input('autoridades_ambientales'), true);
                
                //return $autoridades_ambientales;

                foreach($autoridades_ambientales as $autoridad_ambiental){
                    //Asociar la autoridad con el proyecto
                    $autoridades_proyecto = new AutoridadesProyectos();
                    $autoridades_proyecto->proyecto_id = $proyecto->proyecto_id;
                    $autoridades_proyecto->autoridad_id = $autoridad_ambiental["autoridad_id"];
                    $autoridades_proyecto->save();
                }
            }
           
            //TODO: Agregar incolucrados proyecto
            if(!empty($request->input('involucrados_proyecto')))
            {       
                    $involucrados = json_decode($request->input('involucrados_proyecto'), true);
                    foreach($involucrados as $involucrado){
                        $involucrados_proyectos = new InvolucradosProyectos();
                        $involucrados_proyectos->rol_involucrado = $involucrado["rol_involucrado"];
                        $involucrados_proyectos->nombre = $involucrado["nombre"];
                        $involucrados_proyectos->tipo = $involucrado["tipo"];
                        $involucrados_proyectos->proyecto_id = $proyecto->proyecto_id;
                        $involucrados_proyectos->save();
                    }
            }
            
            $estado_inicial = new HistoricoEstados();
            $estado_inicial->historico_id = NULL;
            $estado_inicial->tiempo_inicio = date('Y-m-d:H-m-s');
            $estado_inicial->estado_antes = 'Borrador';
            $estado_inicial->estado_despues = 'Borrador';
            $estado_inicial->comentario = 'Se agregaron los datos básicos';
            $estado_inicial->proyecto_id = $proyecto->proyecto_id;
            $estado_inicial->autor = $proyecto->autor_id;
            $estado_inicial->save();

            //Debe existir previamente una acción en la tabla acciones proyecto
            $nueva_accion = new HistoricoAcciones();
            $nueva_accion->proyecto_id = $proyecto->proyecto_id;
            $nueva_accion->accion_id = 1;
            $nueva_accion->fecha = date('Y-m-d:H-m-s');
            $nueva_accion->hecha_por = $proyecto->autor_id;
            $nueva_accion->parametros = 'Parametros';
            $nueva_accion->save();

        }
        else{

         
        $proyecto = Proyecto::select('*')->where('proyecto_id',$proyectoBuscado[0]["proyecto_id"])->first();
        //fecha_registro

                if(!empty($request->input('nom_proyecto_psa'))){
                    $proyecto->nom_proyecto_psa = $request->input('nom_proyecto_psa');
                }

                if(!empty($request->input('etapa_proyecto_psa'))){
                    $proyecto->etapa_proyecto_psa = $request->input('etapa_proyecto_psa');
                }

                if(!empty($request->input('tipo_proyecto_psa'))){
                    $proyecto->tipo_proyecto_psa = $request->input('tipo_proyecto_psa');
                }
                if(!empty($request->input('modalidad_proyecto_psa'))){
                    $proyecto->modalidad_proyecto_psa = $request->input('modalidad_proyecto_psa');
                }
                if(!empty($request->input('gasto_administrativo_psa'))){
                    $proyecto->gasto_administrativo_psa = $request->input('gasto_administrativo_psa');
                }
                if(!empty($request->input('gasto_monitoreo_psa'))){
                    $proyecto->gasto_monitoreo_psa = $request->input('gasto_monitoreo_psa');
                }
                if(!empty($request->input('valor_total_psa'))){
                    $proyecto->valor_total_psa = $request->input('valor_total_psa');
                }
                if(!empty($request->input('beneficiado_directo_psa'))){
                    $proyecto->beneficiado_directo_psa = $request->input('beneficiado_directo_psa');
                }
                
                if(!empty($request->input('area_predio_ecosistema_psa'))){
                    $proyecto->area_predio_ecosistema_psa = $request->input('area_predio_ecosistema_psa');
                }
                if(!empty($request->input('metodo_estimacion_valor_incentivo'))){
                    $proyecto->metodo_estimacion_valor_incentivo = $request->input('metodo_estimacion_valor_incentivo');
                }
                if(!empty($request->input('otro_metodo_estimacion'))){
                    $proyecto->otro_metodo_estimacion = $request->input('otro_metodo_estimacion');
                }
                if(!empty($request->input('metodo_pago_psa'))){
                    $proyecto->metodo_pago_psa = $request->input('metodo_pago_psa');
                }
                if(!empty($request->input('periodicidad_pago_psa'))){
                    $proyecto->periodicidad_pago_psa = $request->input('periodicidad_pago_psa');
                }
                if(!empty($request->input('otra_periodicidad_psa'))){
                    $proyecto->otra_periodicidad_psa = $request->input('otra_periodicidad_psa');
                }
                if(!empty($request->input('termino_duracion_acuerdo'))){
                    $proyecto->termino_duracion_acuerdo = $request->input('termino_duracion_acuerdo');
                }
                if(!empty($request->input('tipos_acuerdo_psa'))){
                    $proyecto->tipos_acuerdo_psa = $request->input('tipos_acuerdo_psa');
                }
                if(!empty($request->input('num_acuerdo_celebrado'))){
                    $proyecto->num_acuerdo_celebrado = $request->input('num_acuerdo_celebrado');
                }
                if(!empty($request->input('fuente_shapes'))){
                    $proyecto->fuente_shapes = $request->input('fuente_shapes');
                }

                $proyecto->update();
        }
        
        //Datos del Proyecto
        $proyecto = Proyecto::where('proyecto_id',$proyecto->proyecto_id)->get();

        //Autoridades Ambientales - Enums
        $autoridades_ambientales = DB::table('autoridades_ambientales')->select('autoridad_id','nombre','cod_autoridad_ambiental')->get();
        $involucrados_proyecto = DB::table('involucrados_proyectos')->select('*')->get();
        $etapas_proyecto         = Etapa::getEnumOptions();
        $tipos_proyecto          = Tipo::getEnumOptions();
        $modalidades_proyecto    = Modalidad::getEnumOptions();
        $metodos_estimacion      = Metodo::getEnumOptions();
        $metodos_pago            = Pago::getEnumOptions();
        $periodicidades_pago     = Periodicidad::getEnumOptions();
        $tipos_acuerdo           = Acuerdo::getEnumOptions();
        $shapes                  = Shape::getEnumOptions();
        $acciones_reconocimiento = Accion::getEnumOptions();
        $tipos_entidad           = Entidad::getEnumOptions();

        return response()->json([
            'etapas_proyecto '          => $etapas_proyecto ,
            'tipos_proyecto '           => $tipos_proyecto ,
            'modalidades_proyecto '     => $modalidades_proyecto ,
            'metodos_estimacion '       => $metodos_estimacion ,
            'metodos_pago '             => $metodos_pago ,
            'periodicidades_pago '      => $periodicidades_pago,
            'tipos_acuerdo'             => $tipos_acuerdo ,
            'acciones_reconocimiento'   => $acciones_reconocimiento,
            'tipos_entidad'             => $tipos_entidad,
            'autoridades_ambientales'   => $autoridades_ambientales,
            'proyecto'                  => $proyecto,
            'involucrados_proyecto'     => $involucrados_proyecto
        ]);

    }

    //Actualizar Proyecto 
    //http://127.0.0.1:8000/api/proyectos/1 | PUT
    public function update(Request $request,$id){
        $proyecto = Proyecto::find($id);
        $proyecto->update($request->all());
        return $proyecto;
    }

    //Eliminar proyecto
    //http://127.0.0.1:8000/api/proyectos/51 | DELETE
    public function destroy($id){
        $proyecto = Proyecto::find($id);
        $proyecto->delete();
        return 204;
    }
}
