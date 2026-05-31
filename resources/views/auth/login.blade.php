{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Lorent Inmobiliaria</title>

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">

    {{-- FUENTE --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<div class="auth-container">

    {{-- PANEL IZQUIERDO --}}
    <div class="auth-left hidden md:flex">

        <div class="overlay"></div>

        <div class="left-content">

            <div class="badge-status">
                <span class="dot"></span>
                Plataforma activa
            </div>

            <h1>
                Gestiona tus <span>propiedades</span>
                de forma inteligente
            </h1>

            <p>
                Sistema inmobiliario moderno para clientes,
                agentes, administradores y seguimiento CRM.
            </p>

        </div>

    </div>

    {{-- PANEL DERECHO --}}
    <div class="auth-right">

        <div class="auth-card w-full max-w-md mx-auto p-4">

            {{-- LOGO --}}
            <div class="logo-area">

                <div class="logo-icon">
                    🏠
                </div>

                <div class="logo-text">
                    Lorent<span>Inmobiliaria</span>
                </div>

            </div>

            {{-- TABS --}}
            <div class="tabs">

                <button class="tab active" onclick="showPanel('login', this)">
                    Iniciar sesión
                </button>

                <button class="tab" onclick="showPanel('register', this)">
                    Registrarse
                </button>

            </div>

            {{-- LOGIN --}}
            <div id="login-panel" class="panel active">

                <h2>Bienvenido 👋</h2>
                <p class="subtitle">
                    Inicia sesión para continuar
                </p>

                @if($errors->has('correo') || $errors->has('contrasena'))
                    <div class="alert error">
                        {{ $errors->first('correo') ?? $errors->first('contrasena') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">

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

                    <div class="input-group">

                        <label>Contraseña</label>

                        <div class="password-wrapper">

                            <input
                                type="password"
                                name="contrasena"
                                id="loginPassword"
                                placeholder="********"
                                required
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                onclick="togglePassword('loginPassword')"
                            >
                                👁
                            </button>

                        </div>

                    </div>

                    <div class="forgot-password">
                        <a href="{{ route('password.request') }}">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>

                    <button type="submit" class="btn-primary">
                        Iniciar sesión
                    </button>

                </form>

            </div>

            {{-- REGISTRO --}}
            <div id="register-panel" class="panel">

                <h2>Crear cuenta 🚀</h2>

                <p class="subtitle">
                    Completa tus datos para registrarte
                </p>

                @if($errors->any())
                    <div class="alert error" style="color: #e53935;">
                        <ul style="margin:0;padding-left:18px;font-size:14px">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('registro.post') }}" method="POST">

                    @csrf

                    <div class="input-group">

                        <label>Nombre completo</label>

                        <input
                            type="text"
                            name="nombre"
                            placeholder="Ingresa tu nombre"
                            required
                        >

                    </div>

                    <div class="input-group">

                        <label>Correo electrónico</label>

                        <input
                            type="email"
                            name="correo"
                            placeholder="correo@ejemplo.com"
                            required
                        >

                    </div>

                    <div class="input-group">

                        <label>Usuario</label>

                        <input
                            type="text"
                            name="usuario"
                            placeholder="Nombre de usuario"
                            required
                        >

                    </div>

                    <div class="input-group">

                        <label>Contraseña</label>

                        <div class="password-wrapper">

                            <input
                                type="password"
                                name="contrasena"
                                id="registerPassword"
                                placeholder="********"
                                required
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                onclick="togglePassword('registerPassword')"
                            >
                                👁
                            </button>

                        </div>

                    </div>

                    <button type="submit" class="btn-primary">
                        Crear cuenta
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

{{-- JS --}}
<script src="{{ asset('js/auth/login.js') }}"></script>

<script>
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            const registerPanel = document.getElementById('register-panel');
            const loginPanel = document.getElementById('login-panel');
            const tabs = document.querySelectorAll('.tab');
            
            if (registerPanel) {
                registerPanel.classList.add('active');
            }
            if (loginPanel) {
                loginPanel.classList.remove('active');
            }
            if (tabs.length >= 2) {
                tabs[0].classList.remove('active');
                tabs[1].classList.add('active');
            }
        });
    @endif
</script>

</body>
</html>