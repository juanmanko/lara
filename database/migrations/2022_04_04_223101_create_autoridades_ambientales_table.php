<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoridadesAmbientalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('autoridades_ambientales', function (Blueprint $table) {
            $table->bigIncrements('autoridad_id');
            $table->string('cod_autoridad_ambiental',100)->unique();
            $table->string('nombre');
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
        Schema::dropIfExists('autoridades_ambientales');
    }
}
