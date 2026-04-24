<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtTareaReporte extends Model
{
    protected $table = 'ot_tarea_reportes';

    protected $fillable = [
        'ot_tarea_id',
        'user_id',
        'comentario',
        'foto_path'
    ];

    public function tarea() {
        return $this->belongsTo(OtTarea::class, 'ot_tarea_id');
    }

    public function tecnico() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}