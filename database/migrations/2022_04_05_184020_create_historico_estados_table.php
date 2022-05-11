<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricoEstadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() 
    {
        Schema::create('historico_estados', function (Blueprint $table) {
            $table->bigIncrements('historico_id');
            $table->dateTime('tiempo_inicio')->unique(); // Unique?
            $table->enum('estado_antes',\App\Enums\Historicos\Estado::getEnumOptions())->nullable();
            $table->enum('estado_despues',\App\Enums\Historicos\Estado::getEnumOptions());
            $table->string('comentario');
            $table->unsignedBigInteger('proyecto_id');
            $table->unsignedBigInteger('autor');
            $table->foreign('autor')
            ->references('usuario_id')->on('usuarios')
            ->onUpdate('cascade')
            ->onDelete('restrict');

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
        Schema::dropIfExists('historico_estados');
    }
}
