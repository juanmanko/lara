<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComentariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->bigIncrements('comentario_id');
            $table->unsignedBigInteger('proyecto_id');
            $table->unsignedBigInteger('campo_id');
            $table->text('texto');
            $table->dateTime('fecha_creacion');
            $table->integer('autor');
            $table->integer('origen');
            $table->enum('estado',\App\Enums\Comentarios\Comentario::getEnumOptions());

            $table->foreign('proyecto_id')
            ->references('proyecto_id')->on('proyectos')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('campo_id')
            ->references('campo_id')->on('campos_formulario')
            ->onUpdate('cascade')
            ->onDelete('restrict');
            
            $table->timestamps();

            /*
                INSERT INTO `comentarios` (`comentario_id`, `proyecto_id`, `campo_id`, `texto`, `fecha_creacion`, `autor`, `origen`, `estado`, `created_at`, `updated_at`) VALUES (NULL, '30', '1', 'PROYECTO CON COMENTARIOS CON ESTADO ABIERTO', '2022-04-19 22:12:40.000000', '2', '1', 'Abierto', '2022-04-19 15:12:40', '2022-04-19 15:12:40');
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
        Schema::dropIfExists('comentarios');
    }
}
