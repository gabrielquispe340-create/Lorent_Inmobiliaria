<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Lorent Inmobiliaria')</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    @if(in_array(auth()->user()->rol, ['administrador','agente','asistente']))
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('css/dashboard_usuario.css') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/compartido/topbar.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
    <style> #sidebar-mobile { position: fixed !important; top: 0; left: 0; bottom: 0; width: 260px; background: #0f172a; z-index: 9999; transform: translateX(-100%); transition: transform 0.3s ease; } #sidebar-mobile.active { transform: translateX(0); } @media (min-width: 768px) { #sidebar-mobile { position: static !important; transform: none !important; } } </style>
</head>
<body>


<div class="layout">
    @include('compartido.sidebar')

    <div class="main" style="margin-left: 0 !important;">
        <div class="topbar z-40 relative">
            {{-- Botón hamburguesa visible solo en móvil --}}
            <button type="button" id="btn-menu-trigger" style="cursor: pointer; padding: 10px; z-index: 10000;" class="md:hidden">☰</button>
            <span class="topbar-title">@yield('titulo_pagina', 'Panel')</span>
            <div class="user-info" id="userDropdownWrapper">
                <div class="user-avatar" id="userAvatarBtn" onclick="toggleUserMenu()" title="Opciones de cuenta">
                    {{ strtoupper(substr(auth()->user()->nombre, 0, 2)) }}
                </div>
                <div class="user-details" id="userAvatarBtn" onclick="toggleUserMenu()">
                    <span class="user-name">{{ auth()->user()->nombre }}</span>
                    <span class="user-role">{{ ucfirst(auth()->user()->rol) }}</span>
                </div>
                <svg class="user-chevron" onclick="toggleUserMenu()" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>

                {{-- DROPDOWN --}}
                <div class="user-dropdown" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="dropdown-avatar">{{ strtoupper(substr(auth()->user()->nombre, 0, 2)) }}</div>
                        <div>
                            <div class="dropdown-name">{{ auth()->user()->nombre }}</div>
                            <div class="dropdown-email">{{ auth()->user()->correo }}</div>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('perfil') }}" class="dropdown-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Mi perfil
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item dropdown-logout">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Mensajes flash --}}
        @if(session('success'))
        <div style="margin:16px 24px 0;background:#e8f5e9;border:1px solid #4caf50;color:#2e7d32;padding:10px 16px;border-radius:8px;font-size:13px">
            ✓ {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div style="margin:16px 24px 0;background:#ffebee;border:1px solid #ef9a9a;color:#c62828;padding:10px 16px;border-radius:8px;font-size:13px">
            ✗ {{ session('error') }}
        </div>
        @endif

        <div class="content">
            @yield('contenido')
        </div>
    </div>
</div>



<script src="{{ asset('js/compartido/topbar.js') }}"></script>
@stack('scripts')

{{-- MODAL ELIMINAR GLOBAL --}}
<div class="delete-modal-overlay fixed hidden inset-0 z-[60]" id="deleteModal" style="background: rgba(15,23,42,0.4); backdrop-filter: blur(4px);">

    <div class="delete-modal w-[95%] max-w-md mx-auto">

        <button class="delete-close" id="closeDeleteModal">
            ×
        </button>

        <div class="delete-icon">
            🗑️
        </div>

        <h2 class="delete-title">
            Eliminar registro
        </h2>

        <p class="delete-text" id="deleteText">
            ¿Seguro que deseas eliminar este elemento?
        </p>

        <div class="delete-actions flex flex-col sm:flex-row gap-2">

            <button type="button"
                    class="btn-cancel-delete"
                    id="cancelDeleteBtn">
                Cancelar
            </button>

            <button type="button"
                    class="btn-confirm-delete"
                    id="confirmDeleteBtn">
                Sí, eliminar
            </button>

        </div>

    </div>

</div>



<script>

const deleteModal = document.getElementById('deleteModal');
const closeDeleteModal = document.getElementById('closeDeleteModal');
const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
const deleteText = document.getElementById('deleteText');

let currentDeleteForm = null;

// ABRIR MODAL

document.querySelectorAll('.open-delete-modal').forEach(button => {

    button.addEventListener('click', () => {

        currentDeleteForm = button.closest('.delete-form, .form-eliminar');

        const name = button.dataset.name || button.closest('form')?.dataset?.title || 'este registro';

        deleteText.textContent =
            `¿Seguro que deseas eliminar "${name}"? Esta acción no se puede deshacer.`;

        deleteModal.classList.remove('hidden');
        deleteModal.classList.add('flex');
        deleteModal.classList.add('open');

    });

});

// CERRAR MODAL
function cerrarModalEliminar() {

    deleteModal.classList.remove('open');
    deleteModal.classList.remove('flex');
    deleteModal.classList.add('hidden');

}

// CLICK FUERA
deleteModal.addEventListener('click', e => {

    if(e.target === deleteModal) {

        cerrarModalEliminar();

    }

});

// CLICK CONFIRMAR
confirmDeleteBtn.addEventListener('click', () => {

    if(currentDeleteForm) {

        currentDeleteForm.submit();

    }

});

// BOTÓN X
document.querySelector('.delete-close')
?.addEventListener('click', () => {

    cerrarModalEliminar();

});


// BOTÓN CANCELAR
document.querySelector('.btn-cancel-delete')
?.addEventListener('click', () => {

    cerrarModalEliminar();

});
</script>

<script>
window.onload = function() {
    var btn  = document.getElementById('btn-menu-trigger');
    var menu = document.getElementById('sidebar-mobile');
    if (btn && menu) {
        btn.onclick = function() {
            if (menu.style.transform === 'translateX(0%)') {
                menu.style.transform = 'translateX(-100%)';
            } else {
                menu.style.transform = 'translateX(0%)';
            }
        };

        // ← AGREGA ESTO: cerrar al tocar fuera del sidebar
        document.addEventListener('click', function(e) {
            if (window.innerWidth < 768 &&
                menu.style.transform === 'translateX(0%)' &&
                !menu.contains(e.target) &&
                e.target !== btn) {
                menu.style.transform = 'translateX(-100%)';
            }
        });
    }
};
</script>
</body>
</html>