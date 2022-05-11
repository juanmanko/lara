<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnexosProyectoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anexos_proyecto', function (Blueprint $table) {
            $table->unsignedBigInteger('proyecto_id');
            $table->string('tipo',255);
            $table->string('descripcion',255);
            $table->string('ubicacion');
            $table->dateTime('fecha_creacion');
            $table->timestamps();

            $table->foreign('proyecto_id')
            ->references('proyecto_id')->on('proyectos')
            ->onUpdate('cascade')
            ->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anexos_proyecto');
    }
}
