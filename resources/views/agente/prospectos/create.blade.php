@extends('layouts.panel')
@section('titulo', 'Nuevo Prospecto')
@section('titulo_pagina', 'Registrar Nuevo Prospecto')

@section('contenido')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <span class="card-title">Información del Prospecto</span>
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

        <form action="{{ route('agente.prospectos.store') }}" method="POST">
            @csrf
            
            <div class="mb-4" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Nombre del Prospecto *</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;" required>
            </div>

            <div class="mb-4" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono') }}" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">
            </div>

            <div class="mb-4" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Correo Electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">
            </div>

            <div class="mb-4" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Propiedad de Interés</label>
                <select name="propiedad_id" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">
                    <option value="">-- Ninguna --</option>
                    @foreach($propiedades as $propiedad)
                        <option value="{{ $propiedad->id }}" {{ old('propiedad_id') == $propiedad->id ? 'selected' : '' }}>
                            {{ $propiedad->titulo }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Etapa Inicial *</label>
                <select name="etapa" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;" required>
                    <option value="Nuevo" {{ old('etapa') == 'Nuevo' ? 'selected' : '' }}>Nuevo</option>
                    <option value="Contactado" {{ old('etapa') == 'Contactado' ? 'selected' : '' }}>Contactado</option>
                    <option value="Interesado" {{ old('etapa') == 'Interesado' ? 'selected' : '' }}>Interesado</option>
                </select>
            </div>

            <div class="mb-4" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Notas Iniciales</label>
                <textarea name="notas" rows="4" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">{{ old('notas') }}</textarea>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" style="background-color: #0d6efd; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;">Registrar Prospecto</button>
                <a href="{{ route('agente.prospectos.index') }}" style="background-color: #6c757d; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
