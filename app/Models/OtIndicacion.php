<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtIndicacion extends Model
{
    protected $table = 'ot_indicaciones';

    protected $fillable = [
        'orden_trabajo_id',
        'indicacion'
    ];

    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }
}