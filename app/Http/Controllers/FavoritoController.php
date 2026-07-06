<?php
namespace App\Http\Controllers;

use App\Models\Favorito;
use App\Models\Propiedad;
use App\Models\RegistroActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritoController extends Controller
{
    public function index()
    {
        $favoritos = Propiedad::whereHas('favoritos', function ($q) {
            $q->where('cliente_id', Auth::id());
        })->withAvg('resenas', 'puntuacion')
          ->withCount('resenas')
          ->orderBy('id', 'desc')
          ->get();

        return view('cliente.favoritos', compact('favoritos'));
    }

    public function toggle($propiedadId)
    {
        $propiedad = Propiedad::findOrFail($propiedadId);
        $clienteId = Auth::id();

        $existe = Favorito::where('cliente_id', $clienteId)
            ->where('propiedad_id', $propiedadId)
            ->exists();

        if ($existe) {
            Favorito::where('cliente_id', $clienteId)
                ->where('propiedad_id', $propiedadId)
                ->delete();

            RegistroActividad::log(
                'Favorito eliminado',
                "El cliente quitó \"{$propiedad->titulo}\" de favoritos."
            );

            return back()->with('success', 'Propiedad eliminada de favoritos.');
        }

        Favorito::create([
            'cliente_id'    => $clienteId,
            'propiedad_id'  => $propiedadId,
        ]);

        RegistroActividad::log(
            'Favorito agregado',
            "El cliente agregó \"{$propiedad->titulo}\" a favoritos."
        );

        return back()->with('success', 'Propiedad agregada a favoritos.');
    }

    public function destroy($propiedadId)
    {
        $propiedad = Propiedad::findOrFail($propiedadId);

        $deleted = Favorito::where('cliente_id', Auth::id())
            ->where('propiedad_id', $propiedadId)
            ->delete();

        if ($deleted) {
            RegistroActividad::log(
                'Favorito eliminado',
                "El cliente quitó \"{$propiedad->titulo}\" de favoritos desde Mis Favoritos."
            );
        }

        return redirect()->route('cliente.favoritos.index')
            ->with('success', 'Propiedad eliminada de favoritos.');
    }
}
