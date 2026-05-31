<?php
// app/Http/Controllers/ReporteController.php
namespace App\Http\Controllers;

use App\Models\RegistroActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteExport;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $filtroAccion = $request->query('accion','todas');
        $filtroRol    = $request->query('rol','todos');
        $filtroFecha  = $request->query('fecha','');

        $query = RegistroActividad::query()->orderBy('id','desc');

        if ($filtroAccion !== 'todas') $query->where('accion', $filtroAccion);
        if ($filtroRol    !== 'todos') $query->where('rol', $filtroRol);
        if ($filtroFecha)              $query->whereRaw("DATE(fecha_hora) = ?", [$filtroFecha]);

        $actividad = $query->limit(200)->get();
        $acciones  = RegistroActividad::select('accion')->distinct()->pluck('accion');

        $totalLogins   = RegistroActividad::where('accion','Inicio de sesión')->count();
        $totalFallidos = RegistroActividad::where('accion','Intento de sesión fallido')->count();
        $totalProps    = RegistroActividad::where('accion','Propiedad registrada')->count();
        $totalHoy      = RegistroActividad::whereRaw("DATE(fecha_hora) = CURRENT_DATE")->count();

        // Guardar/actualizar exportes (PDF y Excel) para los filtros actuales
        try {
            $this->storeReports($actividad, $filtroAccion, $filtroRol, $filtroFecha);
        } catch (\Throwable $e) {
            // No detener la vista si falla la generación de archivos; solo registrar si hay logger
            if (function_exists('logger')) logger('Error generando reportes: ' . $e->getMessage());
        }

        return view('compartido.reportes', compact(
            'actividad','acciones',
            'totalLogins','totalFallidos','totalProps','totalHoy',
            'filtroAccion','filtroRol','filtroFecha'
        ));
    }

    /**
     * Endpoint para descargar el reporte generado (pdf|xlsx)
     */
    public function export(Request $request, $type)
    {
        $filtroAccion = $request->query('accion','todas');
        $filtroRol    = $request->query('rol','todos');
        $filtroFecha  = $request->query('fecha','');

        // Soporte CSV inmediato sin paquetes externos
        if ($type === 'csv') {
            $query = RegistroActividad::query()->orderBy('id','desc');
            if ($filtroAccion !== 'todas') $query->where('accion', $filtroAccion);
            if ($filtroRol    !== 'todos') $query->where('rol', $filtroRol);
            if ($filtroFecha)              $query->whereRaw("DATE(fecha_hora) = ?", [$filtroFecha]);
            $actividad = $query->limit(200)->get();

            $filename = 'Reportes_LORENT-INMOBILIARIA_' . now()->format('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($actividad) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID','FechaHora','Nombre','Correo','Rol','Accion','Descripcion','IP']);
                foreach ($actividad as $r) {
                    fputcsv($out, [
                        $r->id,
                        optional($r->fecha_hora)->format('Y-m-d H:i:s'),
                        $r->nombre,
                        $r->correo,
                        $r->rol,
                        $r->accion,
                        $r->descripcion,
                        $r->ip,
                    ]);
                }
                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        }

        $hash = md5(implode('|', [$filtroAccion, $filtroRol, $filtroFecha]));
        $folder = 'reports';

        // Si el usuario pidió PDF/XLSX pero faltan las dependencias, entregar CSV como fallback
        if ($type === 'pdf' && !class_exists('Barryvdh\\DomPDF\\Facade\\Pdf')) {
            // Regenerar actividad y servir CSV
            $query = RegistroActividad::query()->orderBy('id','desc');
            if ($filtroAccion !== 'todas') $query->where('accion', $filtroAccion);
            if ($filtroRol    !== 'todos') $query->where('rol', $filtroRol);
            if ($filtroFecha)              $query->whereRaw("DATE(fecha_hora) = ?", [$filtroFecha]);
            $actividad = $query->limit(200)->get();
            // Reuse CSV stream
            $filename = 'Reportes_LORENT-INMOBILIARIA_fallback_' . now()->format('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];
            $callback = function() use ($actividad) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID','FechaHora','Nombre','Correo','Rol','Accion','Descripcion','IP']);
                foreach ($actividad as $r) {
                    fputcsv($out, [
                        $r->id,
                        optional($r->fecha_hora)->format('Y-m-d H:i:s'),
                        $r->nombre,
                        $r->correo,
                        $r->rol,
                        $r->accion,
                        $r->descripcion,
                        $r->ip,
                    ]);
                }
                fclose($out);
            };
            return response()->stream($callback, 200, $headers);
        }

        if ($type === 'xlsx' && !class_exists('Maatwebsite\\Excel\\Facades\\Excel')) {
            // Mismo fallback a CSV
            $query = RegistroActividad::query()->orderBy('id','desc');
            if ($filtroAccion !== 'todas') $query->where('accion', $filtroAccion);
            if ($filtroRol    !== 'todos') $query->where('rol', $filtroRol);
            if ($filtroFecha)              $query->whereRaw("DATE(fecha_hora) = ?", [$filtroFecha]);
            $actividad = $query->limit(200)->get();
            $filename = 'Reportes_LORENT-INMOBILIARIA_fallback_' . now()->format('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];
            $callback = function() use ($actividad) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID','FechaHora','Nombre','Correo','Rol','Accion','Descripcion','IP']);
                foreach ($actividad as $r) {
                    fputcsv($out, [
                        $r->id,
                        optional($r->fecha_hora)->format('Y-m-d H:i:s'),
                        $r->nombre,
                        $r->correo,
                        $r->rol,
                        $r->accion,
                        $r->descripcion,
                        $r->ip,
                    ]);
                }
                fclose($out);
            };
            return response()->stream($callback, 200, $headers);
        }

        if ($type === 'pdf') {
            $path = "$folder/Reportes_LORENT-INMOBILIARIA_{$hash}.pdf";
        } else {
            $path = "$folder/Reportes_LORENT-INMOBILIARIA_{$hash}.xlsx";
        }

        if (!Storage::disk('public')->exists($path)) {
            // Si no existe, regenerar y luego devolver
            $query = RegistroActividad::query()->orderBy('id','desc');
            if ($filtroAccion !== 'todas') $query->where('accion', $filtroAccion);
            if ($filtroRol    !== 'todos') $query->where('rol', $filtroRol);
            if ($filtroFecha)              $query->whereRaw("DATE(fecha_hora) = ?", [$filtroFecha]);
            $actividad = $query->limit(200)->get();
            $this->storeReports($actividad, $filtroAccion, $filtroRol, $filtroFecha);
        }

        return Storage::disk('public')->download($path);
    }

    /**
     * Genera y guarda PDF y Excel en storage/app/public/reports
     */
    private function storeReports($actividad, $filtroAccion, $filtroRol, $filtroFecha): void
    {
        $hash = md5(implode('|', [$filtroAccion, $filtroRol, $filtroFecha]));
        $folder = 'reports';
        // Asegurar carpeta
        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }

        // PDF (si está disponible)
        if (class_exists('Barryvdh\\DomPDF\\Facade\\Pdf')) {
            $pdfView = view('compartido.reportes_pdf', compact('actividad'));
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHtml($pdfView->render())->setPaper('a4', 'portrait');
            $pdfPath = "$folder/Reportes_LORENT-INMOBILIARIA_{$hash}.pdf";
            Storage::disk('public')->put($pdfPath, $pdf->output());
        }

        // Excel (si está disponible)
        if (class_exists('Maatwebsite\\Excel\\Facades\\Excel')) {
            $excelPath = "$folder/Reportes_LORENT-INMOBILIARIA_{$hash}.xlsx";
            \Maatwebsite\Excel\Facades\Excel::store(new ReporteExport($actividad), $excelPath, 'public');
        }
    }
}