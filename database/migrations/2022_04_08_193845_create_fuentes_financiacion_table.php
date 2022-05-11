<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFuentesFinanciacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fuentes_financiacion', function (Blueprint $table) {
            $table->unsignedBigInteger('proyecto_id');
            $table->enum('tipo_fuente_financiacion_psa',\App\Enums\Financiaciones\Tipo::getEnumOptions());
            $table->string('nom_fuente_financiacion_psa');
            $table->string('codigo_divipola');
            $table->float('valor_financiado_psa');

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
        Schema::dropIfExists('fuentes_financiacion');
    }
}
