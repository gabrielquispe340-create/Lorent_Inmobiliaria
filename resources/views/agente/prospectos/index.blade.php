@extends('layouts.panel')
@section('titulo', 'Mis Prospectos')
@section('titulo_pagina', 'Mis Prospectos')

@section('contenido')
<div class="card">
    <div class="card-header flex justify-between items-center">
        <span class="card-title">Listado de Prospectos CRM</span>
        <a href="{{ route('agente.prospectos.create') }}" class="btn-primary" style="padding: 5px 15px; text-decoration: none; border-radius: 5px;">+ Nuevo Prospecto</a>
    </div>

    @if(session('success'))
        <div class="alert success" style="padding: 10px; background-color: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
        <table class="min-w-[600px] w-full text-sm text-left">
            <thead>
                <tr style="background-color: #f8f9fa;">
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Nombre</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Contacto</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Etapa (Estado)</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Propiedad de Interés</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prospectos as $p)
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">{{ $p->nombre }}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">
                        {{ $p->telefono ?? '—' }} <br>
                        <span style="font-size: 0.85em; color: #6c757d;">{{ $p->email ?? '' }}</span>
                    </td>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">
                        <span class="badge" style="background-color: #e9ecef; color: #495057; padding: 3px 8px; border-radius: 10px;">{{ $p->etapa }}</span>
                    </td>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">{{ $p->propiedad->titulo ?? 'Ninguna' }}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">
                        <a href="{{ route('agente.prospectos.edit', $p) }}" class="btn-edit" style="color: #007bff; text-decoration: none;">Actualizar</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:#6c757d;padding:20px">No tienes prospectos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
