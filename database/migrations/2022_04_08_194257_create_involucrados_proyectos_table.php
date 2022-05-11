<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvolucradosProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        
        Schema::create('involucrados_proyectos', function (Blueprint $table) { 
            $table->bigIncrements('involucrado_id');
            $table->enum('rol_involucrado',\App\Enums\Involucrados\Rol::getEnumOptions());
            $table->string('nombre',400);
            $table->string('tipo');
            $table->unsignedBigInteger('proyecto_id'); 
            
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
        Schema::dropIfExists('involucrados_proyectos');
    }
}
