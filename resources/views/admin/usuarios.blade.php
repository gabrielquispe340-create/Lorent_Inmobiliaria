@extends('layouts.panel')
@section('titulo','Usuarios')
@section('titulo_pagina','Gestión de usuarios')

@section('contenido')
<div class="card">
    <div class="card-header">
        <span class="card-title">Lista de usuarios</span>
        <button class="btn-primary" onclick="abrirModal()">+ Agregar usuario</button>
    </div>
<div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
        <thead><tr><th>#</th><th>Nombre</th><th>Correo</th><th>Usuario</th><th>Rol</th><th>Acciones</th></tr></thead>
        <tbody>
        @forelse($usuarios as $u)
        @php
            $rolClass = match($u->rol) {
                'administrador' => 'badge-blue',
                'asistente'     => 'badge-purple',
                'agente'        => 'badge-green',
                default         => 'badge-amber',
            };
        @endphp
        <tr>
            <td>{{ $u->id }}</td>
            <td>{{ $u->nombre }}</td>
            <td>{{ $u->correo }}</td>
            <td>{{ $u->usuario }}</td>
            <td><span class="badge {{ $rolClass }}">{{ ucfirst($u->rol) }}</span></td>
            <td>
                <div class="action-btns flex flex-col sm:flex-row gap-2">
                    <button
                        type="button"
                        class="btn-edit btn-editar-usuario"

                        data-id="{{ $u->id }}"
                        data-nombre="{{ $u->nombre }}"
                        data-email="{{ $u->email }}"
                        data-rol="{{ $u->rol }}">

                        Editar

                    </button>
                    <form method="POST"
                        action="{{ route('admin.usuarios.destroy', $u) }}"
                        class="form-eliminar"
                        data-title="{{ $u->nombre }}"
                        style="display:inline">

                        @csrf
                        @method('DELETE')

                        <button type="button" class="btn-delete open-delete-modal-user">
                            Eliminar
                        </button>

                    </form>
                    
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:#6c757d;padding:20px">No hay usuarios.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

</div>

{{-- MODAL AGREGAR --}}
<div class="modal-overlay" id="modalOverlay">
<div class="modal w-[95%] max-w-lg mx-auto sm:w-full">
    <h2 id="modalTitulo">Agregar usuario</h2>

    {{-- Formulario AGREGAR --}}
    <form id="formAgregar" method="POST" action="{{ route('admin.usuarios.store') }}">
        @csrf
        <div class="form-grid">
            <div class="form-group full">
                <label>Nombre completo</label>
                <input type="text" name="nombre" id="aNombre" required>
            </div>
            <div class="form-group">
                <label>Correo</label>
                <input type="email" name="correo" id="aCorreo" required>
            </div>
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="usuario" id="aUsuario" required>
            </div>
            <div class="form-group">
                <label>Contraseña <span style="font-weight:400;color:#6c757d">(requerida)</span></label>
                <div style="position:relative">
                    <input type="password" name="contrasena" id="aPass" required
                           style="padding-right:40px;width:100%;padding:9px 40px 9px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;outline:none;background:#f8f9fa;box-sizing:border-box">
                    <button type="button" onclick="togglePass('aPass','eyeA')"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#6c757d;font-size:16px;padding:0">
                        <span id="eyeA">👁</span>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label>Rol</label>
                <select name="rol" id="aRol">
                    <option value="agente">Agente</option>
                    <option value="administrador">Administrador</option>
                    <option value="asistente">Asistente</option>
                    <option value="cliente">Cliente</option>
                </select>
            </div>
        </div>
        <div class="form-actions flex flex-col sm:flex-row gap-2 mt-4">
            <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
            <button type="submit" class="btn-primary">Agregar</button>
        </div>
    </form>

    {{-- Formulario EDITAR --}}
    <form id="formEditar" method="POST" style="display:none">
        @csrf @method('PUT')
        <div class="form-grid">
            <div class="form-group full">
                <label>Nombre completo</label>
                <input type="text" name="nombre" id="eNombre" required>
            </div>
            <div class="form-group">
                <label>Correo</label>
                <input type="email" name="correo" id="eCorreo" required>
            </div>
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="usuario" id="eUsuario" required>
            </div>
            <div class="form-group">
                <label>Contraseña <span style="font-weight:400;color:#6c757d">(Opcional)</span></label>
                <div style="position:relative">
                    <input type="password" name="contrasena" id="ePass"
                           style="padding-right:40px;width:100%;padding:9px 40px 9px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;outline:none;background:#f8f9fa;box-sizing:border-box">
                    <button type="button" onclick="togglePass('ePass','eyeE')"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#6c757d;font-size:16px;padding:0">
                        <span id="eyeE">👁</span>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label>Rol</label>
                <select name="rol" id="eRol">
                    <option value="agente">Agente</option>
                    <option value="administrador">Administrador</option>
                    <option value="asistente">Asistente</option>
                    <option value="cliente">Cliente</option>
                </select>
            </div>
        </div>
        <div class="form-actions flex flex-col sm:flex-row gap-2 mt-4">
            <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
            <button type="submit" class="btn-primary">Guardar cambios</button>
        </div>
    </form>

