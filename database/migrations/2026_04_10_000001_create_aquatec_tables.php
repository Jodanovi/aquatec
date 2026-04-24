<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Tabla de OTs
        Schema::create('orden_trabajos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_ot')->unique();
            $table->string('cliente');
            $table->string('embarcacion_unidad');
            $table->string('lugar');
            $table->date('permiso_fecha_vigencia')->nullable();
            $table->boolean('permiso_negociable')->default(false);
            $table->enum('estado', ['creada', 'en_planificacion', 'activa', 'finalizada'])->default('creada');
            $table->timestamps();
        });

        Schema::create('ot_indicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_trabajo_id')->constrained('orden_trabajos')->onDelete('cascade');
            $table->text('indicacion');
            $table->timestamps();
        });

        Schema::create('ot_tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_trabajo_id')->constrained('orden_trabajos')->onDelete('cascade');
            $table->string('descripcion_tarea');
            $table->boolean('esta_completada')->default(false);
            $table->timestamps();
        });

        Schema::create('ot_reportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ot_tarea_id')->constrained('ot_tareas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); 
            $table->text('reporte_crudo');
            $table->text('reporte_profesional')->nullable();
            $table->json('fotos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('ot_reportes');
        Schema::dropIfExists('ot_tareas');
        Schema::dropIfExists('ot_indicaciones');
        Schema::dropIfExists('orden_trabajos');
    }
};