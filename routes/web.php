<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\TecnicoController; 
use App\Models\OrdenTrabajo;
use Illuminate\Support\Facades\Route;

// El público va al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Solo tú sabes que esta URL existe para ver el diseño original
Route::get('/debug-inicio', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('tecnico')) {
        return redirect()->route('tecnico.index');
    }
    
    $ots = OrdenTrabajo::latest()->take(10)->get();
    return view('dashboard', compact('ots'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    
    // --- BLOQUE 1: GESTIÓN Y PLANIFICACIÓN (ADMIN/JEFE/OPERADOR) ---
    
    // El resource cubre index, create, store, show, edit, update, destroy
    Route::resource('orden_trabajos', OrdenTrabajoController::class);
    
    // Rutas de estados de la OT
    Route::patch('/orden_trabajos/{id}/activar', [OrdenTrabajoController::class, 'activar'])->name('orden_trabajos.activar');
    Route::post('/orden_trabajos/{id}/cancelar-planificacion', [OrdenTrabajoController::class, 'cancelarPlanificacion'])->name('orden_trabajos.cancelar');
    
    // Cierre definitivo y bloqueo de la OT (Jefe Técnico)
    Route::patch('/orden_trabajos/{id}/finalizar', [OrdenTrabajoController::class, 'finalizar'])->name('orden_trabajos.finalizar');
    
    // --- BLOQUE 2: EJECUCIÓN TÉCNICA ---
    Route::prefix('ejecucion')->group(function () {
        Route::get('/', [TecnicoController::class, 'index'])->name('tecnico.index');
        Route::get('/ot/{id}', [TecnicoController::class, 'show'])->name('tecnico.show');
        
        // RUTA DE HISTORIAL (Movida a TecnicoController)
        // El "where" al final permite que el ID contenga barras diagonales como "R/E PARAPITI"
        Route::get('/historial/{id}', [TecnicoController::class, 'historialUnidad'])->name('unidades.historial');

        Route::post('/tarea/{id}/validar', [TecnicoController::class, 'validarTarea'])->name('tecnico.tarea.validar');
        
        // Gestión de Reportes
        Route::post('/tarea/{tarea_id}/reporte', [TecnicoController::class, 'guardarReporte'])->name('tecnico.reporte.guardar');
        Route::patch('/reporte/{id}/actualizar', [TecnicoController::class, 'actualizarReporte'])->name('reporte.actualizar');
        
        // Eliminar el reporte completo (texto y fotos)
        Route::delete('/reporte/{id}/eliminar', [TecnicoController::class, 'eliminarReporte'])->name('tecnico.reporte.eliminar');
        
        // Eliminar solo una foto individual del reporte
        Route::delete('/reporte/{id}/foto-especifica', [TecnicoController::class, 'eliminarFotoReporte'])->name('tecnico.foto.eliminar');
    });

    // --- BLOQUE 3: PERFIL DE USUARIO ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';