<?php

namespace App\Http\Controllers;

use App\Models\Prospecto;
use App\Models\Propiedad;
use App\Models\RegistroActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProspectoController extends Controller
{
    public function index()
    {
        $prospectos = Prospecto::with('propiedad')
            ->where('agente_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('agente.prospectos.index', compact('prospectos'));
    }

    public function create()
    {
        $propiedades = Propiedad::where('agente_id', Auth::id())->get();
        return view('agente.prospectos.create', compact('propiedades'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'propiedad_id' => 'nullable|exists:propiedades,id',
            'etapa' => 'required|in:Nuevo,Contactado,Interesado,Negociando,Cerrado,Perdido',
            'notas' => 'nullable|string',
        ]);

        $validated['agente_id'] = Auth::id();
        $validated['fecha_contacto'] = now();

        $prospecto = Prospecto::create($validated);

        RegistroActividad::log(
            'Prospecto registrado',
            "El agente registró el prospecto {$prospecto->nombre}."
        );

        return redirect()->route('agente.prospectos.index')
            ->with('success', 'Prospecto registrado correctamente.');
    }

    public function edit(Prospecto $prospecto)
    {
        if ($prospecto->agente_id !== Auth::id()) {
            abort(403);
        }

        $propiedades = Propiedad::where('agente_id', Auth::id())->get();
        return view('agente.prospectos.edit', compact('prospecto', 'propiedades'));
    }

    public function update(Request $request, Prospecto $prospecto)
    {
        if ($prospecto->agente_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'propiedad_id' => 'nullable|exists:propiedades,id',
            'etapa' => 'required|in:Nuevo,Contactado,Interesado,Negociando,Cerrado,Perdido',
            'notas' => 'nullable|string',
        ]);

        $estadoAnterior = $prospecto->etapa;
        $prospecto->update($validated);

        if ($estadoAnterior !== $validated['etapa']) {
            RegistroActividad::log(
                'Estado de prospecto actualizado',
                "El agente cambió el estado del prospecto {$prospecto->nombre} de {$estadoAnterior} a {$validated['etapa']}."
            );
        }

        return redirect()->route('agente.prospectos.index')
            ->with('success', 'Prospecto actualizado correctamente.');
    }

    public function destroy(Prospecto $prospecto)
    {
        if ($prospecto->agente_id !== Auth::id()) {
            abort(403);
        }

        $prospecto->delete();

        return redirect()->route('agente.prospectos.index')
            ->with('success', 'Prospecto eliminado correctamente.');
    }
}
