<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proyectos', function (Blueprint $table) {

            $table->bigIncrements('proyecto_id');   
            $table->string('nom_proyecto_psa',128); //required
            $table->integer('anno_implementacion'); 
            $table->dateTime('fecha_registro')->unique(); // Unique
            $table->enum('etapa_proyecto_psa',\App\Enums\Proyectos\Etapa::getEnumOptions()); //required
            $table->enum('tipo_proyecto_psa',\App\Enums\Proyectos\Tipo::getEnumOptions()); //required;
            $table->enum('modalidad_proyecto_psa',\App\Enums\Proyectos\Modalidad::getEnumOptions())->nullable();
            $table->float('gasto_administrativo_psa')->nullable();
            $table->float('gasto_monitoreo_psa')->nullable();
            $table->float('valor_total_psa')->nullable();
            $table->text('beneficiado_directo_psa',256)->nullable();
            $table->float('area_predio_ecosistema_psa')->nullable();
            $table->enum('metodo_estimacion_valor_incentivo',\App\Enums\Proyectos\Metodo::getEnumOptions())->nullable();
            $table->string('otro_metodo_estimacion',64)->nullable();
            $table->enum('metodo_pago_psa',\App\Enums\Proyectos\Pago::getEnumOptions())->nullable();
            $table->enum('periodicidad_pago_psa',\App\Enums\Proyectos\Periodicidad::getEnumOptions())->nullable();
            $table->string('otra_periodicidad_psa',64)->nullable();
            $table->float('termino_duracion_acuerdo')->nullable();
            $table->enum('tipos_acuerdo_psa',\App\Enums\Proyectos\Acuerdo::getEnumOptions())->nullable();
            $table->integer('num_acuerdo_celebrado')->nullable();
            $table->enum('fuente_shapes',\App\Enums\Proyectos\Shape::getEnumOptions())->nullable();
            $table->unsignedBigInteger('autor_id'); //required

            $table->foreign('autor_id')
            ->references('usuario_id')->on('usuarios')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            //$table->timestamps();  

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proyectos');
    }
}
