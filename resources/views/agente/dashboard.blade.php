@extends('layouts.panel')
@section('titulo', 'Panel Agente')
@section('titulo_pagina', 'Panel del Agente')

@section('contenido')

<div class="stats grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="stat-card">
        <p class="stat-label">Mis propiedades</p>
        <p class="stat-value">{{ $misProps }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Disponibles</p>
        <p class="stat-value">{{ $disponibles }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Vendidas</p>
        <p class="stat-value">{{ $vendidas }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Visitas pendientes</p>
        <p class="stat-value">{{ $visitasPend }}</p>
    </div>
</div>

{{-- Mis propiedades recientes --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <span class="card-title">Mis propiedades recientes</span>
        <a href="{{ route('agente.propiedades') }}" class="btn-primary">Ver todas</a>
    </div>
<div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
        <thead>
            <tr>
                <th>Título</th>
                <th>Zona</th>
                <th>Tipo</th>
                <th>Precio</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
        @forelse($ultimas as $p)
        <tr>
            <td>{{ $p->titulo }}</td>
            <td>{{ $p->zona }}</td>
            <td>{{ $p->tipo }}</td>
            <td>${{ number_format($p->precio, 0, ',', '.') }}</td>
            <td>
                <span class="badge badge-{{ strtolower($p->estado) }}">
                    {{ $p->estado }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align:center;color:#6c757d;padding:20px">
                No tienes propiedades asignadas aún.
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

</div>

{{-- Visitas pendientes --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Solicitudes de visita pendientes</span>
        <a href="{{ route('agente.visitas') }}" class="btn-primary">Ver todas</a>
    </div>
<div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
        <thead>
            <tr>
                <th>Propiedad</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
        @forelse($visitas as $v)
        <tr>
            <td>{{ $v->propiedad->titulo ?? '—' }}</td>
            <td>{{ $v->cliente->nombre ?? '—' }}</td>
            <td>{{ $v->fecha_solicitada }}</td>
            <td><span class="badge badge-reservado">Pendiente</span></td>
            <td>
                <div class="action-btns">
                    <form method="POST"
                          action="{{ route('agente.visitas.estado', $v->id) }}"
                          style="display:inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="estado" value="confirmada">
                        <button type="submit" class="btn-edit">Confirmar</button>
                    </form>
                    <form method="POST"
                          action="{{ route('agente.visitas.estado', $v->id) }}"
                          style="display:inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="estado" value="cancelada">
                        <button type="submit" class="btn-delete">Cancelar</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align:center;color:#6c757d;padding:20px">
                No hay visitas pendientes.
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

</div>

@endsection
