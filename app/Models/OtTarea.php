<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtTarea extends Model
{
    protected $table = 'ot_tareas';

    protected $fillable = [
        'orden_trabajo_id', 
        'descripcion_tarea', 
        'indicaciones_tecnicas', // <--- Agrega esta línea
        'esta_completada', 
        'validacion_profesional', 
        'estado_tarea'
    ];

    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'orden_trabajo_id');
    }

    // Corregido: Apuntamos al modelo exacto OtTareaReporte
    public function reportes()
    {
        return $this->hasMany(OtTareaReporte::class, 'ot_tarea_id');
    }
}