# Documentación línea por línea (archivo clave)

Este documento describe con detalle el comportamiento de los archivos más importantes del proyecto.
Incluye comentarios por secciones y bloques para entender qué hace cada archivo.

---

## `routes/web.php`

```php
<?php

/*
|--------------------------------------------------------------------------
| Rutas de la aplicación
|--------------------------------------------------------------------------
| Aquí se definen las URL públicas y protegidas. Las rutas se agrupan por
| funcionalidad: autenticación, administrador, agente, asistente, cliente.
*/

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropiedadController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\ReporteController;
```

- Las importaciones (`use`) enlazan las rutas con los controladores responsables.

### Sección de autenticación

```php
Route::get('/', [AuthController::class, 'showLogin'])->name('login');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendForgotPassword'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/registro', [AuthController::class, 'showRegistro'])->name('registro');
Route::post('/registro', [AuthController::class, 'registro'])->name('registro.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
```

- `/login`: muestra el formulario de acceso.
- `POST /login`: procesa las credenciales y redirige según rol.
- `/forgot-password`: formulario para solicitar el enlace de recuperación.
- `POST /forgot-password`: envía el correo con el token de restablecimiento.
- `/reset-password/{token}`: muestra el formulario para cambiar la contraseña.
- `/registro`: formulario público de registro de usuario.
- `/logout`: cierra la sesión activa.

### Grupo de rutas por roles

Cada grupo usa middleware para exigir autenticación y comprobar el rol del usuario.
Las rutas comparten un prefijo y un alias de nombre.

- `admin`: solo para `administrador`.
- `agente`: para `agente` y `administrador`.
- `asistente`: para `asistente` y `administrador`.
- `cliente`: para cualquier usuario autenticado.

```php
Route::middleware(['auth', 'role:administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::get('/propiedades', [PropiedadController::class, 'index'])->name('propiedades');
        Route::post('/propiedades', [PropiedadController::class, 'store'])->name('propiedades.store');
        Route::get('/buscar', [PropiedadController::class, 'buscarAdmin'])->name('buscar');
        Route::put('/propiedades/{propiedad}', [PropiedadController::class, 'update'])->name('propiedades.update');
        Route::delete('/propiedades/{propiedad}', [PropiedadController::class, 'destroy'])->name('propiedades.destroy');
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes');
    });
```

- El administrador administra propiedades, usuarios y reportes.
- El agente puede gestionar propiedades propias, ver visitas, clientes y calendario.
- El asistente puede ver búsquedas, visitas, reportes y calendario.
- El cliente puede navegar propiedades, solicitar visitas, cancelar/reagendar y ver su calendario.

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/perfil', [UsuarioController::class, 'perfil'])->name('perfil');
    Route::put('/perfil', [UsuarioController::class, 'actualizarPerfil'])->name('perfil.update');
});
```

- Estas rutas protegen el perfil de cualquier usuario autenticado.

---

## `app/Http/Controllers/AuthController.php`

```php
namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\RegistroActividad;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
```

- Importa modelos y utilidades necesarias: `Usuario`, `RegistroActividad`, correo, sesión y tokens.

```php
class AuthController extends Controller
{
    public function showLogin()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return view('auth.login');
    }
```

- `showLogin()`: muestra la pantalla de login.
- Cierra sesión activa y renueva el token CSRF para mayor seguridad.

```php
    public function login(Request $request)
    {
        $request->validate([
            'correo'    => 'required|email',
            'contrasena'=> 'required',
        ], [
            'correo.required'     => 'El correo es obligatorio.',
            'correo.email'        => 'Ingresa un correo válido.',
            'contrasena.required' => 'La contraseña es obligatoria.',
        ]);

        $usuario = Usuario::where('correo', $request->correo)
            ->where('contrasena', $request->contrasena)
            ->first();

        if ($usuario) {
            Auth::loginUsingId($usuario->id);
            $request->session()->regenerate();
            RegistroActividad::log('Inicio de sesión', "El usuario {$usuario->nombre} ({$usuario->rol}) inició sesión.");
            return $this->redirigirPorRol();
        }

        RegistroActividad::log('Intento de sesión fallido', "Intento fallido con correo: {$request->correo}");
        return back()->withErrors(['correo' => 'Correo o contraseña incorrectos.']);
    }
