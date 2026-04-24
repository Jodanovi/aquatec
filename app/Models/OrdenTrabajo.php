<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenTrabajo extends Model
{
    use HasFactory;

    protected $table = 'orden_trabajos';

    protected $fillable = [
        'numero_ot', 
        'cliente', 
        'embarcacion_unidad', 
        'lugar', 
        'permiso_fecha_vigencia', 
        'permiso_negociable', 
        'estado',
        'user_id', 
        'updated_by',
        // CAMPOS QUE FALTABAN:
        'conclusion_jefe', 
        'fecha_inicio', 
        'fecha_finalizacion'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_finalizacion' => 'datetime',
        'permiso_fecha_vigencia' => 'date',
    ];

    // Relación para saber quién la creó
    public function creador() {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación para saber quién fue el último en editar
    public function editor() {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function generarProximoNumero()
    {
        $anio = date('y'); // 26
        // Buscamos la última OT que termine en .26
        $ultimaOt = self::where('numero_ot', 'like', "%.$anio")
                        ->orderBy('id', 'desc')
                        ->first();

        if (!$ultimaOt) {
            return "001.$anio";
        }

        // Extraemos el número correlativo antes del punto
        $partes = explode('.', $ultimaOt->numero_ot);
        $correlativo = (int)$partes[0] + 1;
        
        // Retornamos con formato 00X.26
        return str_pad($correlativo, 3, '0', STR_PAD_LEFT) . ".$anio";
    }

    // Relación con las indicaciones
    public function indicaciones()
    {
        return $this->hasMany(OtIndicacion::class, 'orden_trabajo_id');
    }

    // Relación con las tareas
    public function tareas()
    {
        return $this->hasMany(OtTarea::class, 'orden_trabajo_id');
    }
}