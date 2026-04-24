<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Primero borramos si existen por si acaso quedaron rastros
        Schema::dropIfExists('ot_tarea_reportes');
        Schema::dropIfExists('tarea_reportes');

        Schema::create('ot_tarea_reportes', function (Blueprint $table) {
            $table->id();
            // Asegúrate que tu tabla de tareas se llame 'ot_tareas'
            $table->foreignId('ot_tarea_id')->constrained('ot_tareas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->text('comentario');
            $table->string('foto_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ot_tarea_reportes');
    }
};