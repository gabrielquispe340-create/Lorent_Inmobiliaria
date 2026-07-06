<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

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
        'precio_anterior','created_at',
    ];

    protected $casts = [
        'precio'          => 'float',
        'precio_anterior' => 'float',
        'created_at'      => 'datetime',
    ];

    protected $appends = ['badges'];

    public function getBadgesAttribute(): array
    {
        $badges = [];

        if ($this->precio_anterior > 0 && $this->precio < $this->precio_anterior) {
            $porcentaje = round((1 - $this->precio / $this->precio_anterior) * 100);
            $badges[] = [
                'texto'  => "!Rebajado! -{$porcentaje}%",
                'tipo'   => 'danger',
                'icono'  => 'ti ti-arrow-down-right',
            ];
        }

        if ($this->created_at && $this->created_at->gt(now()->subDays(3))) {
            $badges[] = [
                'texto'  => 'Nuevo',
                'tipo'   => 'success',
                'icono'  => 'ti ti-sparkles',
            ];
        }

        $solicitudesActivas = $this->solicitudes()
            ->whereNotIn('estado', ['Rechazada', 'Cancelada'])
            ->count();

        if ($solicitudesActivas >= 3) {
            $badges[] = [
                'texto'  => 'Alta Demanda',
                'tipo'   => 'warning',
                'icono'  => 'ti ti-flame',
            ];
        }

        return $badges;
    }

    public function agente() {
        return $this->belongsTo(Usuario::class, 'agente_id');
    }
    public function solicitudes() {
        return $this->hasMany(SolicitudVisita::class, 'propiedad_id');
    }
    public function resenas() {
        return $this->hasMany(Resena::class, 'propiedad_id');
    }

    public function favoritos() {
        return $this->hasMany(Favorito::class, 'propiedad_id');
    }
}