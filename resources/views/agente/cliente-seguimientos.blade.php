@extends('layouts.panel')
@section('titulo','Seguimiento de cliente')
@section('titulo_pagina','Seguimiento de cliente')

@section('contenido')
<div class="card">
    <div class="card-header">
        <span class="card-title">Cliente: {{ $cliente->nombre }}</span>
        <span class="text-xs text-gray-500">{{ $cliente->correo }}</span>
    </div>

    @if(session('success'))
        <div class="alert success">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="card mb-6">
                <div class="card-header">
                    <span class="card-title">Historial de seguimientos</span>
                </div>
                <div class="p-4">
                    @if($seguimientos->isEmpty())
                        <p class="text-sm text-gray-600">Aún no hay notas de seguimiento para este cliente.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($seguimientos as $seguimiento)
                                <div class="border border-gray-200 rounded-lg p-4 bg-white">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <strong>{{ $seguimiento->tipo_contacto }}</strong>
                                            <span class="text-xs text-gray-500">· {{ $seguimiento->fecha }}</span>
                                        </div>
                                        <span class="text-xs text-gray-500">por {{ $seguimiento->agente->nombre ?? 'Agente' }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700">{{ $seguimiento->descripcion }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">Visitas relacionadas</span>
                </div>
                <div class="p-4">
                    @if($visitas->isEmpty())
                        <p class="text-sm text-gray-600">No hay visitas registradas para este cliente con tus propiedades.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($visitas as $visita)
                                <div class="border border-gray-200 rounded-lg p-3 bg-white">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-semibold">{{ $visita->propiedad->titulo ?? 'Propiedad' }}</span>
                                        <span class="text-xs text-gray-500">{{ $visita->estado }}</span>
                                    </div>
                                    <div class="text-sm text-gray-600">Fecha solicitada: {{ $visita->fecha_solicitada }}</div>
                                    <div class="text-sm text-gray-600">Mensaje: {{ $visita->mensaje }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Registrar seguimiento</span>
                </div>
                <div class="p-4">
                    @if($errors->any())
                        <div class="alert error mb-4">
                            <ul class="list-disc list-inside text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('agente.clientes.seguimientos.store', $cliente) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Fecha</label>
                            <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" class="input-full" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Tipo de contacto</label>
                            <select name="tipo_contacto" class="input-full" required>
                                <option value="">Selecciona un tipo</option>
                                <option value="Llamada" {{ old('tipo_contacto') === 'Llamada' ? 'selected' : '' }}>Llamada</option>
                                <option value="Correo" {{ old('tipo_contacto') === 'Correo' ? 'selected' : '' }}>Correo</option>
                                <option value="Visita" {{ old('tipo_contacto') === 'Visita' ? 'selected' : '' }}>Visita</option>
                                <option value="WhatsApp" {{ old('tipo_contacto') === 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="Otro" {{ old('tipo_contacto') === 'Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Descripción</label>
                            <textarea name="descripcion" rows="5" class="input-full" required>{{ old('descripcion') }}</textarea>
                        </div>
                        <button type="submit" class="btn-primary w-full">Guardar seguimiento</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
