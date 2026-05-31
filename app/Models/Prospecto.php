<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospecto extends Model
{
    use HasFactory;

    protected $table = 'prospectos';

    protected $fillable = [
        'agente_id',
        'nombre',
        'telefono',
        'email',
        'propiedad_id',
        'etapa',
        'notas',
        'fecha_contacto',
    ];

    public function agente()
    {
        return $this->belongsTo(Usuario::class, 'agente_id');
    }

    public function propiedad()
    {
        return $this->belongsTo(Propiedad::class, 'propiedad_id');
    }
}
