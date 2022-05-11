<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUbicacionesProyectoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ubicaciones_proyecto', function (Blueprint $table) {
            $table->bigIncrements('ubicacion_id'); 
            $table->string('nom_departamento',60); 
            $table->string('nom_municipio',100);
            $table->integer('area_psa_restauracion')->nulleable(); 
            $table->integer('area_psa_preservacion')->nulleable();  
            $table->integer('num_familias_beneficiadas')->nulleable(); 
            $table->integer('num_familias_beneficiadas_campesina')->nulleable(); 
            $table->integer('num_familias_beneficiadas_indigena')->nulleable(); 
            $table->integer('num_familias_beneficiadas_afro')->nulleable(); 
            $table->integer('num_familias_beneficiadas_otras')->nulleable();
            $table->integer('num_mujeres_beneficiadas')->nulleable(); 
            $table->integer('num_hombres_beneficiados')->nulleable(); 
            $table->float('nivel_ingreso_promedio_familia')->nulleable(); 
            $table->float('costo_oportunidad')->nulleable(); 
            $table->float('valor_incentivo_psa')->nulleable(); 
            $table->unsignedBigInteger('proyecto_id');

            $table->foreign('proyecto_id')
            ->references('proyecto_id')->on('proyectos')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->timestamps();

            //Total familias: no es un campo que el usuario ingrese sino uno calculado por el sistema e igual a la sumatoria de los anteriores campos
            //Familias campesinas: número entero mayor a cero
            //Nivel promedio ingreso: se presentan las 4 opciones en el mismo orden que aparecen en el mockup, y se almacena la posición del elemento de la lista seleccionado.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ubicaciones_proyecto');
    }
}
