<?php
// este archivo realiza todas las operaciones relacionadas con las propiedades, como listar, crear, actualizar, eliminar y buscar propiedades. También maneja la lógica de negocio relacionada con las propiedades, como validar los datos de entrada, gestionar las imágenes y registrar las actividades realizadas en el sistema.
// app/Http/Controllers/PropiedadController.php — REEMPLAZA el tuyo
namespace App\Http\Controllers;

use App\Models\Propiedad;
use App\Models\Usuario;
use App\Models\Resena;
use App\Models\RegistroActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropiedadController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->esAdmin()) {
            $agentes     = Usuario::where('rol','agente')->orderBy('nombre')->get();
            $propiedades = Propiedad::with('agente')->orderBy('id','desc')->get();
            return view('admin.propiedades', compact('propiedades','agentes'));
        }
        $propiedades = Propiedad::where('agente_id',$user->id)->orderBy('id','desc')->get();
        return view('agente.propiedades', compact('propiedades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'      => 'required|min:5',
            'tipo'        => 'required|in:Venta,Alquiler,Anticretico',
            'zona'        => 'required',
            'precio'      => 'required|numeric|min:1',
            'area'        => 'required|numeric|min:1',
            'descripcion' => 'required|min:10',
            'estado'      => 'required|in:Disponible,Reservado,Vendido',
            'imagen'      => 'nullable|image|max:2048',
        ]);
        $data = $request->only(['titulo','tipo','zona','precio','area','descripcion','estado','agente_id']);
        if (Auth::user()->esAgente()) $data['agente_id'] = Auth::id();
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('propiedades', 'public');
        }
        $data['latitud']  = $request->latitud  ?: null;
        $data['longitud'] = $request->longitud ?: null;
        $prop = Propiedad::create($data);
        RegistroActividad::log('Propiedad registrada', "Se registró: \"{$prop->titulo}\" ({$prop->tipo}) en {$prop->zona}.");
        return back()->with('success','Propiedad registrada correctamente.');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'titulo'      => 'required|min:5',
            'tipo'        => 'required|in:Venta,Alquiler,Anticretico',
            'zona'        => 'required',
            'precio'      => 'required|numeric|min:1',
            'area'        => 'required|numeric|min:1',
            'descripcion' => 'required|min:10',
            'estado'      => 'required|in:Disponible,Reservado,Vendido',
            'imagen'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $propiedad = Propiedad::findOrFail($id);
        $datos = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            if ($propiedad->imagen) {
                Storage::disk('public')->delete($propiedad->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('propiedades', 'public');
        }

        $propiedad->update($datos);
        RegistroActividad::log('Propiedad modificada', "Se modificó ID {$propiedad->id}: \"{$propiedad->titulo}\".");
        return back()->with('success','Propiedad actualizada correctamente.');
    }


    public function destroy(Propiedad $propiedad)
    {
        $titulo = $propiedad->titulo;
        $propiedad->delete();
        RegistroActividad::log('Propiedad eliminada', "Se eliminó: \"$titulo\".");
        return back()->with('success','Propiedad eliminada correctamente.');
    }

    public function disponibles(Request $request)
    {
        $tipos_validos = ['Venta','Alquiler','Anticretico'];
        $tipo   = $request->query('tipo');
        $filtro = ($tipo && in_array($tipo, $tipos_validos)) ? $tipo : 'Todas';
        $query  = Propiedad::where('estado','Disponible')
            ->withAvg('resenas', 'puntuacion')
            ->withCount('resenas');
        if ($filtro !== 'Todas') $query->where('tipo', $filtro);
        $propiedades = $query->orderBy('id','desc')->get();
        return view('cliente.propiedades', compact('propiedades','filtro'));
    }

    // ─── CU8: BUSCAR PROPIEDADES ─────────────────────────────
    public function buscar(Request $request)
    {
        $q         = trim($request->query('q', ''));
        $tipo      = $request->query('tipo', 'Todas');
        $estado    = $request->query('estado', 'Disponible');
        $precioMax = $request->query('precio_max', '');
        $areaMin   = $request->query('area_min', '');

        if (!in_array($tipo,   ['Todas','Venta','Alquiler','Anticretico'])) $tipo   = 'Todas';
        if (!in_array($estado, ['Disponible','Reservado','Todas']))          $estado = 'Disponible';

        $query = Propiedad::query();

        if ($estado !== 'Todas') $query->where('estado', $estado);
        if ($tipo   !== 'Todas') $query->where('tipo', $tipo);

        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('titulo',       'ilike', "%{$q}%")
                    ->orWhere('zona',        'ilike', "%{$q}%")
                    ->orWhere('descripcion', 'ilike', "%{$q}%");
            });
        }

        if ($precioMax !== '' && is_numeric($precioMax)) {
            $query->where('precio', '<=', (float)$precioMax);
        }
        if ($areaMin !== '' && is_numeric($areaMin)) {
            $query->where('area', '>=', (float)$areaMin);
        }

        $propiedades      = $query->orderBy('id','desc')->get();
        $totalDisponibles = Propiedad::where('estado','Disponible')->count();

        return view('cliente.buscar', compact(
            'propiedades','q','tipo','estado','precioMax','areaMin','totalDisponibles'
        ));
    }

    public function detalle(Propiedad $propiedad)
    {
        $propiedad->load('agente', 'resenas.cliente');
        $user = Auth::user();
        $yaResenado = $user && $user->esCliente()
            ? Resena::where('propiedad_id', $propiedad->id)
                    ->where('cliente_id', $user->id)
                    ->exists()
            : false;
        return view('cliente.detalle', compact('propiedad', 'yaResenado'));
    }

    public function storeResena(Request $request, Propiedad $propiedad)
    {
        $request->validate([
            'puntuacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        if (!$user->esCliente()) {
            return back()->with('error', 'Solo los clientes pueden dejar reseñas.');
        }

        $existe = Resena::where('propiedad_id', $propiedad->id)
                        ->where('cliente_id', $user->id)
                        ->exists();
        if ($existe) {
            return back()->with('error', 'Ya has dejado una reseña para esta propiedad.');
        }

        Resena::create([
            'propiedad_id' => $propiedad->id,
            'cliente_id'   => $user->id,
            'puntuacion'   => $request->puntuacion,
            'comentario'   => $request->comentario,
            'fecha'        => now(),
        ]);

        RegistroActividad::log(
            'Resena creada',
            "El cliente {$user->nombre} reseñó \"{$propiedad->titulo}\" con {$request->puntuacion} estrellas."
        );

        return back()->with('success', 'Reseña publicada correctamente.');
    }

    // ─── CU8: BUSCAR PROPIEDADES — ADMINISTRADOR ────────────────
    public function buscarAdmin(Request $request)
    {
        $q         = trim($request->query('q', ''));
        $tipo      = $request->query('tipo', 'Todas');
        $estado    = $request->query('estado', 'Todas');
        $precioMax = $request->query('precio_max', '');
        $areaMin   = $request->query('area_min', '');
        $agenteId  = $request->query('agente_id', '');
 
        if (!in_array($tipo,   ['Todas','Venta','Alquiler','Anticretico'])) $tipo   = 'Todas';
        if (!in_array($estado, ['Disponible','Reservado','Vendido','Todas'])) $estado = 'Todas';
 
        // Admin ve TODAS las propiedades
        $query = Propiedad::with('agente');
 
        if ($estado !== 'Todas')        $query->where('estado', $estado);
        if ($tipo   !== 'Todas')        $query->where('tipo', $tipo);
        if ($agenteId !== '')           $query->where('agente_id', $agenteId);
 
        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('titulo',       'ilike', "%{$q}%")
                    ->orWhere('zona',        'ilike', "%{$q}%")
                    ->orWhere('descripcion', 'ilike', "%{$q}%");
            });
        }
 
        if ($precioMax !== '' && is_numeric($precioMax)) {
            $query->where('precio', '<=', (float)$precioMax);
        }
        if ($areaMin !== '' && is_numeric($areaMin)) {
            $query->where('area', '>=', (float)$areaMin);
        }
 
        $propiedades      = $query->orderBy('id', 'desc')->get();
        $totalPropiedades = Propiedad::count();
        $agentes          = Usuario::where('rol', 'agente')->orderBy('nombre')->get();
 
        return view('admin.buscar', compact(
            'propiedades', 'q', 'tipo', 'estado',
            'precioMax', 'areaMin', 'agenteId',
            'totalPropiedades', 'agentes'
        ));
    }
 
    // ─── CU8: BUSCAR PROPIEDADES — AGENTE ───────────────────────
    public function buscarAgente(Request $request)
    {
        $q         = trim($request->query('q', ''));
        $tipo      = $request->query('tipo', 'Todas');
        $estado    = $request->query('estado', 'Todas');
        $precioMax = $request->query('precio_max', '');
        $areaMin   = $request->query('area_min', '');
 
        if (!in_array($tipo,   ['Todas','Venta','Alquiler','Anticretico'])) $tipo   = 'Todas';
        if (!in_array($estado, ['Disponible','Reservado','Vendido','Todas'])) $estado = 'Todas';
 
        // Agente solo ve SUS propiedades
        $query = Propiedad::where('agente_id', Auth::id());
 
        if ($estado !== 'Todas') $query->where('estado', $estado);
        if ($tipo   !== 'Todas') $query->where('tipo', $tipo);
 
        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('titulo',       'ilike', "%{$q}%")
                    ->orWhere('zona',        'ilike', "%{$q}%")
                    ->orWhere('descripcion', 'ilike', "%{$q}%");
            });
        }
 
        if ($precioMax !== '' && is_numeric($precioMax)) {
            $query->where('precio', '<=', (float)$precioMax);
        }
        if ($areaMin !== '' && is_numeric($areaMin)) {
            $query->where('area', '>=', (float)$areaMin);
        }
 
        $propiedades      = $query->orderBy('id', 'desc')->get();
        $totalPropiedades = Propiedad::where('agente_id', Auth::id())->count();
 
        return view('agente.buscar', compact(
            'propiedades', 'q', 'tipo', 'estado',
            'precioMax', 'areaMin', 'totalPropiedades'
        ));
    }
 
    // ─── CU8: BUSCAR PROPIEDADES — ASISTENTE ────────────────────
    public function buscarAsistente(Request $request)
    {
        $q         = trim($request->query('q', ''));
        $tipo      = $request->query('tipo', 'Todas');
        $estado    = $request->query('estado', 'Disponible');
        $precioMax = $request->query('precio_max', '');
        $areaMin   = $request->query('area_min', '');
 
        if (!in_array($tipo,   ['Todas','Venta','Alquiler','Anticretico'])) $tipo   = 'Todas';
        if (!in_array($estado, ['Disponible','Reservado','Todas']))          $estado = 'Disponible';
 
        // Asistente ve todas las propiedades (solo lectura)
        $query = Propiedad::with('agente');
 
        if ($estado !== 'Todas') $query->where('estado', $estado);
        if ($tipo   !== 'Todas') $query->where('tipo', $tipo);
 
        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('titulo',       'ilike', "%{$q}%")
                    ->orWhere('zona',        'ilike', "%{$q}%")
                    ->orWhere('descripcion', 'ilike', "%{$q}%");
            });
        }
 
        if ($precioMax !== '' && is_numeric($precioMax)) {
            $query->where('precio', '<=', (float)$precioMax);
        }
        if ($areaMin !== '' && is_numeric($areaMin)) {
            $query->where('area', '>=', (float)$areaMin);
        }
 
        $propiedades      = $query->orderBy('id', 'desc')->get();
        $totalPropiedades = Propiedad::count();
 
        return view('asistente.buscar', compact(
            'propiedades', 'q', 'tipo', 'estado',
            'precioMax', 'areaMin', 'totalPropiedades'
        ));
    }
}