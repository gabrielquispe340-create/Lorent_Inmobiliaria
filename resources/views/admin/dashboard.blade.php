@extends('layouts.panel')
@section('titulo', 'Dashboard — Administrador')
@section('titulo_pagina', 'Dashboard')

@section('contenido')

{{-- ═══════════════════════════════════════
     TARJETAS DE ESTADÍSTICAS
════════════════════════════════════════ --}}
<div class="stats grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    {{-- Card 1: Total propiedades (destacada) --}}
    <div class="stat-card">
        <span class="badge-available">● Available</span>
        <p class="stat-label">Total propiedades</p>
        <p class="stat-value">{{ $totalProps }}</p>
        <span class="badge badge-green" style="margin-top:10px;display:inline-flex">
            {{ $disponibles }} disponibles
        </span>
        <span class="stat-icon">🏠</span>
    </div>

    {{-- Card 2: Propiedades vendidas --}}
    <div class="stat-card">
        <p class="stat-label">Propiedades vendidas</p>
        <p class="stat-value">{{ $totalVentas }}</p>
        <span class="badge" style="margin-top:10px;background:#fff3e0;color:#e65100;display:inline-flex">
            {{ $totalVentas > 0 ? $totalVentas : '0' }} en proceso
        </span>
        <span class="stat-icon">💰</span>
    </div>

    {{-- Card 3: Usuarios del sistema --}}
    <div class="stat-card">
        <p class="stat-label">Usuarios del sistema</p>
        <p class="stat-value">{{ $totalUsuarios }}</p>
        <span class="stat-icon">👥</span>
    </div>

</div>

{{-- ═══════════════════════════════════════
     ACTIVIDAD RECIENTE
════════════════════════════════════════ --}}
<div class="activity-card">
    <p class="section-title">Actividad reciente</p>

    <div class="activity-list">

        {{-- Propiedades registradas --}}
        <div class="activity-item">
            <div class="activity-icon blue">🏡</div>
            <div class="activity-body">
                <p class="activity-title">Propiedades registradas</p>
                @if($totalProps > 0)
                    <p class="activity-desc">
                        {{ $totalProps }} propiedad(es) registrada(s) en el sistema.
                    </p>
                @else
                    <p class="activity-desc">Sin propiedades registradas aún.</p>
                @endif
            </div>
        </div>

        {{-- Ventas registradas --}}
        <div class="activity-item">
            <div class="activity-icon gold">💲</div>
            <div class="activity-body">
                <p class="activity-title">Ventas registradas</p>
                <p class="activity-desc">{{ $totalVentas }} ventas registradas este mes.</p>
            </div>
        </div>

        {{-- Usuarios activos --}}
        <div class="activity-item">
            <div class="activity-icon purple">👤</div>
            <div class="activity-body">
                <p class="activity-title">Usuarios activos</p>
                <p class="activity-desc">{{ $totalUsuarios }} usuarios actualmente en el sistema.</p>
            </div>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════
     ÚLTIMAS PROPIEDADES REGISTRADAS
════════════════════════════════════════ --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Últimas propiedades registradas</span>
        <a href="{{ route('admin.propiedades') }}" class="btn-primary">Ver todas</a>
    </div>

    <div class="table-responsive">
<div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Zona</th>
                    <th>Tipo</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Agente</th>
                </tr>
            </thead>
            <tbody>
            @forelse($ultimas as $p)
            <tr>
                <td>{{ $p->titulo }}</td>
                <td>{{ $p->zona }}</td>
                <td>
                    <span class="badge badge-{{ strtolower($p->tipo) === 'alquiler' ? 'alquiler' : 'venta' }}">
                        {{ $p->tipo }}
                    </span>
                </td>
                <td>${{ number_format($p->precio, 0, ',', '.') }}</td>
                <td>
                    <span class="badge badge-{{ strtolower($p->estado) }}">
                        {{ $p->estado }}
                    </span>
                </td>
                <td>
                    <div class="agent-cell">
                        <div class="agent-avatar">
                            {{ strtoupper(substr($p->agente->nombre ?? 'SA', 0, 2)) }}
                        </div>
                        {{ $p->agente->nombre ?? 'Sin asignar' }}
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;color:#8a94a6;padding:28px">
                    No hay propiedades registradas aún.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
</div>
 </div>
</div>

@endsection