<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMunicipiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('municipios', function (Blueprint $table) {
            $table->bigIncrements('municipio_id');
            $table->string('nom_municipio',100);
            $table->unsignedBigInteger('departamento_id');

            $table->foreign('departamento_id')
            ->references('departamento_id')->on('departamentos')
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
        Schema::dropIfExists('municipios'); 
    }
}
