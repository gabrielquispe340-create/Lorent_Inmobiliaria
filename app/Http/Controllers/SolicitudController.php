<?php
// app/Http/Controllers/SolicitudController.php
namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\SolicitudVisita;
use App\Models\Propiedad;
use App\Models\RegistroActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SolicitudController extends Controller
{
    public function misSolicitudes()
    {
        $solicitudes = SolicitudVisita::with('propiedad')
            ->where('cliente_id', Auth::id())
            ->orderBy('id','desc')
            ->get();

        return view('cliente.solicitudes', compact('solicitudes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'propiedad_id'    => 'required|exists:propiedades,id',
            'fecha_solicitada'=> 'required|date|after_or_equal:today',
            'mensaje'         => 'required|min:5',
        ]);

        SolicitudVisita::create([
            'propiedad_id'    => $request->propiedad_id,
            'cliente_id'      => Auth::id(),
            'fecha_solicitada'=> $request->fecha_solicitada,
            'mensaje'         => $request->mensaje,
            'estado'          => 'Pendiente',
        ]);

        $prop = Propiedad::find($request->propiedad_id);
        RegistroActividad::log('Solicitud de visita enviada',
            "Cliente solicitó visita para \"{$prop->titulo}\" el {$request->fecha_solicitada}.");

        return redirect()->route('cliente.propiedades')
                         ->with('success','Solicitud enviada. Un agente te contactará pronto.');
    }

    public function visitasAgente(Request $request)
    {
        // Mantener el valor original para la vista (ej. 'todas', 'pendiente', 'confirmada')
        $rawFiltro = strtolower($request->query('estado', 'todas'));

        // Mapear los filtros legibles a los valores reales almacenados en la BD
        $map = [
            'todas'      => null,
            'pendiente'  => 'Pendiente',
            'confirmada' => 'Aceptada',
            'confirmado' => 'Aceptada',
            'cancelada'  => 'Rechazada',
            'rechazada'  => 'Rechazada',
        ];

        $query = SolicitudVisita::with(['propiedad','cliente'])
            ->whereHas('propiedad', fn($q) => $q->where('agente_id', Auth::id()));

        if (isset($map[$rawFiltro]) && $map[$rawFiltro] !== null) {
            $query->where('estado', $map[$rawFiltro]);
        }

        $solicitudes = $query->orderBy('fecha_solicitada')->get();
        // Pasar a la vista el filtro en su forma original para mantener la UI
        $filtro = $rawFiltro;
        return view('agente.visitas', compact('solicitudes','filtro'));
    }

    public function actualizarEstado(Request $request, SolicitudVisita $solicitud)
    {
        $request->validate(['estado' => 'required|in:Aceptada,Rechazada']);
        $solicitud->update(['estado' => $request->estado]);
        $msg = $request->estado === 'Aceptada' ? 'Visita confirmada.' : 'Visita cancelada.';
        return back()->with('success', $msg);
    }

    public function visitasAsistente()
    {
        $solicitudes = SolicitudVisita::with(['propiedad','cliente'])
            ->orderBy('fecha_solicitada')
            ->get();
        return view('asistente.visitas', compact('solicitudes'));
    }

    public function clientesAgente()
    {
        $clientes = SolicitudVisita::with('cliente')
            ->whereHas('propiedad', fn($q) => $q->where('agente_id', Auth::id()))
            ->select('cliente_id')
            ->selectRaw('COUNT(*) as total_visitas')
            ->selectRaw('MAX(fecha_solicitada) as ultima_visita')
            ->groupBy('cliente_id')
            ->get();

        return view('agente.clientes', compact('clientes'));
    }

    public function cambiarEstado(Request $request, $id)
    {
        $solicitud = SolicitudVisita::findOrFail($id);
        $solicitud->estado = $request->estado;
        $solicitud->save();
        return back()->with('success', 'Estado actualizado correctamente');
    }

    public function cancelar(Request $request, $id)
    {
        $solicitud = SolicitudVisita::where('id', $id)
            ->where('cliente_id', Auth::id())
            ->where('estado', 'Pendiente')
            ->firstOrFail();

        $solicitud->update(['estado' => 'Rechazada']);
        RegistroActividad::log('Visita cancelada', 'El cliente canceló la solicitud #'.$id);

        return back()->with('success', 'Solicitud cancelada.');
    }

    public function reagendar(Request $request, $id)
    {
        $request->validate([
            'fecha_solicitada' => 'required|date|after_or_equal:today',
        ], [
            'fecha_solicitada.required'        => 'La nueva fecha es obligatoria.',
            'fecha_solicitada.after_or_equal'  => 'La fecha no puede ser anterior a hoy.',
        ]);

        $solicitud = SolicitudVisita::where('id', $id)
            ->where('cliente_id', Auth::id())
            ->where('estado', 'Pendiente')
            ->firstOrFail();

        $solicitud->update(['fecha_solicitada' => $request->fecha_solicitada]);

        RegistroActividad::log(
            'Visita reagendada',
            'El cliente reagendó la solicitud #'.$id.' para '.$request->fecha_solicitada
        );

        return back()->with('success', 'Visita reagendada correctamente.');
    }

    // ── AGENTE ────────────────────────────────────────────
    public function calendarioAgente()
    {
        return view('agente.calendario');
    }

    public function eventosAgente()
    {
        $visitas = SolicitudVisita::with(['propiedad','cliente'])
            ->whereHas('propiedad', fn($q) => $q->where('agente_id', Auth::id()))
            ->get();

        return response()->json($this->formatearEventos($visitas));
    }

    // ── ASISTENTE ─────────────────────────────────────────
    public function calendarioAsistente()
    {
        return view('asistente.calendario');
    }

    public function eventosAsistente()
    {
        $visitas = SolicitudVisita::with(['propiedad','cliente'])->get();
        return response()->json($this->formatearEventos($visitas));
    }

    // ── CLIENTE ───────────────────────────────────────────
    public function calendarioCliente()
    {
        return view('cliente.calendario');
    }

    public function eventosCliente()
    {
        $visitas = SolicitudVisita::with('propiedad')
            ->where('cliente_id', Auth::id())
            ->get();

        return response()->json($this->formatearEventos($visitas));
    }

    // ── HELPER ────────────────────────────────────────────
    private function formatearEventos($visitas)
    {
        $colores = [
            'Pendiente'  => ['background' => '#f59e0b', 'border' => '#d97706'],
            'Aceptada'   => ['background' => '#10b981', 'border' => '#059669'],
            'Rechazada'  => ['background' => '#ef4444', 'border' => '#dc2626'],
            'Completada' => ['background' => '#6366f1', 'border' => '#4f46e5'],
        ];

        return $visitas->map(function($v) use ($colores) {
            $color = $colores[$v->estado] ?? ['background' => '#94a3b8', 'border' => '#64748b'];
            return [
                'id'               => $v->id,
                'title'            => $v->propiedad->titulo ?? 'Propiedad',
                'start'            => $v->fecha_solicitada,
                'backgroundColor'  => $color['background'],
                'borderColor'      => $color['border'],
                'textColor'        => '#ffffff',
                'extendedProps'    => [
                    'estado'   => $v->estado,
                    'cliente'  => $v->cliente->nombre ?? null,
                    'zona'     => $v->propiedad->zona ?? '—',
                    'mensaje'  => $v->mensaje ?? '—',
                ],
            ];
        });
    }
}