```

- `login()`: valida los campos y busca usuario en tabla `usuarios`.
- Nota de seguridad: compara contraseña en texto plano, no hashing.
- Si el login es correcto, inicia sesión y redirige según rol.
- Si falla, registra el intento y muestra error.

```php
    public function showRegistro()
    {
        return view('auth.registro');
    }
```

- `showRegistro()`: muestra el formulario de registro.

```php
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }
```

- `showForgotPassword()`: muestra el formulario para solicitar el correo de restablecimiento.

```php
    public function sendForgotPassword(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
        ], [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Ingresa un correo válido.',
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if ($usuario) {
            $token = Str::random(64);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $usuario->correo],
                ['token' => Hash::make($token), 'created_at' => now()]
            );
            $resetUrl = route('password.reset', ['token' => $token, 'email' => $usuario->correo]);
            Mail::to($usuario->correo)->send(new PasswordResetMail($resetUrl, $usuario->nombre));
        }

        return back()->with('success', 'Si ese correo existe, te enviamos un enlace para recuperar la contraseña.');
    }
```

- `sendForgotPassword()`: valida correo y genera token.
- Guarda token en tabla `password_reset_tokens` y envía el email.
- No revela si el correo existe para evitar fugas de datos.

```php
    public function showResetPassword(Request $request, string $token)
    {
        $email = $request->query('email');
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['correo' => 'Correo inválido.']);
        }
        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    }
```

- `showResetPassword()`: recibe el token por URL y el correo por query string.
- Devuelve la vista que permite ingresar la contraseña nueva.

```php
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'contrasena' => 'required|min:6|confirmed',
        ], [
            'contrasena.required' => 'La contraseña es obligatoria.',
            'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasena.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'El enlace de recuperación no es válido o ya expiró.']);
        }

        $expires = now()->subMinutes(config('auth.passwords.usuarios.expire'));
        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->lt($expires)) {
            return back()->withErrors(['email' => 'El enlace de recuperación no es válido o ya expiró.']);
        }

        $usuario = Usuario::where('correo', $request->email)->first();
        if (!$usuario) {
            return back()->withErrors(['email' => 'No encontramos un usuario con ese correo.']);
        }

        $usuario->contrasena = $request->contrasena;
        $usuario->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        return redirect()->route('login')->with('success', 'Contraseña actualizada. Ya puedes iniciar sesión.');
    }
```

- `resetPassword()`: valida token, correo y contraseña.
- Verifica token hasheado, expiración y existencia del usuario.
- Actualiza contraseña y borra el token usado.

```php
    public function registro(Request $request)
    {
        $validated = $request->validate([...]);
        Usuario::create([...]);
        RegistroActividad::log('Nuevo registro', ...);
        return redirect()->route('login')->with('success','Usuario registrado. Ya puedes iniciar sesión.');
    }
```

- `registro()`: crea un usuario cliente nuevo con los datos enviados.
- Define validaciones personalizadas y guarda el registro.

```php
    public function logout(Request $request)
    {
        $user = Auth::user();
        RegistroActividad::log('Cierre de sesión', ...);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
```

- `logout()`: cierra sesión y regenera token para seguridad.

```php
    private function redirigirPorRol()
    {
        $user = Auth::user();
        switch ($user->rol) {
            case 'administrador': return redirect()->route('admin.dashboard');
            case 'agente': return redirect()->route('agente.dashboard');
            case 'asistente': return redirect()->route('asistente.dashboard');
            case 'cliente': return redirect()->route('cliente.dashboard');
            default: return redirect()->route('login');
        }
    }
```

- `redirigirPorRol()`: envía al usuario a su dashboard según su rol.

---

## `app/Http/Controllers/PropiedadController.php`

```php
namespace App\Http\Controllers;

use App\Models\Propiedad;
use App\Models\Usuario;
use App\Models\RegistroActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
```

- Controlador que gestiona propiedades.
- Herramientas: autenticación, almacenamiento de imágenes y registros de actividad.

```php
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
```

- `index()`: muestra distinta vista si el usuario es administrador o agente.
- Admin ve todas las propiedades más la lista de agentes.
- Agente ve solo sus propias propiedades.

```php
    public function store(Request $request)
    {
        $request->validate([...]);
        $data = $request->only([...]);
        if (Auth::user()->esAgente()) $data['agente_id'] = Auth::id();
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('propiedades', 'public');
        }
        $data['latitud']  = $request->latitud  ?: null;
        $data['longitud'] = $request->longitud ?: null;
        $prop = Propiedad::create($data);
        RegistroActividad::log('Propiedad registrada', ...);
        return back()->with('success','Propiedad registrada correctamente.');
    }
