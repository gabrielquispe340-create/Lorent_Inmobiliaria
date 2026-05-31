<?php
// app/Models/Usuario.php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    protected $fillable = [
        'nombre','correo','usuario','contrasena','rol','descripcion',
    ];

    public function getAuthPassword() { return $this->contrasena; }

    public function propiedades() {
        return $this->hasMany(Propiedad::class, 'agente_id');
    }

    public function solicitudes() {
        return $this->hasMany(SolicitudVisita::class, 'cliente_id');
    }

    public function seguimientosComoCliente() {
        return $this->hasMany(Seguimiento::class, 'cliente_id');
    }

    public function seguimientosComoAgente() {
        return $this->hasMany(Seguimiento::class, 'agente_id');
    }

    public function esAdmin()     { return $this->rol === 'administrador'; }
    public function esAgente()    { return $this->rol === 'agente'; }
    public function esAsistente() { return $this->rol === 'asistente'; }
    public function esCliente()   { return $this->rol === 'cliente'; }
}