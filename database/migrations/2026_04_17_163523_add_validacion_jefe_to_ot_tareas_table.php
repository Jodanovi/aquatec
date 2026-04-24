<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('ot_tareas', function (Blueprint $table) {
            $table->text('validacion_profesional')->nullable(); // El "toque" del jefe
            $table->string('estado_tarea')->default('pendiente'); // pendiente, validada
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ot_tareas', function (Blueprint $table) {
            //
        });
    }
};