</div>
</div>

{{-- MODAL CONFIRMAR ELIMINACIÓN USUARIO --}}
<div id="deleteUserOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:36px 32px;max-width:420px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
        <div style="width:72px;height:72px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <span style="font-size:36px;">👤</span>
        </div>
        <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:10px;">Eliminar usuario</h2>
        <p id="deleteUserMsg" style="font-size:14px;color:#64748b;margin-bottom:28px;line-height:1.6;"></p>
        <div style="display:flex;gap:12px;justify-content:center;">
            <button onclick="cerrarDeleteUserModal()" style="padding:10px 24px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;font-size:14px;font-weight:500;cursor:pointer;">Cancelar</button>
            <button id="btnConfirmarEliminarUser" style="padding:10px 24px;border:none;border-radius:8px;background:#dc2626;color:#fff;font-size:14px;font-weight:600;cursor:pointer;">Sí, eliminar</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const overlay     = document.getElementById('modalOverlay');
const formAgregar = document.getElementById('formAgregar');
const formEditar  = document.getElementById('formEditar');

// Toggle ver/ocultar contraseña — funciona siempre
function togglePass(inputId, eyeId) {
    const input = document.getElementById(inputId);
    const eye   = document.getElementById(eyeId);
    if (input.type === 'password') {
        input.type  = 'text';
        eye.textContent = '🙈';
    } else {
        input.type  = 'password';
        eye.textContent = '👁';
    }
}

function abrirModal() {
    formAgregar.reset();
    // Asegurarse de que el campo contraseña vuelve a tipo password
    document.getElementById('aPass').type = 'password';
    document.getElementById('eyeA').textContent = '👁';
    formAgregar.style.display = 'block';
    formEditar.style.display  = 'none';
    document.getElementById('modalTitulo').textContent = 'Agregar usuario';
    overlay.classList.add('open');
}

function cerrarModal() {
    overlay.classList.remove('open');
    formAgregar.reset();
    formEditar.reset();
    // Restablecer tipos de contraseña al cerrar
    document.getElementById('aPass').type = 'password';
    document.getElementById('ePass').type = 'password';
    document.getElementById('eyeA').textContent = '👁';
    document.getElementById('eyeE').textContent = '👁';
    formAgregar.style.display = 'block';
    formEditar.style.display  = 'none';
}

overlay.addEventListener('click', e => {
    if (e.target === overlay) cerrarModal();
});

function editarUsuario(id, nombre, correo, usuario, rol) {
    formEditar.action = `/admin/usuarios/${id}`;
    document.getElementById('eNombre').value  = nombre;
    document.getElementById('eCorreo').value  = correo;
    document.getElementById('eUsuario').value = usuario;
    document.getElementById('eRol').value     = rol;
    document.getElementById('ePass').value    = '';
    document.getElementById('ePass').type     = 'password';
    document.getElementById('eyeE').textContent = '👁';
    formAgregar.style.display = 'none';
    formEditar.style.display  = 'block';
    document.getElementById('modalTitulo').textContent = 'Editar usuario';
    overlay.classList.add('open');
}

document.querySelectorAll('.btn-editar-usuario')
.forEach(button => {

    button.addEventListener('click', function () {

        editarUsuario(

            this.dataset.id,
            this.dataset.nombre,
            this.dataset.email,
            this.dataset.rol

        );

    });

});

// ── ELIMINAR USUARIO ──────────────────────────────────
let pendingUserForm = null;

document.querySelectorAll('.open-delete-modal-user').forEach(btn => {
    btn.addEventListener('click', function() {
        const form  = this.closest('.form-eliminar');
        const title = form.dataset.title || 'este usuario';
        pendingUserForm = form;
        document.getElementById('deleteUserMsg').textContent =
            `¿Seguro que deseas eliminar al usuario "${title}"? Esta acción no se puede deshacer.`;
        document.getElementById('deleteUserOverlay').style.display = 'flex';
    });
});

function cerrarDeleteUserModal() {
    document.getElementById('deleteUserOverlay').style.display = 'none';
    pendingUserForm = null;
}

document.getElementById('btnConfirmarEliminarUser').addEventListener('click', function() {
    if (pendingUserForm) pendingUserForm.submit();
});

document.getElementById('deleteUserOverlay').addEventListener('click', function(e) {
    if (e.target === this) cerrarDeleteUserModal();
});

</script>
@endpush
