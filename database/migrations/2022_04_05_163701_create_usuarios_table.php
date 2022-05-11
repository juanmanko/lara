<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
                $table->bigIncrements('usuario_id');
                $table->string('correo_electronico')->unique();
                $table->integer('tipo_documento'); 
                $table->integer('numero_documento');
                $table->timestamps();

                //Example Insert
                /*
                INSERT INTO `usuarios` (`usuario_id`, `correo_electronico`, `tipo_documento`, `numero_documento`, `created_at`, `updated_at`) 
                VALUES (NULL, 'usuario1@gmail.com', '1', '1152176918', '2022-04-13 12:08:06', '2022-04-13 12:08:06');
                */

          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