```

- `store()`: valida datos y crea nueva propiedad.
- Si el usuario es agente, asigna su `id` como `agente_id`.
- Almacena imagen en disco público si se sube.

```php
    public function update(Request $request, $id)
    {
        $request->validate([...]);
        $propiedad = Propiedad::findOrFail($id);
        $datos = $request->except('imagen');
        if ($request->hasFile('imagen')) {
            if ($propiedad->imagen) {
                Storage::disk('public')->delete($propiedad->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('propiedades', 'public');
        }
        $propiedad->update($datos);
        RegistroActividad::log('Propiedad modificada', ...);
        return back()->with('success','Propiedad actualizada correctamente.');
    }
```

- `update()`: actualiza campos de propiedad y reemplaza imagen si es necesario.

```php
    public function destroy(Propiedad $propiedad)
    {
        $titulo = $propiedad->titulo;
        $propiedad->delete();
        RegistroActividad::log('Propiedad eliminada', ...);
        return back()->with('success','Propiedad eliminada correctamente.');
    }
```

- `destroy()`: elimina una propiedad y registra la acción.

```php
    public function disponibles(Request $request)
    {
        $tipos_validos = ['Venta','Alquiler','Anticretico'];
        $tipo   = $request->query('tipo');
        ...
        $query  = Propiedad::where('estado','Disponible');
        if ($filtro !== 'Todas') $query->where('tipo', $filtro);
        $propiedades = $query->orderBy('id','desc')->get();
        return view('cliente.propiedades', compact('propiedades','filtro'));
    }
```

- `disponibles()`: muestra propiedades disponibles al cliente.
- Permite filtrar por tipo de operación.

```php
    public function buscar(Request $request)
    {
        $q         = trim($request->query('q', ''));
        $tipo      = $request->query('tipo', 'Todas');
        $estado    = $request->query('estado', 'Disponible');
        $precioMax = $request->query('precio_max', '');
        $areaMin   = $request->query('area_min', '');
        ...
        $query = Propiedad::query();
        if ($estado !== 'Todas') $query->where('estado', $estado);
        if ($tipo   !== 'Todas') $query->where('tipo', $tipo);
        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('titulo', 'ilike', "%{$q}%")
                    ->orWhere('zona', 'ilike', "%{$q}%")
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
        return view('cliente.buscar', compact('propiedades','q','tipo','estado','precioMax','areaMin','totalDisponibles'));
    }
```

- `buscar()`: busca propiedades para cliente con filtros de texto, estado, tipo, precio y área.
- Usa consultas dinámicas y `ilike` para coincidencias parciales.

```php
    public function detalle(Propiedad $propiedad)
    {
        $propiedad->load('agente');
        return view('cliente.detalle', compact('propiedad'));
    }
```

- `detalle()`: carga la propiedad con su agente y muestra la vista de detalles.

- `buscarAdmin()`, `buscarAgente()`, `buscarAsistente()` siguen la misma lógica de búsqueda,
  pero ajustan el alcance de las propiedades según el rol:
  - Admin ve todo.
  - Agente ve solo sus propiedades.
  - Asistente ve todas las propiedades en modo lectura.

---

## `app/Http/Controllers/SolicitudController.php`

```php
namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\SolicitudVisita;
use App\Models\Propiedad;
use App\Models\RegistroActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
```

- Controlador para solicitudes de visita y calendario.

```php
    public function misSolicitudes()
    {
        $solicitudes = SolicitudVisita::with('propiedad')
            ->where('cliente_id', Auth::id())
            ->orderBy('id','desc')
            ->get();
        return view('cliente.solicitudes', compact('solicitudes'));
    }
```

- `misSolicitudes()`: muestra las solicitudes propias del cliente.

```php
    public function store(Request $request)
    {
        $request->validate([
            'propiedad_id'    => 'required|exists:propiedades,id',
            'fecha_solicitada'=> 'required|date|after_or_equal:today',
            'mensaje'         => 'required|min:5',
        ]);
        SolicitudVisita::create([...]);
        $prop = Propiedad::find($request->propiedad_id);
        RegistroActividad::log('Solicitud de visita enviada', ...);
        return redirect()->route('cliente.propiedades')->with('success','Solicitud enviada. Un agente te contactará pronto.');
    }
```

- `store()`: crea una nueva solicitud de visita para la propiedad seleccionada.
- Valida que la fecha no sea anterior a hoy.

```php
    public function visitasAgente(Request $request)
    {
        $filtro  = $request->query('estado','todas');
        $estados = ['Pendiente','Confirmada','Cancelada'];
        $query = SolicitudVisita::with(['propiedad','cliente'])
            ->whereHas('propiedad', fn($q) => $q->where('agente_id', Auth::id()));
        if (in_array($filtro, $estados)) $query->where('estado', $filtro);
        $solicitudes = $query->orderBy('fecha_solicitada')->get();
        return view('agente.visitas', compact('solicitudes','filtro'));
    }
```

- `visitasAgente()`: muestra las solicitudes relacionadas con las propiedades del agente.
- Agrega filtro por estado.

```php
    public function actualizarEstado(Request $request, SolicitudVisita $solicitud)
    {
        $request->validate(['estado' => 'required|in:Aceptada,Rechazada']);
        $solicitud->update(['estado' => $request->estado]);
        $msg = $request->estado === 'Aceptada' ? 'Visita confirmada.' : 'Visita cancelada.';
        return back()->with('success', $msg);
    }
```

- `actualizarEstado()`: altera el estado de la solicitud desde la vista del agente.

```php
    public function visitasAsistente()
    {
        $solicitudes = SolicitudVisita::with(['propiedad','cliente'])
            ->orderBy('fecha_solicitada')
            ->get();
        return view('asistente.visitas', compact('solicitudes'));
    }
```

- `visitasAsistente()`: asistente ve todas las visitas, sin limitar por agente.

```php
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
```

- `clientesAgente()`: agrupa clientes y muestra conteo de visitas para el agente.

```php
    public function cambiarEstado(Request $request, $id)
    {
        $solicitud = SolicitudVisita::findOrFail($id);
        $solicitud->estado = $request->estado;
        $solicitud->save();
        return back()->with('success', 'Estado actualizado correctamente');
    }
```

- `cambiarEstado()`: actualiza estado de la solicitud para asistente.

```php
    public function cancelar(Request $request, $id)
    {
        $solicitud = SolicitudVisita::where('id', $id)
            ->where('cliente_id', Auth::id())
            ->where('estado', 'Pendiente')
            ->firstOrFail();
        $solicitud->update(['estado' => 'Rechazada']);
        RegistroActividad::log('Visita cancelada', ...);
        return back()->with('success', 'Solicitud cancelada.');
    }
```

- `cancelar()`: permite al cliente cancelar su propia solicitud pendiente.

```php
    public function reagendar(Request $request, $id)
    {
        $request->validate([...]);
        $solicitud = SolicitudVisita::where('id', $id)
            ->where('cliente_id', Auth::id())
            ->where('estado', 'Pendiente')
            ->firstOrFail();
        $solicitud->update(['fecha_solicitada' => $request->fecha_solicitada]);
        RegistroActividad::log('Visita reagendada', ...);
        return back()->with('success', 'Visita reagendada correctamente.');
    }
```

- `reagendar()`: permite al cliente cambiar la fecha de una visita pendiente.

### Calendarios y eventos

```php
    public function calendarioAgente() { return view('agente.calendario'); }
    public function eventosAgente() { ... return response()->json($this->formatearEventos($visitas)); }
    public function calendarioAsistente() { return view('asistente.calendario'); }
    public function eventosAsistente() { ... }
    public function calendarioCliente() { return view('cliente.calendario'); }
    public function eventosCliente() { ... }
```

- Cada rol tiene su vista de calendario y endpoint JSON de eventos.

```php
    private function formatearEventos($visitas)
    {
        $colores = [...];
        return $visitas->map(function($v) use ($colores) {
            return [
                'id' => $v->id,
                'title' => $v->propiedad->titulo ?? 'Propiedad',
                'start' => $v->fecha_solicitada,
                'extendedProps' => [...],
            ];
        });
    }
```

- `formatearEventos()`: convierte las visitas en eventos compatibles con calendarios JS.
- Añade colores y propiedades extendidas.

---

## `app/Http/Controllers/UsuarioController.php`

```php
namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\RegistroActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
```

- Controlador para administrar usuarios y el perfil.

```php
    public function index()
    {
        $usuarios = Usuario::orderBy('nombre')->get();
        return view('admin.usuarios', compact('usuarios'));
    }
```

- `index()`: muestra la lista de usuarios en el panel de administrador.

```php
    public function store(Request $request)
    {
        $request->validate([...]);
        $user = Usuario::create($request->only(['nombre','correo','usuario','contrasena','rol']));
        RegistroActividad::log('Usuario creado', ...);
        return back()->with('success','Usuario agregado correctamente.');
    }
```

- `store()`: crea un usuario nuevo con el rol elegido.

```php
    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([...]);
        $data = $request->only(['nombre','correo','usuario','rol']);
        if ($request->filled('contrasena')) { $data['contrasena'] = $request->contrasena; }
        $usuario->update($data);
        RegistroActividad::log('Usuario editado', ...);
        return back()->with('success','Usuario actualizado correctamente.');
    }
```

- `update()`: edita un usuario existente.
- Solo actualiza `contrasena` si se envía un nuevo valor.

```php
    public function destroy(Usuario $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return back()->with('error','No puedes eliminar tu propia cuenta.');
        }
        $usuario->delete();
        RegistroActividad::log('Usuario eliminado', ...);
        return back()->with('success','Usuario eliminado correctamente.');
    }
