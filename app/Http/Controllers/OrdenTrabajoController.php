<?php

namespace App\Http\Controllers;

use App\Models\OtTareaReporte;
use App\Models\OrdenTrabajo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrdenTrabajoController extends Controller
{
    public function create()
    {
        if (auth()->user()->role == 'tecnico') {
            return redirect()->route('dashboard')->with('error', 'No tienes permiso para crear órdenes.');
        }
        
        $proximaOt = OrdenTrabajo::generarProximoNumero();
        $clientesExistentes = OrdenTrabajo::distinct()->pluck('cliente');
        return view('orden_trabajos.create', compact('proximaOt', 'clientesExistentes'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'permiso_negociable' => $request->has('permiso_negociable'),
            'numero_ot' => OrdenTrabajo::generarProximoNumero(),
            'user_id' => Auth::id(),
            'estado' => 'creada'
        ]);

        OrdenTrabajo::create($request->all());

        return redirect()->route('dashboard')->with('success', 'OT generada correctamente.');
    }

    public function edit($id)
    {
        $ot = OrdenTrabajo::with(['tareas', 'indicaciones'])->findOrFail($id);
        
        if ($ot->estado == 'activa' || $ot->estado == 'finalizada') {
            return redirect()->route('dashboard')->with('error', 'Esta OT ya está en ejecución y no puede modificarse.');
        }

        return view('orden_trabajos.edit', compact('ot'));
    }

    /**
     * ACTUALIZADO: Procesa array de tareas con indicaciones técnicas
     */
    public function update(Request $request, $id)
    {
        $ot = OrdenTrabajo::findOrFail($id);

        // Validación ajustada para el nuevo formato de array asociativo
        $request->validate([
            'indicacion' => 'required|string',
            'tareas' => 'required|array|min:1',
            'tareas.*.descripcion' => 'required|string',
            'tareas.*.indicaciones' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $ot) {
            $ot->update([
                'updated_by' => Auth::id(),
                'estado' => 'en_planificacion'
            ]);

            // Limpiamos registros previos para evitar duplicados al re-editar
            $ot->indicaciones()->delete();
            $ot->tareas()->delete();

            // Guardamos la logística general
            $ot->indicaciones()->create([
                'indicacion' => $request->indicacion,
                'user_id' => Auth::id()
            ]);

            // Guardamos cada tarea con su respectiva indicación técnica
            foreach ($request->tareas as $tareaData) {
                if (!empty($tareaData['descripcion'])) {
                    $ot->tareas()->create([
                        'descripcion_tarea' => $tareaData['descripcion'],
                        'indicaciones_tecnicas' => $tareaData['indicaciones'] ?? null,
                        'esta_completada' => false,
                        'estado_tarea' => 'pendiente'
                    ]);
                }
            }
        });

        return redirect()->route('orden_trabajos.edit', $ot->id)
                         ->with('success', 'Planificación guardada. Por favor, verifique y active la orden.');
    }

    public function show($id)
    {
        $ot = OrdenTrabajo::with([
            'tareas.reportes.tecnico', 
            'tareas.reportes.editor', 
            'indicaciones'
        ])->findOrFail($id);

        return view('orden_trabajos.show', compact('ot'));
    }

    public function activar($id)
    {
        $ot = OrdenTrabajo::findOrFail($id);
        
        $ot->update([
            'estado' => 'activa',
            'fecha_inicio' => now(),
            'updated_by' => Auth::id()
        ]);

        return redirect()->route('dashboard')->with('success', 'OT Activada. El técnico ya puede trabajar.');
    }

    public function eliminarReporte($id)
    {
        $reporte = OtTareaReporte::findOrFail($id);
        
        if ($reporte->tarea->ordenTrabajo->estado !== 'activa') {
            return redirect()->back()->with('error', 'No se puede eliminar: la OT ya no está en ejecución.');
        }

        if ($reporte->foto_path) {
            $fotos = explode(',', $reporte->foto_path);
            foreach ($fotos as $foto) {
                Storage::disk('public')->delete(trim($foto));
            }
        }

        $reporte->delete();
        return redirect()->back()->with('success', 'Reporte eliminado correctamente.');
    }

    public function cancelarPlanificacion($id)
    {
        $ot = OrdenTrabajo::findOrFail($id);

        if ($ot->estado == 'en_planificacion') {
            DB::transaction(function () use ($ot) {
                $ot->tareas()->delete();
                $ot->indicaciones()->delete();
                $ot->update([
                    'estado' => 'creada',
                    'updated_by' => null
                ]);
            });
        }

        return redirect()->route('dashboard')->with('info', 'Se canceló la edición. La OT volvió a su estado inicial.');
    }

    public function finalizar(Request $request, $id)
    {
        // Bajamos el mínimo para que no sea tan estricto en pruebas
        $request->validate([
            'conclusion_jefe' => 'required|string|min:3', 
        ]);

        $ot = OrdenTrabajo::findOrFail($id);
        
        // Usamos asignación directa para estar seguros
        $ot->conclusion_jefe = $request->conclusion_jefe;
        $ot->estado = 'finalizada';
        $ot->fecha_finalizacion = now();
        
        if ($ot->save()) {
            return redirect()->route('dashboard')->with('success', 'Orden de Trabajo finalizada correctamente.');
        }

        return back()->with('error', 'Error al intentar guardar los cambios.');
    }

    public function historialUnidad($embarcacion_nombre)
    {
        // Buscamos todas las OTs que pertenezcan a esa embarcación/unidad
        $historial = \App\Models\OrdenTrabajo::where('embarcacion_unidad', $embarcacion_nombre)
            ->with(['tareas', 'creador']) // Traemos las tareas y quién creó la OT
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orden_trabajos.historial', [
            'historial' => $historial,
            'unidad_nombre' => $embarcacion_nombre
        ]);
    }
}