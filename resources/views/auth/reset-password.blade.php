{{-- resources/views/auth/reset-password.blade.php --}}
{{--
    Vista para restablecer la contraseña del usuario.
    Se muestra después de que el usuario hace clic en el enlace enviado por correo.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña | Lorent Inmobiliaria</title>
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<div class="auth-container">
    <div class="auth-left hidden md:flex">
        <div class="overlay"></div>
        <div class="left-content">
            <div class="badge-status">
                <span class="dot"></span>
                Plataforma activa
            </div>
            <h1>
                Cambia tu contraseña
                con seguridad
            </h1>
            <p>
                Ingresa una nueva contraseña para tu cuenta.
            </p>
        </div>
    </div>
    <div class="auth-right">
        <div class="auth-card w-full max-w-md mx-auto p-4">
            <div class="logo-area">
                <div class="logo-icon">🏠</div>
                <div class="logo-text">Lorent<span>Inmobiliaria</span></div>
            </div>
            <div class="panel active">
                <h2>Restablecer contraseña</h2>
                <p class="subtitle">
                    Define una nueva contraseña para tu cuenta.
                </p>
                @if($errors->any())
                    <div class="alert error">
                        {{ $errors->first() }}
                    </div>
                @endif
                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    {{-- El token y el correo se envían ocultos para validar el restablecimiento. --}}
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">
                    <div class="input-group">
                        <label>Nueva contraseña</label>
                        <input
                            type="password"
                            name="contrasena"
                            placeholder="********"
                            required
                        >                                   
                    </div>                    
                    <div class="input-group">
                        <label>Confirmar contraseña</label>
                        <input
                            type="password"
                            name="contrasena_confirmation"
                            placeholder="********"
                            required
                        >                                                                                              
                    </div>
                    <button type="submit" class="btn-primary">
                        Guardar contraseña
                    </button>
                </form>
                <div class="forgot-password">
                    <a href="{{ route('login') }}">Volver al inicio de sesión</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