```

- `destroy()`: borra un usuario salvo que sea la cuenta actual.

```php
    public function perfil()
    {
        $usuario = Auth::user();
        return view('compartido.perfil', compact('usuario'));
    }
```

- `perfil()`: muestra la página de perfil del usuario.

```php
    public function actualizarPerfil(Request $request)
    {
        $usuario = Auth::user();
        $request->validate([...]);
        $data = ['nombre' => $request->nombre, 'usuario' => $request->usuario];
        if ($request->filled('contrasena_nueva')) { $data['contrasena'] = $request->contrasena_nueva; }
        $usuario->update($data);
        return back()->with('success','Perfil actualizado correctamente.');
    }
```

- `actualizarPerfil()`: actualiza datos propios del usuario autenticado.

---

## `app/Http/Controllers/DashboardController.php`

```php
namespace App\Http\Controllers;

use App\Models\{Propiedad, Usuario, SolicitudVisita, RegistroActividad};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
```

- Controlador que prepara estadísticas para cada dashboard según el rol.

```php
    public function admin()
    {
        $totalProps    = Propiedad::count();
        $disponibles   = Propiedad::where('estado','Disponible')->count();
        $totalUsuarios = Usuario::count();
        $totalVentas   = Propiedad::where('estado','Vendido')->count();
        $ultimas       = Propiedad::with('agente')->orderBy('id','desc')->limit(5)->get();
        return view('admin.dashboard', compact('totalProps','disponibles','totalUsuarios','totalVentas','ultimas'));
    }
