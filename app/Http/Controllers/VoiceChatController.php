<?php
namespace App\Http\Controllers;

use App\Models\RegistroActividad;
use App\Models\SolicitudVisita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoiceChatController extends Controller
{
    public function process(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $text = mb_strtolower(trim($request->text));
        $rol = $user->rol;

        $response = $this->interpretCommand($text, $rol, $user);

        RegistroActividad::log(
            'Comando por voz',
            "El usuario {$user->nombre} ({$rol}) dijo: \"{$request->text}\""
        );

        return response()->json($response);
    }

    private function interpretCommand(string $text, string $rol, $user): array
    {
        // ── REPORTES (admin, asistente) ──────────────────────────
        if (in_array($rol, ['administrador', 'asistente']) && $this->match($text, ['reporte', 'reportes', 'exportar', 'descargar reporte', 'generar reporte'])) {
            return $this->reportResponse($rol);
        }

        // ── EXCEL ────────────────────────────────────────────────
        if (in_array($rol, ['administrador', 'asistente']) && $this->match($text, ['excel', 'xlsx'])) {
            return $this->reportResponse($rol, 'excel');
        }

        // ── PDF ──────────────────────────────────────────────────
        if (in_array($rol, ['administrador', 'asistente']) && $this->match($text, ['pdf'])) {
            return $this->reportResponse($rol, 'pdf');
        }

        // ── CSV ──────────────────────────────────────────────────
        if (in_array($rol, ['administrador', 'asistente']) && $this->match($text, ['csv'])) {
            return $this->reportResponse($rol, 'csv');
        }

        // ── VISITAS PRÓXIMAS (cliente) ───────────────────────────
        if ($rol === 'cliente' && $this->match($text, ['visita', 'visitas', 'próximas visitas', 'mis visitas', 'citas', 'próximas citas'])) {
            return $this->clientVisitsResponse($user);
        }

        // ── VISITAS PENDIENTES (agente) ─────────────────────────
        if ($rol === 'agente' && $this->match($text, ['pendiente', 'pendientes', 'visitas pendientes', 'solicitudes pendientes', 'visitas solicitadas'])) {
            return $this->agentPendingVisitsResponse($user);
        }

        // ── TODOS LOS ROLES: ayuda ──────────────────────────────
        if ($this->match($text, ['ayuda', 'help', 'qué puedes hacer', 'que puedes hacer', 'comandos', 'opciones'])) {
            return $this->helpResponse($rol);
        }

        // ── FALLBACK ────────────────────────────────────────────
        return [
            'type' => 'text',
            'message' => $this->fallbackMessage($rol),
        ];
    }

    private function match(string $text, array $keywords): bool
    {
        foreach ($keywords as $kw) {
            if (str_contains($text, $kw)) {
                return true;
            }
        }
        return false;
    }

    private function reportResponse(string $rol, ?string $format = null): array
    {
        $baseUrl = $rol === 'administrador'
            ? route('admin.reportes', [], false)
            : route('asistente.reportes', [], false);

        $reportUrl = url($baseUrl);
        $pdfUrl = url($baseUrl . '/export/pdf');
        $xlsxUrl = url($baseUrl . '/export/xlsx');
        $csvUrl = url($baseUrl . '/export/csv');

        if ($format === 'pdf') {
            return [
                'type' => 'download',
                'format' => 'pdf',
                'url' => $pdfUrl,
                'message' => 'Claro, aquí tienes el enlace para descargar el reporte en PDF. Haz clic para descargarlo.',
            ];
        }

        if ($format === 'excel') {
            return [
                'type' => 'download',
                'format' => 'xlsx',
                'url' => $xlsxUrl,
                'message' => 'Por supuesto, aquí tienes el reporte en Excel listo para descargar.',
            ];
        }

        if ($format === 'csv') {
            return [
                'type' => 'download',
                'format' => 'csv',
                'url' => $csvUrl,
                'message' => 'Aquí está el reporte en formato CSV. Puedes descargarlo desde este enlace.',
            ];
        }

        return [
            'type' => 'multi_download',
            'files' => [
                ['format' => 'pdf', 'url' => $pdfUrl, 'label' => 'Descargar PDF'],
                ['format' => 'xlsx', 'url' => $xlsxUrl, 'label' => 'Descargar Excel'],
                ['format' => 'csv', 'url' => $csvUrl, 'label' => 'Descargar CSV'],
            ],
            'message' => 'He preparado los reportes para ti. Puedes descargarlos en PDF, Excel o CSV. ¿Cuál prefieres? O dime "excel", "pdf" o "csv" para obtener uno en específico.',
        ];
    }

    private function clientVisitsResponse($user): array
    {
        $visitas = SolicitudVisita::with('propiedad')
            ->where('cliente_id', $user->id)
            ->where('estado', 'Aceptada')
            ->whereDate('fecha_solicitada', '>=', now()->toDateString())
            ->orderBy('fecha_solicitada')
            ->get();

        if ($visitas->isEmpty()) {
            return [
                'type' => 'text',
                'message' => 'No tienes visitas próximas aceptadas en este momento. Si quieres agendar una, ve a la sección de propiedades.',
            ];
        }

        $lista = $visitas->map(function ($v) {
            $fecha = \Carbon\Carbon::parse($v->fecha_solicitada)->format('d/m/Y');
            return "- {$v->propiedad->titulo} el {$fecha}";
        })->implode("\n");

        $count = $visitas->count();
        $msg = $count === 1
            ? "Tienes 1 visita próxima aceptada:\n{$lista}"
            : "Tienes {$count} visitas próximas aceptadas:\n{$lista}";

        return [
            'type' => 'text',
            'message' => $msg,
        ];
    }

    private function agentPendingVisitsResponse($user): array
    {
        $visitas = SolicitudVisita::with(['propiedad', 'cliente'])
            ->where('estado', 'Pendiente')
            ->whereHas('propiedad', fn($q) => $q->where('agente_id', $user->id))
            ->orderBy('fecha_solicitada')
            ->get();

        if ($visitas->isEmpty()) {
            return [
                'type' => 'text',
                'message' => 'No tienes solicitudes de visita pendientes. ¡Buen trabajo!',
            ];
        }

        $lista = $visitas->map(function ($v) {
            $fecha = \Carbon\Carbon::parse($v->fecha_solicitada)->format('d/m/Y');
            return "- {$v->propiedad->titulo} — {$v->cliente->nombre} — {$fecha}";
        })->implode("\n");

        $count = $visitas->count();
        $msg = $count === 1
            ? "Tienes 1 solicitud de visita pendiente:\n{$lista}"
            : "Tienes {$count} solicitudes de visita pendientes:\n{$lista}";

        return [
            'type' => 'text',
            'message' => $msg,
        ];
    }

    private function helpResponse(string $rol): array
    {
        $comandos = match ($rol) {
            'administrador' => [
                '- "generar reporte" / "reportes" — ver opciones de descarga',
                '- "pdf" — descargar reporte en PDF',
                '- "excel" — descargar reporte en Excel',
                '- "csv" — descargar reporte en CSV',
                '- "ayuda" — mostrar esta ayuda',
            ],
            'asistente' => [
                '- "generar reporte" / "reportes" — ver opciones de descarga',
                '- "pdf" — descargar reporte en PDF',
                '- "excel" — descargar reporte en Excel',
                '- "csv" — descargar reporte en CSV',
                '- "ayuda" — mostrar esta ayuda',
            ],
            'agente' => [
                '- "visitas pendientes" — ver solicitudes de visita pendientes',
                '- "ayuda" — mostrar esta ayuda',
            ],
            'cliente' => [
                '- "mis visitas" / "próximas visitas" — ver tus visitas aceptadas próximas',
                '- "ayuda" — mostrar esta ayuda',
            ],
            default => ['- "ayuda" — mostrar esta ayuda'],
        };

        $msg = "Puedes decirme:\n" . implode("\n", $comandos);

        return [
            'type' => 'text',
            'message' => $msg,
        ];
    }

    private function fallbackMessage(string $rol): string
    {
        $sugerencias = match ($rol) {
            'administrador' => 'Puedes pedirme: "reportes", "pdf", "excel", "csv" o "ayuda".',
            'asistente' => 'Puedes pedirme: "reportes", "pdf", "excel", "csv" o "ayuda".',
            'agente' => 'Puedes pedirme: "visitas pendientes" o "ayuda".',
            'cliente' => 'Puedes pedirme: "mis visitas", "próximas visitas" o "ayuda".',
            default => 'Puedes pedirme "ayuda" para ver lo que puedo hacer.',
        };

        return "No entendí tu solicitud. {$sugerencias}";
    }
}
