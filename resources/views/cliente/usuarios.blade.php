{{-- resources/views/admin/usuarios.blade.php --}}
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
        <thead><tr><th>#</th><th>Nombre</th><th>Correo</th><th>Usuario</th><th>Rol</th><th>Descripción</th><th>Acciones</th></tr></thead>
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
            <td>{{ $u->descripcion }}</td>
            <td>
                <div class="action-btns">
                    <button
    type="button"
    class="btn-edit btn-editar-usuario"

    data-id="{{ $u->id }}"
    data-nombre="{{ $u->nombre }}"
    data-correo="{{ $u->correo }}"
    data-usuario="{{ $u->usuario }}"
    data-rol="{{ $u->rol }}"
    data-descripcion="{{ $u->descripcion }}"
>
    Editar
</button>
                    <form method="POST"
      action="{{ route('admin.propiedades.destroy', $p) }}"
      class="form-eliminar"
      data-title="{{ $p->titulo }}">

    @csrf
    @method('DELETE')

    <button type="button"
            class="btn-delete open-delete-modal"
            data-name="{{ $p->titulo }}">
        Eliminar
    </button>
</form>
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-delete">Eliminar</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;color:#6c757d;padding:20px">No hay usuarios.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

</div>

<div class="modal-overlay" id="modalOverlay">
<div class="modal w-[95%] max-w-lg mx-auto sm:w-full">
    <h2 id="modalTitulo">Agregar usuario</h2>
    <form id="formUsuario" method="POST" action="{{ route('admin.usuarios.store') }}">
        @csrf <span id="methodField"></span>
        <div class="form-grid">
            <div class="form-group full"><label>Nombre completo</label><input type="text" name="nombre" id="userName" required></div>
            <div class="form-group"><label>Correo</label><input type="email" name="correo" id="userCorreo" required></div>
            <div class="form-group"><label>Usuario</label><input type="text" name="usuario" id="userUsuario" required></div>
            <div class="form-group"><label>Contraseña <span id="passNote" style="font-weight:400;color:#6c757d">(requerida)</span></label><input type="password" name="contrasena" id="userPass"></div>
            <div class="form-group"><label>Rol</label>
                <select name="rol" id="userRol">
                    <option value="agente">Agente</option>
                    <option value="administrador">Administrador</option>
                    <option value="asistente">Asistente</option>
                    <option value="cliente">Cliente</option>
                </select>
            </div>
            <div class="form-group full" id="descripcionGroup" style="display:none"><label>Descripción</label>
                <textarea name="descripcion" id="userDescripcion" rows="3"></textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
            <button type="submit" class="btn-primary" id="btnSubmit">Agregar</button>
        </div>
    </form>
</div>
</div>
@endsection

@push('scripts')
<script>
const overlay = document.getElementById('modalOverlay');
const form    = document.getElementById('formUsuario');
function abrirModal(){
    form.reset(); form.action='{{ route("admin.usuarios.store") }}';
    document.getElementById('methodField').innerHTML='';
    document.getElementById('modalTitulo').textContent='Agregar usuario';
    document.getElementById('btnSubmit').textContent='Agregar';
    document.getElementById('passNote').textContent='(requerida)';
    document.getElementById('userDescripcion').value='';
    document.getElementById('descripcionGroup').style.display = document.getElementById('userRol').value === 'cliente' ? 'block' : 'none';
    overlay.classList.add('open');
}
function cerrarModal(){ overlay.classList.remove('open'); }
overlay.addEventListener('click',e=>{ if(e.target===overlay) cerrarModal(); });
function editarUsuario(id,nombre,correo,usuario,rol){
    form.action=`/admin/usuarios/${id}`;
    document.getElementById('methodField').innerHTML='<input type="hidden" name="_method" value="PUT">';
    document.getElementById('userName').value=nombre;
    document.getElementById('userCorreo').value=correo;
    document.getElementById('userUsuario').value=usuario;
    document.getElementById('userRol').value=rol;
    document.getElementById('userPass').value='';
    document.getElementById('passNote').textContent='(dejar vacío para no cambiar)';
    document.getElementById('userDescripcion').value = document.querySelector(`[data-id="${id}"]`)?.dataset?.descripcion ?? '';
    document.getElementById('descripcionGroup').style.display = rol === 'cliente' ? 'block' : 'none';
    document.getElementById('modalTitulo').textContent='Editar usuario';
    document.getElementById('btnSubmit').textContent='Guardar cambios';
    overlay.classList.add('open');
}
document.getElementById('userRol').addEventListener('change', function(){
    document.getElementById('descripcionGroup').style.display = this.value === 'cliente' ? 'block' : 'none';
});
</script>
@endpush
