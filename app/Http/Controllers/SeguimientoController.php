<?php

namespace App\Http\Controllers;

use App\Models\Seguimiento;
use App\Models\Usuario;
use App\Models\SolicitudVisita;
use App\Models\RegistroActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeguimientoController extends Controller
{
    public function index(Usuario $cliente)
    {
        $cliente = Usuario::where('id', $cliente->id)
            ->where('rol', 'cliente')
            ->whereHas('solicitudes', function ($query) {
                $query->whereHas('propiedad', function ($query) {
                    $query->where('agente_id', Auth::id());
                });
            })
            ->firstOrFail();

        $seguimientos = Seguimiento::with('agente')
            ->where('cliente_id', $cliente->id)
            ->where('agente_id', Auth::id())
            ->orderByDesc('fecha')
            ->get();

        $visitas = SolicitudVisita::with('propiedad')
            ->where('cliente_id', $cliente->id)
            ->whereHas('propiedad', function ($query) {
                $query->where('agente_id', Auth::id());
            })
            ->orderByDesc('fecha_solicitada')
            ->get();

        return view('agente.cliente-seguimientos', compact('cliente', 'seguimientos', 'visitas'));
    }

    public function store(Request $request, Usuario $cliente)
    {
        $cliente = Usuario::where('id', $cliente->id)
            ->where('rol', 'cliente')
            ->whereHas('solicitudes', function ($query) {
                $query->whereHas('propiedad', function ($query) {
                    $query->where('agente_id', Auth::id());
                });
            })
            ->firstOrFail();

        $validated = $request->validate([
            'tipo_contacto' => 'required|in:Llamada,Correo,Visita,WhatsApp,Otro',
            'descripcion'   => 'required|min:10',
            'fecha'         => 'required|date',
        ], [
            'tipo_contacto.required' => 'Selecciona el tipo de contacto.',
            'tipo_contacto.in'       => 'Tipo de contacto inválido.',
            'descripcion.required'   => 'La descripción es obligatoria.',
            'descripcion.min'        => 'La nota debe tener al menos 10 caracteres.',
            'fecha.required'         => 'La fecha es obligatoria.',
            'fecha.date'             => 'La fecha no es válida.',
        ]);

        Seguimiento::create([
            'agente_id'     => Auth::id(),
            'cliente_id'    => $cliente->id,
            'tipo_contacto' => $validated['tipo_contacto'],
            'descripcion'   => $validated['descripcion'],
            'fecha'         => $validated['fecha'],
        ]);

        RegistroActividad::log(
            'Seguimiento registrado',
            "El agente registró un seguimiento para el cliente {$cliente->nombre} ({$validated['tipo_contacto']})."
        );

        return redirect()->route('agente.clientes.seguimientos', $cliente)
            ->with('success', 'Seguimiento guardado correctamente.');
    }
}
