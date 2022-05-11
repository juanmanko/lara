<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricoAccionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historico_acciones', function (Blueprint $table) {
            $table->unsignedBigInteger('proyecto_id');
            $table->unsignedBigInteger('accion_id');
            $table->dateTime('fecha');
            $table->unsignedBigInteger('hecha_por');
            $table->text('parametros');

            $table->foreign('proyecto_id')
            ->references('proyecto_id')->on('proyectos')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('hecha_por')
            ->references('usuario_id')->on('usuarios')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('accion_id')
            ->references('accion_id')->on('acciones_proyecto')
            ->onUpdate('cascade')
            ->onDelete('restrict');

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
        Schema::dropIfExists('historico_acciones');
    }
}
