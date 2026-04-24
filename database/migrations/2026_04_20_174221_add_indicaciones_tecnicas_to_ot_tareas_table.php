<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
	{
		Schema::table('ot_tareas', function (Blueprint $table) {
			// Añadimos el campo después de la descripción de la tarea
			$table->text('indicaciones_tecnicas')->nullable()->after('descripcion_tarea');
		});
	}

	public function down(): void
	{
		Schema::table('ot_tareas', function (Blueprint $table) {
			$table->dropColumn('indicaciones_tecnicas');
		});
	}
};
