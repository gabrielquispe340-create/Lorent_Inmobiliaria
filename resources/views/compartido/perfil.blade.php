@extends('layouts.panel')
@section('titulo','Mi perfil')
@section('titulo_pagina','Mi perfil')

@section('contenido')
<div class="card" style="max-width:520px">
    <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px">
        <div class="user-avatar" style="width:72px;height:72px;font-size:26px;font-weight:700;flex-shrink:0">
            {{ strtoupper(substr($usuario->nombre,0,2)) }}
        </div>
        <div>
            <p style="font-size:18px;font-weight:600;color:#1a3a6b">{{ $usuario->nombre }}</p>
            <p style="font-size:13px;color:#6c757d;margin-top:3px">{{ ucfirst($usuario->rol) }} — {{ $usuario->correo }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('perfil.update') }}">
        @csrf @method('PUT')

        @if($errors->any())
        <div style="background:#ffebee;border:1px solid #ef9a9a;color:#c62828;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:13px">
            {{ $errors->first() }}
        </div>
        @endif

        <p style="font-size:13px;font-weight:600;color:#1a3a6b;margin-bottom:14px">Datos personales</p>

        <div style="display:flex;flex-direction:column;gap:5px;margin-bottom:14px">
            <label style="font-size:12px;color:#6c757d;font-weight:500">Nombre completo</label>
            <input type="text" name="nombre" value="{{ old('nombre',$usuario->nombre) }}" required
                   style="padding:9px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;outline:none;background:#f8f9fa">
        </div>

        <div style="display:flex;flex-direction:column;gap:5px;margin-bottom:14px">
            <label style="font-size:12px;color:#6c757d;font-weight:500">Correo electrónico (no se puede cambiar)</label>
            <input type="email" value="{{ $usuario->correo }}" readonly
                   style="padding:9px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;background:#f0f2f5;color:#6c757d">
        </div>

        <div style="display:flex;flex-direction:column;gap:5px;margin-bottom:14px">
            <label style="font-size:12px;color:#6c757d;font-weight:500">Nombre de usuario</label>
            <input type="text" name="usuario" value="{{ old('usuario',$usuario->usuario) }}" required
                   style="padding:9px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;outline:none;background:#f8f9fa">
        </div>

        @if($usuario->rol === 'cliente')
        <div style="display:flex;flex-direction:column;gap:5px;margin-bottom:14px">
            <label style="font-size:12px;color:#6c757d;font-weight:500">Descripción</label>
            <textarea name="descripcion" rows="3" style="padding:9px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;outline:none;background:#f8f9fa">{{ old('descripcion',$usuario->descripcion) }}</textarea>
        </div>
        @endif

        <hr style="border:none;border-top:1px solid #e2e6ea;margin:20px 0">
        <p style="font-size:13px;font-weight:600;color:#1a3a6b;margin-bottom:14px">Cambiar contraseña</p>

        <div style="display:flex;flex-direction:column;gap:5px;margin-bottom:14px">
            <label style="font-size:12px;color:#6c757d;font-weight:500">
                Nueva contraseña <span style="font-weight:400">(dejar vacío para no cambiar)</span>
            </label>
            <input type="password" name="contrasena_nueva" placeholder="Nueva contraseña"
                   style="padding:9px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;outline:none;background:#f8f9fa">
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px">
            <a href="javascript:history.back()"
               style="padding:8px 18px;border-radius:6px;border:1px solid #dee2e6;color:#6c757d;font-size:13px;text-decoration:none">
                Cancelar
            </a>
            <button type="submit" class="btn-primary">Guardar cambios</button>
        </div>
    </form>
</div>
@endsection
