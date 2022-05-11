<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoridadesProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('autoridades_proyectos', function (Blueprint $table) {
            $table->unsignedBigInteger('proyecto_id');
            $table->unsignedBigInteger('autoridad_id');
            $table->primary(['proyecto_id', 'autoridad_id']);

            $table->foreign('proyecto_id')
            ->references('proyecto_id')->on('proyectos')
            ->onUpdate('cascade')
            ->onDelete('restrict');
            
            $table->foreign('autoridad_id')
            ->references('autoridad_id')->on('autoridades_ambientales')
            ->onUpdate('cascade')
            ->onDelete('restrict');
            $table->timestamps();

            ////Llave primaria
            //$table->primary(['id', 'id_departamento']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('autoridades_proyectos');
    }
}
