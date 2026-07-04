<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    protected $table      = 'resenas';
    public $timestamps    = false;

    protected $fillable = [
        'propiedad_id', 'cliente_id', 'puntuacion', 'comentario', 'fecha',
    ];

    protected $casts = [
        'puntuacion' => 'integer',
        'fecha'      => 'datetime',
    ];

    public function propiedad()
    {
        return $this->belongsTo(Propiedad::class, 'propiedad_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Usuario::class, 'cliente_id');
    }
}
