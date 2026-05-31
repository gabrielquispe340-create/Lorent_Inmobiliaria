<?php
// app/Http/Controllers/UsuarioController.php
namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\RegistroActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::orderBy('nombre')->get();
        return view('admin.usuarios', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|min:3',
            'correo'    => 'required|email|unique:usuarios,correo',
            'usuario'   => 'required|min:3|unique:usuarios,usuario',
            'contrasena'=> 'required|min:3',
            'rol'       => 'required|in:administrador,agente,asistente,cliente',
            'descripcion' => 'nullable|string',
        ]);

        $data = $request->only(['nombre','correo','usuario','contrasena','rol','descripcion']);
        // Only keep descripcion when rol is cliente to match DB constraint
        if (($data['rol'] ?? '') !== 'cliente') {
            $data['descripcion'] = null;
        }

        $user = Usuario::create($data);
        RegistroActividad::log('Usuario creado',
            "Se creó el usuario {$user->nombre} con rol {$user->rol}.");

        return back()->with('success','Usuario agregado correctamente.');
    }

    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nombre'  => 'required|min:3',
            'correo'  => 'required|email|unique:usuarios,correo,'.$usuario->id,
            'usuario' => 'required|min:3|unique:usuarios,usuario,'.$usuario->id,
            'rol'     => 'required|in:administrador,agente,asistente,cliente',
            'descripcion' => 'nullable|string',
        ]);
        $data = $request->only(['nombre','correo','usuario','rol','descripcion']);
        if ($request->filled('contrasena')) {
            $data['contrasena'] = $request->contrasena;
        }

        if (($data['rol'] ?? '') !== 'cliente') {
            $data['descripcion'] = null;
        }

        $usuario->update($data);
        RegistroActividad::log('Usuario editado', "Se editó el usuario {$usuario->nombre}.");
        return back()->with('success','Usuario actualizado correctamente.');
    }

    public function destroy(Usuario $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return back()->with('error','No puedes eliminar tu propia cuenta.');
        }
        $nombre = $usuario->nombre;
        $usuario->delete();
        RegistroActividad::log('Usuario eliminado', "Se eliminó el usuario $nombre.");
        return back()->with('success','Usuario eliminado correctamente.');
    }

    public function perfil()
    {
        $usuario = Auth::user();
        return view('compartido.perfil', compact('usuario'));
    }

    public function actualizarPerfil(Request $request)
    {
        $usuario = Auth::user();
        $request->validate([
            'nombre'  => 'required|min:3',
            'usuario' => 'required|min:3|unique:usuarios,usuario,'.$usuario->id,
        ]);

        $request->validate([
            'descripcion' => 'nullable|string',
        ]);

        $data = ['nombre' => $request->nombre, 'usuario' => $request->usuario, 'descripcion' => $request->descripcion ?? null];
        if ($usuario->rol !== 'cliente') {
            $data['descripcion'] = null;
        }
        if ($request->filled('contrasena_nueva')) {
            $data['contrasena'] = $request->contrasena_nueva;
        }
        $usuario->update($data);
        return back()->with('success','Perfil actualizado correctamente.');
    }
}