```

- `admin()`: muestra métricas generales del sistema.

```php
    public function agente()
    {
        $id          = Auth::id();
        $misProps    = Propiedad::where('agente_id',$id)->count();
        $disponibles = Propiedad::where('agente_id',$id)->where('estado','Disponible')->count();
        $vendidas    = Propiedad::where('agente_id',$id)->where('estado','Vendido')->count();
        $visitasPend = SolicitudVisita::where('estado', 'Pendiente')->whereHas('propiedad', fn($q) => $q->where('agente_id', auth()->id()))->count();
        $ultimas     = Propiedad::where('agente_id',$id)->orderBy('id','desc')->limit(5)->get();
        $visitas     = SolicitudVisita::with(['propiedad','cliente'])
                           ->where('estado', 'Pendiente')
                           ->whereHas('propiedad', fn($q) => $q->where('agente_id', auth()->id()))
                           ->orderBy('fecha_solicitada')->take(5)->get();
        return view('agente.dashboard', compact('misProps','disponibles','vendidas','visitasPend','ultimas','visitas'));
    }
```

- `agente()`: estadísticas para agente y las visitas pendientes de sus propiedades.

```php
    public function asistente(Request $request)
    {
        $totalClientes = Usuario::where('rol','cliente')->count();
        $visitasPend   = SolicitudVisita::where('estado','pendiente')->count();
        $visitasHoy    = SolicitudVisita::whereDate('fecha_solicitada', today())->count();
        $totalProps    = Propiedad::where('estado','Disponible')->count();
        $clientes = Usuario::where('rol','cliente')->orderBy('id','desc')->limit(5)->get();
        $visitas = SolicitudVisita::with(['propiedad','cliente'])
                        ->where('estado','pendiente')
                        ->orderBy('fecha_solicitada')
                        ->limit(5)
                        ->get();
        return view('asistente.dashboard', compact('totalClientes','visitasPend','visitasHoy','totalProps','clientes','visitas'));
    }
