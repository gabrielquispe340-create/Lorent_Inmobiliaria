<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    protected $table = 'seguimientos';
    public $timestamps = false;

    protected $fillable = [
        'agente_id',
        'cliente_id',
        'tipo_contacto',
        'descripcion',
        'fecha',
    ];

    public function agente()
    {
        return $this->belongsTo(Usuario::class, 'agente_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Usuario::class, 'cliente_id');
    }
}
