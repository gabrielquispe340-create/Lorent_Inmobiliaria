@extends('layouts.panel')
@section('titulo', 'Actualizar Prospecto')
@section('titulo_pagina', 'Actualizar Prospecto CRM')

@section('contenido')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <span class="card-title">Actualizar Estado de Seguimiento</span>
    </div>
    
    <div class="p-4">
        @if($errors->any())
            <div class="alert error mb-4" style="padding: 10px; background-color: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 15px;">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('agente.prospectos.update', $prospecto) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Nombre del Prospecto *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $prospecto->nombre) }}" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;" required>
            </div>

            <div class="mb-4" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $prospecto->telefono) }}" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">
            </div>

            <div class="mb-4" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Correo Electrónico</label>
                <input type="email" name="email" value="{{ old('email', $prospecto->email) }}" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">
            </div>

            <div class="mb-4" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Propiedad de Interés</label>
                <select name="propiedad_id" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">
                    <option value="">-- Ninguna --</option>
                    @foreach($propiedades as $propiedad)
                        <option value="{{ $propiedad->id }}" {{ old('propiedad_id', $prospecto->propiedad_id) == $propiedad->id ? 'selected' : '' }}>
                            {{ $propiedad->titulo }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #0056b3;">Etapa de Seguimiento (Estado) *</label>
                <select name="etapa" style="width: 100%; padding: 8px; border: 1px solid #0056b3; border-radius: 4px;" required>
                    @foreach(['Nuevo', 'Contactado', 'Interesado', 'Negociando', 'Cerrado', 'Perdido'] as $etapa)
                        <option value="{{ $etapa }}" {{ old('etapa', $prospecto->etapa) == $etapa ? 'selected' : '' }}>{{ $etapa }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Notas / Observaciones</label>
                <textarea name="notas" rows="4" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">{{ old('notas', $prospecto->notas) }}</textarea>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" style="background-color: #0d6efd; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;">Guardar Cambios</button>
                <a href="{{ route('agente.prospectos.index') }}" style="background-color: #6c757d; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;">Cancelar</a>
            </div>
        </form>
        
        <form action="{{ route('agente.prospectos.destroy', $prospecto) }}" method="POST" style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('¿Seguro que deseas eliminar este prospecto?')" style="background-color: transparent; color: #dc3545; border: none; cursor: pointer; text-decoration: underline;">Eliminar prospecto</button>
        </form>
    </div>
</div>
@endsection
