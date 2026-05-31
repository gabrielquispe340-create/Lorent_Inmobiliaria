<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class ReporteExport implements FromCollection, WithHeadings
{
    protected $actividad;

    public function __construct($actividad)
    {
        $this->actividad = $actividad;
    }

    public function collection()
    {
        return $this->actividad->map(function ($r) {
            return collect([
                'id' => $r->id,
                'fecha' => optional($r->fecha_hora)->format('Y-m-d H:i:s'),
                'nombre' => $r->nombre,
                'correo' => $r->correo,
                'rol' => $r->rol,
                'accion' => $r->accion,
                'descripcion' => $r->descripcion,
                'ip' => $r->ip,
            ]);
        });
    }

    public function headings(): array
    {
        return [
            'ID', 'FechaHora', 'Nombre', 'Correo', 'Rol', 'Accion', 'Descripcion', 'IP'
        ];
    }
}
