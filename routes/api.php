<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/ 

Route::middleware('auth:api')->get('/user', function (Request $request) { 
    return $request->user();
});

//Listar Proyectos pertenecientes a el usuario logueado

//http://127.0.0.1:8000/api/proyectos?user_id=1
Route::get('proyectos/',[ProyectoController::class, 'index']); 

//Ver detalles de un proyecto perteneciente al usuario logueado
//http://127.0.0.1:8000/api/proyectos/2
Route::get('proyectos/{id}',[ProyectoController::class, 'show']);

//Extratar datos necesarios para crear proyecto

Route::get('proyectos/registrar/datos-basicos/{autoridades?}',[ProyectoController::class, 'create']); 

//Almacenar datos de un proyecto
Route::post('proyectos/',[ProyectoController::class, 'store']);

//Actualizar Proyecto
Route::put('proyectos/{id}',[ProyectoController::class, 'update']);

//Eliminar Proyecto
Route::delete('proyectos/{id}',[ProyectoController::class, 'delete']); 


//http://localhost:8000/api/proyectos/ubicaciones/1
Route::get('proyectos/ubicaciones/{departamento_id}',[ProyectoController::class, 'seleccionarMunicipios']);
//Cargar datos para crear las ubicaciones
Route::post('proyectos/registrar/ubicaciones', [ProyectoController::class, 'guardarUbicaciones']);

Route::get('listar-autoridades-ambientales',[ProyectoController::class, 'listarAutoridadesAmbientales']); 

Route::get('divipola',[ProyectoController::class, 'listarNivelesDivipola']); 















