<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyecto;
use App\Models\HistoricoEstados;
use App\Models\HistoricoAcciones;
use App\Models\AutoridadesProyectos;
use App\Models\UbicacionesProyecto;
use App\Models\Municipio;
use App\Models\InvolucradosProyectos;
use App\Models\AccionesPsaProyectos;
use App\Models\Divipola;

use Illuminate\Support\Facades\DB; 

class ProyectoController extends Controller
{
    //Ver listado Proyectos
    //http://127.0.0.1:8000/api/proyectos?user_id=1 
    
    public function index(){
    
        if(isset($_GET['user_id'])){
            
            $usuario_logueado_id = $_GET['user_id'];

            //TODO: Tranlate to Elocuent
            //Change Date Format

            return $proyectos = DB::select("
            SELECT estado_despues AS item_enum_estado_proyecto, traduccion AS traduccion_enum_estado_proyecto ,
            nom_proyecto_psa,proyectos.proyecto_id, fecha_registro FROM proyectos 
            INNER JOIN historico_estados ON proyectos.proyecto_id = historico_estados.proyecto_id
            INNER JOIN traducciones_enum ON historico_estados.estado_despues::text = traducciones_enum.item
            WHERE tiempo_inicio 
            IN (SELECT MAX(tiempo_inicio) FROM historico_estados WHERE estado_despues <> 'eliminado' 
            GROUP BY historico_estados.proyecto_id) AND proyectos.autor_id = '".$usuario_logueado_id."'");
        }
    }


    //Extraer datos necesarios para crear proyecto 
    //http://127.0.0.1:8000/api/proyectos/registrar/datos-basicos
    //http://127.0.0.1:8000/api/proyectos/registrar/datos-basicos?autoridades=listar
    public function create(){
        
        $etapas_proyecto = DB::table('traducciones_enum')->select('item','traduccion')->where('enum','etapas_proyecto')->get();
        $tipos_proyecto = DB::table('traducciones_enum')->select('item','traduccion')->where('enum','tipos_proyecto')->get();
        $modalidades_proyecto = DB::table('traducciones_enum')->select('item','traduccion')->where('enum','modalidades_proyecto')->get();
        $tipos_entidad = DB::table('traducciones_enum')->select('item','traduccion')->where('enum','tipos_entidad')->get();
        $acciones_reconocimiento = DB::table('traducciones_enum')->select('item','traduccion')->where('enum','acciones_reconocimiento')->get();
     
        $autoridades_ambientales = [];

        if(isset($_GET['autoridades'])){

            if(!empty($_GET['autoridades']) && $_GET['autoridades'] == 'listar'){
                //Cargar listado de autoridades ambientales
                $autoridades_ambientales = DB::table('autoridades_ambientales')->
                select('nombre','cod_autoridad_ambiental')->get();
            }
        }
      
        return response()->json([
            'etapas_proyecto '          => $etapas_proyecto ,
            'tipos_proyecto '           => $tipos_proyecto ,
            'modalidades_proyecto '     => $modalidades_proyecto ,
            'tipos_entidad'             => $tipos_entidad,
            'acciones_reconocimiento'   => $acciones_reconocimiento,
            'autoridades_ambientales'   => $autoridades_ambientales
        ]);
    }

    public function listarAutoridadesAmbientales(){
        //Listado de autoridades ambientales
        $autoridades_ambientales = DB::table('autoridades_ambientales')->
        select('nombre','cod_autoridad_ambiental')->get();

        return response()->json([
            'autoridades_ambientales'   => $autoridades_ambientales
        ]);
    }
    
    //Ruta para insertar registros en la base de datos | POST Request
    //http://127.0.0.1:8000/api/proyectos/
    /*  Antes de correr este proceso debe asegurarse de que las siguientes tablas tengan información:
        1 La tabla Usuarios 
        2 La tabla Autoridades Ambientales
        3 La tabla Acciones Proyecto
        4 La fecha de registro debe ser única
        5 El año de implementación se espera al igual que la fecha
    */

    public function store(Request $request){

        // Año de implementación es el año en progreso.
        $request->validate([
            //Campos requeridos para la el registro del proyecto
            'nom_proyecto_psa'          => 'required|string|max:128',
            'etapa_proyecto_psa'        => 'required|string|max:400',
            'tipo_proyecto_psa'         => 'required|string',
            'autoridades_ambientales'   => 'required',
            //Este campo se espera por defecto.
            'autor_id'                  => 'required|integer'
        ]);

        //$fecha_registro = date('Y-m-d:H:m:s');
        $fecha_registro = $request->input('fecha_registro');
        
        $proyectoBuscado = Proyecto::select('proyecto_id')
        ->where('autor_id',$request->input('autor_id'))
        ->where('fecha_registro', $fecha_registro)->get();
        
        //Comprobar si el proyecto ya existe
        if($proyectoBuscado->count() == 0){
            //Crear un nuevo proyecto
            $proyecto = new Proyecto();

            //Generar la fecha de registro
            $proyecto->fecha_registro = $fecha_registro;

            if(!empty($request->input('nom_proyecto_psa'))){
                $proyecto->nom_proyecto_psa = $request->input('nom_proyecto_psa');
            }

            //Este año no debería actualizarse, para no perder trazabilidad
            if(!empty($request->input('anno_implementacion'))){
                $proyecto->anno_implementacion = $request->input('anno_implementacion');
            }

            if(!empty($request->input('tipo_proyecto_psa'))){
                $proyecto->tipo_proyecto_psa = $request->input('tipo_proyecto_psa');
            }

            if(!empty($request->input('autor_id'))){
                $proyecto->autor_id = $request->input('autor_id');
            }

            //Datos adicionales
            //Si la etapa del proyecto es igual a "Inversión", se debe solicitar los siguientes datos:
            if(!empty($request->input('etapa_proyecto_psa'))){
                $proyecto->etapa_proyecto_psa = $request->input('etapa_proyecto_psa');
                if($request->input('etapa_proyecto_psa') == 'inversion'){
                    if(!empty($request->input('modalidad_proyecto_psa'))){
                        $proyecto->modalidad_proyecto_psa = $request->input('modalidad_proyecto_psa');
                        
                    }
                }
            }

            
            $proyecto->save();
           
            //Se refresta el modelo a través de Elocuent

            if(!empty( $proyecto->etapa_proyecto_psa )){
                
                if( $proyecto->etapa_proyecto_psa == 'inversion'){
                    
                    //Es un array para insertar datos en la tabla acciones_psa_proyecto 
                    if(!empty($request->input('acciones_reconocimiento'))){
                        $acciones = json_decode($request->input('acciones_reconocimiento'), true);
                        
                        //TODO: Validar la clave primaria compuesta, borra todo y volver a crear
                        for( $i = 0; $i < count($acciones); $i ++ )
                        {
                            $acciones_psa_proyecto = new AccionesPsaProyectos();
                            $acciones_psa_proyecto->tipo = $acciones[$i]["item"];
                            $acciones_psa_proyecto->proyecto_id = $proyecto->proyecto_id;
                            $acciones_psa_proyecto->save();
                        }
                    }

                    //Insertar la entidad implementadora
                    
                    //Tipo entidad implementadora | Enum = tipos_entidad
                    //TODO: Validar si viene un array
                    
                    if(!empty($request->input('involucrados_proyecto')))
                    {       
                        $involucrados = json_decode($request->input('involucrados_proyecto'), true);
                        
                        //Se espera únicamente un registro
                        $involucrados_proyectos = new InvolucradosProyectos();
                        $involucrados_proyectos->rol_involucrado = 'implementador';
                        $involucrados_proyectos->nombre = $involucrados[0]["nombre"];
                        $involucrados_proyectos->tipo = $involucrados[0]["tipo"];
                        $involucrados_proyectos->proyecto_id = $proyecto->proyecto_id;
                        $involucrados_proyectos->save();
                    }
                }
                else
                {
                    //Etapa Pre-Inversión
                    //Se espera un array de entidades formuladoras 
                    if(!empty($request->input('involucrados_proyecto')))
                    {       
                            $involucrados = json_decode($request->input('involucrados_proyecto'), true);
                            
                            foreach($involucrados as $involucrado){
                                $involucrados_proyectos = new InvolucradosProyectos();
                                $involucrados_proyectos->rol_involucrado = 'formulador';
                                $involucrados_proyectos->nombre = $involucrado["nombre"];
                                $involucrados_proyectos->tipo = $involucrado["tipo"];
                                $involucrados_proyectos->proyecto_id = $proyecto->proyecto_id;
                                $involucrados_proyectos->save();
                            }
                    }
                    
                }
            

            }

        
            //TODO: Validar que los dos campos sean clave primaria compuesta
            //Se espera recibir un listado de autoridades ambientales
            if(!empty($request->input('autoridades_ambientales'))){
                $autoridades_ambientales = json_decode($request->input('autoridades_ambientales'), true);
                 
                foreach($autoridades_ambientales as $autoridad_ambiental){
                    //Asociar la autoridad con el proyecto
                    $autoridades_proyecto = new AutoridadesProyectos();
                    $autoridades_proyecto->proyecto_id = $proyecto->proyecto_id;
                    $autoridades_proyecto->autoridad_id = $autoridad_ambiental["cod_autoridad_ambiental"];
                    $autoridades_proyecto->save();
                }
            }

            //Agregar el primer estado al histórico
            $estado_inicial = new HistoricoEstados();
            $estado_inicial->tiempo_inicio = date('Y-m-d:H:m:s');
            $estado_inicial->estado_antes = NULL;
            $estado_inicial->estado_despues = 'borrador';
            $estado_inicial->comentario = 'Se agregaron los datos básicos';
            $estado_inicial->proyecto_id = $proyecto->proyecto_id;
            $estado_inicial->autor = $proyecto->autor_id;
            $estado_inicial->save();

            //Debe existir previamente una acción en la tabla acciones proyecto

            $nueva_accion = new HistoricoAcciones();
            $nueva_accion->proyecto_id = $proyecto->proyecto_id;
            $nueva_accion->accion_id = 1;
            $nueva_accion->tiempo = date('Y-m-d:H:m:s');
            $nueva_accion->hecha_por = $proyecto->autor_id;
            $nueva_accion->parametros = 'Parametros';
            $nueva_accion->save();


        //Retornar nuevamente los datos del proyecto actualizado
        //Datos Proyecto
        $proyecto = Proyecto::where('proyecto_id',$proyecto->proyecto_id)->get();
        //Listado completo de autoridades ambientales, en caso de que desea realizar actualizaciones 
        $autoridades_ambientales = DB::table('autoridades_ambientales')
        ->select('nombre','cod_autoridad_ambiental')
        ->get();
        //Listado Autoridades Ambientales Asociadas
        $autoridades_ambientales_proyecto = DB::select("select cod_autoridad_ambiental,nombre from autoridades_ambientales  
        inner join autoridades_proyectos on autoridad_id = cod_autoridad_ambiental
        where autoridades_proyectos.proyecto_id  = '".$proyecto[0]["proyecto_id"]."'");

        //Listado Involucrados Proyecto
         
        $involucrados_proyecto = DB::table('involucrados_proyectos')
        ->select('nombre')
        ->where('proyecto_id',$proyecto[0]["proyecto_id"])
        ->get();

        $etapas_proyecto = DB::table('traducciones_enum')->select('item','traduccion')->where('enum','etapas_proyecto')->get();
        $tipos_proyecto = DB::table('traducciones_enum')->select('item','traduccion')->where('enum','tipos_proyecto')->get();
        $modalidades_proyecto = DB::table('traducciones_enum')->select('item','traduccion')->where('enum','modalidades_proyecto')->get();
        $tipos_entidad = DB::table('traducciones_enum')->select('item','traduccion')->where('enum','tipos_entidad')->get();
        $acciones_reconocimiento = DB::table('traducciones_enum')->select('item','traduccion')->where('enum','acciones_reconocimiento')->get();
     
        return response()->json([
            'etapas_proyecto '                  => $etapas_proyecto,
            'tipos_proyecto '                   => $tipos_proyecto,
            'modalidades_proyecto '             => $modalidades_proyecto,
            'tipos_entidad'                     => $tipos_entidad,
            'acciones_reconocimiento'           => $acciones_reconocimiento,
            'autoridades_ambientales'           => $autoridades_ambientales,
            'proyecto'                          => $proyecto,
            'autoridades_ambientales_proyecto'  => $autoridades_ambientales_proyecto,
            'involucrados_proyecto'             => $involucrados_proyecto
        ]);


        }
        else{
        
        //Si el proyecto ya existe debe actualizarse
        $proyecto = Proyecto::select('*')->where('proyecto_id',$proyectoBuscado[0]["proyecto_id"])->first();
     
        //La fecha de registro y el año de implementación no se actualizan
        if(!empty($request->input('nom_proyecto_psa'))){
            $proyecto->nom_proyecto_psa = $request->input('nom_proyecto_psa');
        }
        //La etapa del proyecto no debería cambiar aquí
        if(!empty($request->input('etapa_proyecto_psa'))){
            $proyecto->etapa_proyecto_psa = $request->input('etapa_proyecto_psa');
        }

        if(!empty($request->input('tipo_proyecto_psa'))){
            $proyecto->tipo_proyecto_psa = $request->input('tipo_proyecto_psa');
        }

        /* Datos Adicionales */

        /*
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
        }*/
        
        $proyecto->update();
        //return "UPDATED";
    }
        //Retornar nuevamente los datos del proyecto actualizado
        //Datos Proyecto
        $proyecto = Proyecto::where('proyecto_id',$proyecto->proyecto_id)->get();
        //Listado completo de autoridades ambientales, en caso de que desea realizar actualizaciones 
        $autoridades_ambientales = DB::table('autoridades_ambientales')
        ->select('nombre','cod_autoridad_ambiental')
        ->get();
        //Listado Autoridades Ambientales Asociadas
        $autoridades_ambientales_proyecto = DB::select("select cod_autoridad_ambiental,nombre from autoridades_ambientales  
        inner join autoridades_proyectos on autoridad_id = cod_autoridad_ambiental
        where autoridades_proyectos.proyecto_id  = '".$proyecto[0]["proyecto_id"]."'");

        //Listado Involucrados Proyecto
         
        $involucrados_proyecto = DB::table('involucrados_proyectos')
        ->select('nombre')
        ->where('proyecto_id',$proyecto[0]["proyecto_id"])
        ->get();

        $etapas_proyecto = DB::table('traducciones_enum')->select('item','traduccion')->where('enum','etapas_proyecto')->get();
        $tipos_proyecto = DB::table('traducciones_enum')->select('item','traduccion')->where('enum','tipos_proyecto')->get();
        $modalidades_proyecto = DB::table('traducciones_enum')->select('item','traduccion')->where('enum','modalidades_proyecto')->get();
        $tipos_entidad = DB::table('traducciones_enum')->select('item','traduccion')->where('enum','tipos_entidad')->get();
        $acciones_reconocimiento = DB::table('traducciones_enum')->select('item','traduccion')->where('enum','acciones_reconocimiento')->get();
     
        return response()->json([
            'etapas_proyecto '                  => $etapas_proyecto,
            'tipos_proyecto '                   => $tipos_proyecto,
            'modalidades_proyecto '             => $modalidades_proyecto,
            'tipos_entidad'                     => $tipos_entidad,
            'acciones_reconocimiento'           => $acciones_reconocimiento,
            'autoridades_ambientales'           => $autoridades_ambientales,
            'proyecto'                          => $proyecto,
            'autoridades_ambientales_proyecto'  => $autoridades_ambientales_proyecto,
            'involucrados_proyecto'             => $involucrados_proyecto
        ]);

    }

    //Actualizar Proyecto 
    //http://127.0.0.1:8000/api/proyectos/1 | PUT
    public function update(Request $request,$id){
        $proyecto = Proyecto::find($id);
        $proyecto->update($request->all());
        return $proyecto;
    }

      //Ver Detalles Proyecto
    //http://127.0.0.1:8000/api/proyectos/1
    public function show($proyecto_id){
        return Proyecto::where('proyecto_id',$proyecto_id)->get();
    }

    //Eliminar proyecto
    //http://127.0.0.1:8000/api/proyectos/51 | DELETE
    public function destroy($id){
        $proyecto = Proyecto::find($id);
        $proyecto->delete();
        return 204;
    }


       //Municipios por departamento
    
    //http://localhost:8000/api/proyectos/ubicaciones/1
    //Asumiendo que se cuenta aon un listado de departamentos
    public function seleccionarMunicipios($codigo){

        //$municipios = Municipio::select('*')->where('departamento_id',$departamento_id)->get();
        $municipios = Divipola::select('*')->where('codigo',$codigo)->get();
        return $municipios;
        
    }

    //http://localhost:8000/api/proyectos/registrar/ubicaciones
    public function guardarUbicaciones(Request $request){

        $request->validate([

            'ubicaciones.*'    => 'required|array'
        ]);

        //$etapa = Proyecto::select('etapa_proyecto_psa')->where('proyecto_id',1)->get();

        if(!empty($request->input('ubicaciones'))){

            //Decodificar el request
            $ubicaciones = json_decode($request->input('ubicaciones'), true);

           if(count($ubicaciones) > 0){
               
                for( $i = 0; $i < count( $ubicaciones ); $i ++ ){
                    
                    //Crear una nueva instancia en cada iteración
                    $nueva_ubicacion = new UbicacionesProyecto();
                   
                    if(!empty( $ubicaciones[$i]["cod_departamento"] ))
                    {
                        $nueva_ubicacion->cod_departamento =  $ubicaciones[$i]["cod_departamento"];
                    }

                    if(!empty( $ubicaciones[$i]["cod_municipio"] ))
                    {
                        $nueva_ubicacion->cod_municipio =  $ubicaciones[$i]["cod_municipio"];
                    }

                    //Campos opcionales 
                    if(!empty( $ubicaciones[$i]["area_psa_preservacion"] ))
                    {
                        $nueva_ubicacion->area_psa_preservacion =  $ubicaciones[$i]["area_psa_preservacion"];
                    }

                    if(!empty( $ubicaciones[$i]["area_psa_restauracion"] ))
                    {
                        $nueva_ubicacion->area_psa_restauracion =  $ubicaciones[$i]["area_psa_restauracion"];
                    }

                    if(!empty( $ubicaciones[$i]["num_familias_beneficiadas"] ))
                    {
                        $nueva_ubicacion->num_familias_beneficiadas =  $ubicaciones[$i]["num_familias_beneficiadas"];
                    }

                    if(!empty( $ubicaciones[$i]["valor_incentivo_psa"] ))
                    {
                        $nueva_ubicacion->valor_incentivo_psa =  $ubicaciones[$i]["valor_incentivo_psa"];
                    }

                    if(!empty( $ubicaciones[$i]["costo_oportunidad"] ))
                    {
                        $nueva_ubicacion->costo_oportunidad =  $ubicaciones[$i]["costo_oportunidad"];
                    }

                    //Id del proyecto
                    if(!empty( $ubicaciones[$i]["proyecto_id"] ))
                    {
                        $nueva_ubicacion->proyecto_id =  $ubicaciones[$i]["proyecto_id"];
                    }
           
                    $nueva_ubicacion->save();         
                }

                return "CREATED";

            }  
        }
    }

    public function registrarBeneficiarios(){

        if(!empty( $beneficiados[$i]["num_familias_beneficiadas_campesina"] ))
        {
            $beneficiados_proyecto->num_familias_beneficiadas_campesina =  $beneficiados[$i]["num_familias_beneficiadas_campesina"];
        }

        if(!empty( $beneficiados[$i]["num_familias_beneficiadas_indigena"] ))
        {
            $beneficiados_proyecto->num_familias_beneficiadas_indigena =  $beneficiados[$i]["num_familias_beneficiadas_indigena"];
        }

        if(!empty( $beneficiados[$i]["num_familias_beneficiadas_afro"] ))
        {
            $beneficiados_proyecto->num_familias_beneficiadas_afro =  $beneficiados[$i]["num_familias_beneficiadas_afro"];
        }

        if(!empty( $beneficiados[$i]["num_familias_beneficiadas_otras"] ))
        {
            $beneficiados_proyecto->num_familias_beneficiadas_otras =  $beneficiados[$i]["num_familias_beneficiadas_otras"];
        }

        if(!empty( $beneficiados[$i]["num_hombres_beneficiados"] ))
        {
            $beneficiados_proyecto->num_hombres_beneficiados =  $beneficiados[$i]["num_hombres_beneficiados"];
        }

        if(!empty( $beneficiados[$i]["num_mujeres_beneficiadas"] ))
        {
            $beneficiados_proyecto->num_mujeres_beneficiadas =  $beneficiados[$i]["num_mujeres_beneficiadas"];
        }

        if(!empty( $beneficiados[$i]["nivel_ingreso_promedio_familia"] ))
        {
            //Cualquiera de las cuatro opciones presentes en el mockup y se almacena la posición del elemento seleccionado
            $beneficiados_proyecto->nivel_ingreso_promedio_familia =  $beneficiados[$i]["nivel_ingreso_promedio_familia"];
        }

        //Id del proyecto
        if(!empty( $ubicaciones[$i]["proyecto_id"] ))
        {
            $nueva_ubicacion->proyecto_id =  $ubicaciones[$i]["proyecto_id"];
        }


    }

    //Divipola
    
    public function listarNivelesDivipola(Request $request){

        $tipo = $request->get('tipo');
        $superior = $request->get('superior');
        $nombre = $request->get('nombre');
        $texto = $request->get('texto');
        
        return $divipola = Divipola::select("*")
            ->tipo($tipo)
            ->superior($superior)
            ->texto($texto)
            ->get();
        
        // api/divipola?type=departamento&parent=[number]&name=['simple'|'compuesto']&text=[string]					
    }  

}
