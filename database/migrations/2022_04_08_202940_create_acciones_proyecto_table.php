<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccionesProyectoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acciones_proyecto', function (Blueprint $table) {

            //TODO: Agregar el campo accion_proyecto_id (PK) y descripcion (varchar)

            $table->id('accion_id');
            $table->string('descripcion');  
            $table->text('plantilla_mensaje');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acciones_proyecto');
    }
}
