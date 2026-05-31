<?php
// este archivo se encarga de salir,entrar,registrarse y recuperar contraseña de los usuarios del sistema
// app/Http/Controllers/AuthController.php
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

/**
 * Controlador de autenticación.
 *
 * Este controlador maneja el inicio de sesión, registro, recuperación de contraseña
 * y cierre de sesión de los usuarios del sistema.
 */


class AuthController extends Controller
{
    // Mostrar login, cierra cualquier sesión activa para evitar conflictos y muestra el formulario de inicio de sesión.
    public function showLogin()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return view('auth.login');
    }

    // Procesar login, valida las credenciales, inicia sesión y redirige según el rol del usuario. También registra la actividad de inicio de sesión.
    public function login(Request $request)
    {
        // Validar que el correo y la contraseña se envíen al servidor.
        $request->validate([
            'correo'    => 'required|email',
            'contrasena'=> 'required',
        ], [
            'correo.required'     => 'El correo es obligatorio.',
            'correo.email'        => 'Ingresa un correo válido.',
            'contrasena.required' => 'La contraseña es obligatoria.',
        ]);

        // Buscar el usuario registrado por correo y contraseña.
        // En esta implementación, la contraseña se compara directamente en texto plano.
        $usuario = Usuario::where('correo', $request->correo)
            ->where('contrasena', $request->contrasena)
            ->first();

        if ($usuario) {
            Auth::loginUsingId($usuario->id);
            $request->session()->regenerate();

            try {
                RegistroActividad::log(
                    'Inicio de sesión',
                    "El usuario {$usuario->nombre} ({$usuario->rol}) inició sesión."
                );
            } catch (\Exception $e) {
                // Ignorar errores en el registro de actividad para no interrumpir el inicio de sesión.
            }

            return $this->redirigirPorRol();
        }

        try {
            RegistroActividad::log(
                'Intento de sesión fallido',
                "Intento fallido con correo: {$request->correo}"
            );
        } catch (\Exception $e) {
            // Ignorar errores en el registro de actividad.
        }

        return back()->withErrors(['correo' => 'Correo o contraseña incorrectos.']);
    }
    // Mostrar registro, muestra el formulario para que un nuevo usuario se registre en el sistema.
    public function showRegistro()
    {
        return view('auth.registro');
    }

    // Mostrar formulario de recuperación de contraseña
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function showVerifyCode(Request $request)
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('password.request')->withErrors(['correo' => 'Correo inválido.']);
        }

        return view('auth.verify-code', [
            'email' => $email,
        ]);
    }

    // Enviar código de recuperación
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
            $codigo = random_int(100000, 999999);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $usuario->correo],
                [
                    'token' => Hash::make($codigo),
                    'created_at' => now(),
                ]
            );

            Mail::to($usuario->correo)->send(new PasswordResetMail((string) $codigo, $usuario->nombre));
        }

        return redirect()->route('password.verify', ['email' => $request->correo])
            ->with('success', 'Si ese correo existe, te enviamos un código de verificación.');
    }

    // Mostrar formulario para crear nueva contraseña
    public function showResetPassword(Request $request, string $token)
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('password.request')->withErrors(['correo' => 'Correo inválido.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'codigo' => 'required|digits:6',
        ], [
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Ingresa un correo válido.',
            'codigo.required' => 'El código es obligatorio.',
            'codigo.digits' => 'El código debe tener 6 dígitos.',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->codigo, $record->token)) {
            return back()->withErrors(['codigo' => 'Código inválido o expirado.'])->withInput();
        }

        $expires = now()->subMinutes(config('auth.passwords.usuarios.expire'));
        $createdAt = Carbon::parse($record->created_at);

        if ($createdAt->lt($expires)) {
            return back()->withErrors(['codigo' => 'Código inválido o expirado.'])->withInput();
        }

        return redirect()->route('password.reset', [
            'token' => $request->codigo,
            'email' => $request->email,
        ]);
    }

    // Guardar nueva contraseña
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'contrasena' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'confirmed',
            ],
        ], [
            'contrasena.required' => 'La contraseña es obligatoria.',
            'contrasena.string' => 'La contraseña debe ser un texto válido.',
            'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasena.regex' => 'La contraseña debe contener mayúscula, minúscula y número.',
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

    public function registro(Request $request)
    {
        $validated = $request->validate(
            [
                'nombre'    => 'required|min:3',
                'correo'    => 'required|email|unique:usuarios,correo',
                'usuario'   => 'required|min:3|unique:usuarios,usuario|regex:/^\S+$/',
                'contrasena'=> [
                    'required',
                    'string',
                    'min:8',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                ],
            ],
            [
                'nombre.required'      => 'El nombre es obligatorio.',
                'nombre.min'           => 'El nombre debe tener al menos 3 caracteres.',
                'correo.required'      => 'El correo es obligatorio.',
                'correo.email'         => 'Ingresa un correo válido.',
                'correo.unique'        => 'Este correo ya está registrado.',
                'usuario.required'     => 'El usuario es obligatorio.',
                'usuario.min'          => 'El usuario debe tener al menos 3 caracteres.',
                'usuario.unique'       => 'Este nombre de usuario ya existe.',
                'usuario.regex'        => 'El usuario no puede contener espacios.',
                'contrasena.required'  => 'La contraseña es obligatoria.',
                'contrasena.min'       => 'La contraseña debe tener al menos 6 caracteres.',
                'contrasena.regex'     => 'La contraseña debe contener mayúscula, minúscula y número.',
                'contrasena.string'    => 'La contraseña debe ser un texto válido.',
            ]
        );

        Usuario::create([
            'nombre'     => $request->nombre,
            'correo'     => $request->correo,
            'usuario'    => $request->usuario,
            'contrasena' => $request->contrasena, // Se hashea automáticamente por el cast
            'rol'        => 'cliente',
        ]);

        RegistroActividad::log(
            'Nuevo registro',
            "Se registró el usuario: {$request->usuario} ({$request->correo})"
        );

        return redirect()->route('login')->with('success', 'Usuario registrado. Ya puedes iniciar sesión.');
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        $user = Auth::user();
        RegistroActividad::log(
            'Cierre de sesión',
            "El usuario {$user->nombre} ({$user->rol}) cerró sesión."
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // Redirigir según rol del usuario
    private function redirigirPorRol()
    {
        $user = Auth::user();

        switch ($user->rol) {
            case 'administrador':
                return redirect()->route('admin.dashboard');
            case 'agente':
                return redirect()->route('agente.dashboard');
            case 'asistente':
                return redirect()->route('asistente.dashboard');
            case 'cliente':
                return redirect()->route('cliente.dashboard');
            default:
                return redirect()->route('login');
        }
    }
}
