<?php
// este archivo trabaja con los modelos propiedad,usuario,solicitudvisita y registroactividad para mostrar las estadísticas y datos relevantes en los dashboards de cada rol (administrador,agente,asistente y cliente)
// app/Http/Controllers/DashboardController.php — REEMPLAZA el tuyo actual
namespace App\Http\Controllers;

use App\Models\{Propiedad, Usuario, SolicitudVisita, RegistroActividad};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // dashboard para el administrador, muestra estadísticas generales del sistema como total de propiedades, usuarios, ventas y las últimas propiedades registradas.
    public function admin()
    {
        $totalProps    = Propiedad::count();
        $disponibles   = Propiedad::where('estado','Disponible')->count();
        $totalUsuarios = Usuario::count();
        $totalVentas   = Propiedad::where('estado','Vendido')->count();
        $ultimas       = Propiedad::with('agente')->orderBy('id','desc')->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalProps','disponibles','totalUsuarios','totalVentas','ultimas'
        ));
    }
// dashboard para el agente, muestra estadísticas de sus propiedades, visitas pendientes y las últimas visitas solicitadas por los clientes.
    public function agente()
    {
        $id          = Auth::id();
        $misProps    = Propiedad::where('agente_id',$id)->count();
        $disponibles = Propiedad::where('agente_id',$id)->where('estado','Disponible')->count();
        $vendidas    = Propiedad::where('agente_id',$id)->where('estado','Vendido')->count();
        $visitasPend = SolicitudVisita::where('estado', 'Pendiente')->whereHas('propiedad', function($q) { $q->where('agente_id', auth()->id()); })->count();
        $ultimas     = Propiedad::where('agente_id',$id)->orderBy('id','desc')->limit(5)->get();
        $visitas     = SolicitudVisita::with(['propiedad','cliente'])
                           ->where('estado', 'Pendiente')
                           ->whereHas('propiedad', function($q) { $q->where('agente_id', auth()->id()); })
                           ->orderBy('fecha_solicitada')->take(5)->get();

        return view('agente.dashboard', compact(
            'misProps','disponibles','vendidas','visitasPend','ultimas','visitas'
        ));
    }
// dashboard para el asistente, muestra estadísticas de clientes, visitas pendientes, visitas programadas para hoy y las últimas actividades realizadas en el sistema.
    public function asistente(Request $request)
    {
        // Stats
        $totalClientes = Usuario::where('rol','cliente')->count();
        $visitasPend   = SolicitudVisita::where('estado','pendiente')->count();
        $visitasHoy    = SolicitudVisita::whereDate('fecha_solicitada', today())->count();
        $totalProps    = Propiedad::where('estado','Disponible')->count();

        // Últimos clientes registrados
        $clientes = Usuario::where('rol','cliente')->orderBy('id','desc')->limit(5)->get();

        // Próximas visitas pendientes (para el dashboard)
        $visitas = SolicitudVisita::with(['propiedad','cliente'])
                        ->where('estado','pendiente')
                        ->orderBy('fecha_solicitada')
                        ->limit(5)
                        ->get();

        return view('asistente.dashboard', compact(
            'totalClientes','visitasPend','visitasHoy','totalProps',
            'clientes','visitas'
        ));
    }
// dashboard para el cliente, muestra estadísticas de propiedades disponibles, ventas, alquileres y las últimas propiedades registradas en el sistema.
    public function cliente()
    {
        $totalDisp     = Propiedad::where('estado','Disponible')->count();
        $totalVenta    = Propiedad::where('estado','Disponible')->where('tipo','Venta')->count();
        $totalAlquiler = Propiedad::where('estado','Disponible')->where('tipo','Alquiler')->count();
        $propiedades   = Propiedad::where('estado','Disponible')->orderBy('id','desc')->limit(8)->get();

        return view('cliente.dashboard', compact(
            'totalDisp','totalVenta','totalAlquiler','propiedades'
        ));
    }
}