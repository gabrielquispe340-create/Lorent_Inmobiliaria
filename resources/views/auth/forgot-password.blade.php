{{-- resources/views/auth/forgot-password.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña | Lorent Inmobiliaria</title>
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
                Recupera tu acceso
                de forma rápida
            </h1>
            <p>
                Te enviaremos un enlace para restablecer tu contraseña.
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
                <h2>Recuperar contraseña</h2>
                <p class="subtitle">
                    Ingresa tu correo y te enviaremos un código de verificación.
                </p>
                @if($errors->any())
                    <div class="alert error">
                        {{ $errors->first() }}
                    </div>
                @endif
                @if(session('success'))
                    <div class="alert success">
                        {{ session('success') }}
                    </div>
                @endif
                <form action="{{ route('password.email') }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <label>Correo electrónico</label>
                        <input
                            type="email"
                            name="correo"
                            placeholder="correo@ejemplo.com"
                            required
                        >
                    </div>
                    <button type="submit" class="btn-primary">
                        Enviar código
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
