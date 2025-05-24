<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportesTable extends Migration
{
    public function up()
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->string('modulo');
            $table->string('formato');
            $table->string('fecha_inicio');
            $table->string('fecha_fin');
            $table->string('ordenar_por')->nullable();
            $table->enum('orden', ['asc', 'desc'])->nullable();
            $table->string('filtros')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reportes');
    }
};