```

- `asistente()`: métricas de clientes y visitas para el asistente.

```php
    public function cliente()
    {
        $totalDisp     = Propiedad::where('estado','Disponible')->count();
        $totalVenta    = Propiedad::where('estado','Disponible')->where('tipo','Venta')->count();
        $totalAlquiler = Propiedad::where('estado','Disponible')->where('tipo','Alquiler')->count();
        $propiedades   = Propiedad::where('estado','Disponible')->orderBy('id','desc')->limit(8)->get();
        return view('cliente.dashboard', compact('totalDisp','totalVenta','totalAlquiler','propiedades'));
    }
```

- `cliente()`: muestra propiedades disponibles y métricas para el panel de cliente.

---

## Vistas de autenticación

### `resources/views/auth/login.blade.php`

- Página principal de login y registro en el mismo archivo.
- Usa dos paneles: `#login-panel` y `#register-panel`.
- Si existen errores, activa el panel de registro automáticamente.
- Incluye campos de correo, contraseña y botones para alternar paneles.
- Envía datos a `login.post` o `registro.post`.

### `resources/views/auth/registro.blade.php`

- Vista alternativa de registro e inicio de sesión.
- Contiene dos formularios separados: uno de login y otro de registro.
- Muestra errores de validación y mensajes de éxito.
- El registro envía a `registro.post`, el login a `login.post`.

### `resources/views/auth/forgot-password.blade.php`

- Formulario para solicitar el enlace de recuperación.
- Envía correo a `password.email`.
- Muestra éxito si se envió o si el correo existe.

### `resources/views/auth/reset-password.blade.php`

- Formulario para establecer la nueva contraseña.
- Recibe `token` y `email` como valores ocultos.
- Envía a `password.update`.
- Contiene campos `contrasena` y `contrasena_confirmation`.

---

## Nota final

Este documento está enfocado en los archivos clave de la lógica de negocio y de autenticación.
Si deseas, puedo extenderlo con más archivos como:
- controladores de reportes,
- modelos (`Usuario`, `Propiedad`, `SolicitudVisita`),
- vistas de paneles (`admin`, `agente`, `asistente`, `cliente`).
