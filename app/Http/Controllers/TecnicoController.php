<?php

namespace App\Http\Controllers;

use App\Models\OtTarea;
use App\Models\OtTareaReporte;
use App\Models\OrdenTrabajo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TecnicoController extends Controller
{
    public function index()
    {
        // Solo mostramos OTs activas para que el técnico no se confunda
        $ots = OrdenTrabajo::whereIn('estado', ['activa', 'Activa', 'ACTIVA'])
        ->orderBy('created_at', 'desc')
        ->get();
        return view('tecnico.index', compact('ots'));
    }

    public function show($id)
    {
        $ot = OrdenTrabajo::with(['tareas.reportes.tecnico', 'indicaciones'])->findOrFail($id);
        return view('tecnico.show', compact('ot'));
    }

    public function guardarReporte(Request $request, $tareaId)
    {
        $request->validate([
            'comentario' => 'required|string|max:1000',
            'fotos.*' => 'image|mimes:jpeg,png,jpg|max:4096', 
        ]);

        $tarea = OtTarea::findOrFail($tareaId);
        $rutas = [];

        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $rutas[] = $foto->store('reportes', 'public');
            }
        }

        $reporte = new OtTareaReporte();
        $reporte->ot_tarea_id = $tarea->id; 
        $reporte->user_id = Auth::id() ?? 1; // Un pequeño respaldo por si se pierde la sesión
        $reporte->comentario = $request->comentario;
        
        if (!empty($rutas)) {
            $reporte->foto_path = implode(',', $rutas); 
        }
        
        $reporte->save();

        if ($request->has('finalizar_tarea')) {
            $tarea->update(['estado' => 'finalizada']);
        }

        // --- ESTO ES LO QUE ARREGLA EL BUCLE ---
        if ($request->expectsJson() || $request->isJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reporte sincronizado con éxito'
            ], 200);
        }

        return redirect()->back()->with('success', 'Reporte guardado con éxito.');
    }

    public function actualizarReporte(Request $request, $id)
    {
        $reporte = OtTareaReporte::findOrFail($id);
        
        // Cambiamos 'required' por 'nullable' para que pueda subir fotos sin tocar el texto
        $request->validate([
            'comentario' => 'nullable|string|max:1000',
            'fotos.*' => 'image|mimes:jpeg,png,jpg|max:4096',
        ]);

        // Solo actualiza el comentario si el usuario escribió algo nuevo
        if ($request->filled('comentario')) {
            $reporte->comentario = $request->comentario;
        }

        if ($request->hasFile('fotos')) {
            $nuevasRutas = [];
            foreach ($request->file('fotos') as $foto) {
                $nuevasRutas[] = $foto->store('reportes', 'public');
            }
            $fotosActuales = $reporte->foto_path ? explode(',', $reporte->foto_path) : [];
            $reporte->foto_path = implode(',', array_merge($fotosActuales, $nuevasRutas));
        }

        $reporte->save();
        return redirect()->back()->with('success', 'Reporte actualizado por la jefatura.');
    }

    /**
     * Elimina un reporte completo y sus archivos físicos
     */
    public function eliminarReporte($id)
    {
        $reporte = OtTareaReporte::findOrFail($id);

        // Borrar fotos físicas del storage antes de eliminar el registro
        if ($reporte->foto_path) {
            $fotos = explode(',', $reporte->foto_path);
            foreach ($fotos as $foto) {
                Storage::disk('public')->delete(trim($foto));
            }
        }

        $reporte->delete();

        return redirect()->back()->with('success', 'El reporte y sus evidencias han sido eliminados.');
    }

    public function eliminarFotoReporte(Request $request, $id)
    {
        $reporte = OtTareaReporte::findOrFail($id);
        $fotoABorrar = $request->foto_path;

        Storage::disk('public')->delete($fotoABorrar);

        $fotos = explode(',', $reporte->foto_path);
        $fotosFiltradas = array_filter($fotos, function($f) use ($fotoABorrar) {
            return trim($f) !== trim($fotoABorrar);
        });

        $reporte->foto_path = count($fotosFiltradas) > 0 ? implode(',', $fotosFiltradas) : null;
        $reporte->save();

        return redirect()->back()->with('success', 'Foto eliminada.');
    }

    public function validarTarea(Request $request, $tareaId)
    {
        $request->validate([
            'validacion_profesional' => 'required|string|max:1000',
        ]);

        $tarea = OtTarea::findOrFail($tareaId);
        $tarea->update([
            'validacion_profesional' => $request->validacion_profesional,
            'estado_tarea' => 'validada'
        ]);

        return redirect()->back()->with('success', 'Tarea validada profesionalmente.');
    }

    public function historialUnidad($id) 
    {
        // 1. Buscamos la OT actual para saber de qué unidad y cliente hablamos
        $otActual = \App\Models\OrdenTrabajo::findOrFail($id);

        // 2. Buscamos todas las OTs finalizadas que coincidan con ESE cliente y ESA unidad
        $historial = \App\Models\OrdenTrabajo::where('cliente', $otActual->cliente)
            ->where('embarcacion_unidad', $otActual->embarcacion_unidad)
            ->where('estado', 'finalizada')
            ->with(['tareas.reportes.tecnico'])
            ->latest()
            ->get();

        // 3. Pasamos los datos originales a la vista
        return view('tecnico.historial', [
            'historial' => $historial,
            'unidad' => strtoupper($otActual->embarcacion_unidad), // Forzamos mayúsculas: R/E PARAPITI
            'cliente' => strtoupper($otActual->cliente)           // Forzamos mayúsculas: IB PY
        ]);
    }
}