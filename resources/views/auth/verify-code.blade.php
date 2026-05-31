{{-- resources/views/auth/verify-code.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar código | Lorent Inmobiliaria</title>
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
                Verifica tu código
                de recuperación
            </h1>
            <p>
                Ingresa el código que te enviamos por correo para continuar.
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
                <h2>Verificar código</h2>
                <p class="subtitle">
                    Revisa tu bandeja de Mailtrap y copia el código enviado.
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
                <form action="{{ route('password.verify.post') }}" method="POST">
                    @csrf
                    <input type="hidden" name="email" value="{{ old('email', $email) }}">
                    <div class="input-group">
                        <label>Correo electrónico</label>
                        <input type="email" value="{{ old('email', $email) }}" disabled>
                    </div>
                    <div class="input-group">
                        <label>Código de verificación</label>
                        <input
                            type="text"
                            name="codigo"
                            value="{{ old('codigo') }}"
                            placeholder="123456"
                            maxlength="6"
                            required
                        >
                    </div>
                    <button type="submit" class="btn-primary">
                        Validar código
                    </button>
                </form>
                <div class="forgot-password">
                    <a href="{{ route('password.request') }}">Volver a solicitar código</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
