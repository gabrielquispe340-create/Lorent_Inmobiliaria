<?php
// app/Models/Propiedad.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de propiedad inmobiliaria.
 *
 * Representa una fila de la tabla `propiedades` y define
 * las relaciones con agente y solicitudes de visita.
 */
class Propiedad extends Model
{
    protected $table   = 'propiedades';
    public $timestamps = false;

    protected $fillable = [
        'titulo','tipo','zona','precio','area','descripcion',
        'estado','agente_id','imagen',
        'categoria_id','propietario_id',
        'habitaciones','banos','antiguedad',
        'latitud','longitud',
    ];

    public function agente() {
        return $this->belongsTo(Usuario::class, 'agente_id');
    }
    public function solicitudes() {
        return $this->hasMany(SolicitudVisita::class, 'propiedad_id');
    }
}