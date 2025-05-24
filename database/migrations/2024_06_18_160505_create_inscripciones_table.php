<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInscripcionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('categoria_id');
            $table->unsignedBigInteger('subcategoria_id');
            $table->unsignedBigInteger('horario_id');
            $table->enum('estado', ['Aprobado', 'Pendiente', 'Rechazado'])->default('Pendiente');
            $table->integer('numero_vacante');
            $table->text('comentarios')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('cascade');
            $table->foreign('subcategoria_id')->references('id')->on('subcategorias')->onDelete('cascade');
            $table->foreign('horario_id')->references('id')->on('horarios')->onDelete('cascade');
        });

        Schema::table('subcategorias', function (Blueprint $table) {
            $table->unsignedInteger('vacantes_ocupadas')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inscripciones');

        Schema::table('subcategorias', function (Blueprint $table) {
            $table->dropColumn('vacantes_ocupadas');
        });
    }
}
