<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtReporte extends Model
{
    protected $table = 'ot_reportes';

    protected $fillable = [
        'ot_tarea_id',
        'user_id',
        'reporte_crudo',
        'reporte_profesional',
        'fotos'
    ];

    protected $casts = [
        'fotos' => 'array' // Para que Laravel maneje el JSON de las fotos automáticamente
    ];
}