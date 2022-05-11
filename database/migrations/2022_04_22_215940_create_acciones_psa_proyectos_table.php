<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccionesPsaProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acciones_psa_proyectos', function (Blueprint $table) {
            $table->enum('tipo',\App\Enums\Proyectos\Accion::getEnumOptions())->nullable(); //PK
            $table->unsignedBigInteger('proyecto_id'); //PK //FK

            $table->foreign('proyecto_id')
            ->references('proyecto_id')->on('proyectos')
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
        Schema::dropIfExists('acciones_psa_proyectos');
    }
}
