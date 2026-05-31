@extends('layouts.panel')
@section('titulo', 'Panel Asistente')
@section('titulo_pagina', 'Dashboard')

@push('styles')
<style>
.rol-badge        { font-size:11px; padding:3px 9px; border-radius:20px; font-weight:600; display:inline-block; }
.rol-administrador{ background:#e3f0ff; color:#1565c0; }
.rol-agente       { background:#e8f5e9; color:#2e7d32; }
.rol-asistente    { background:#ede7f6; color:#512da8; }
.rol-cliente      { background:#fff8e1; color:#e65100; }

.visit-status-pendiente  { background:#fff8e1; color:#e65100; }
.visit-status-confirmada { background:#e8f5e9; color:#2e7d32; }
.visit-status-cancelada  { background:#ffebee; color:#c62828; }
</style>
@endpush

@section('contenido')

{{-- ═══════════════════════════════════════
     TARJETAS DE ESTADÍSTICAS
════════════════════════════════════════ --}}
<div class="stats grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    <div class="stat-card">
        <p class="stat-label">Clientes registrados</p>
        <p class="stat-value">{{ $totalClientes }}</p>
        <span class="stat-icon">👥</span>
    </div>

    <div class="stat-card">
        <p class="stat-label">Visitas pendientes</p>
        <p class="stat-value">{{ $visitasPend }}</p>
        <span class="stat-icon">📋</span>
    </div>

    <div class="stat-card">
        <p class="stat-label">Visitas hoy</p>
        <p class="stat-value">{{ $visitasHoy }}</p>
        <span class="stat-icon">📅</span>
    </div>

    <div class="stat-card">
        <p class="stat-label">Propiedades disponibles</p>
        <p class="stat-value">{{ $totalProps }}</p>
        <span class="stat-icon">🏠</span>
    </div>

</div>

{{-- ═══════════════════════════════════════
     PRÓXIMAS VISITAS
════════════════════════════════════════ --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <span class="card-title">Próximas visitas</span>
        <a href="{{ route('asistente.visitas') }}" class="btn-primary">Ver agenda completa</a>
    </div>
    <div class="table-responsive">
<div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Propiedad</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
            @forelse($visitas as $v)
            <tr>
                <td>
                    <div class="agent-cell">
                        <div class="agent-avatar">
                            {{ strtoupper(substr($v->cliente->nombre ?? 'CL', 0, 2)) }}
                        </div>
                        {{ $v->cliente->nombre ?? '—' }}
                    </div>
                </td>
                <td>{{ $v->propiedad->titulo ?? '—' }}</td>
                <td style="white-space:nowrap;font-size:12px">
                    <strong>{{ \Carbon\Carbon::parse($v->fecha_solicitada)->format('d/m/Y') }}</strong>
                </td>
                <td>
                    <span class="badge visit-status-{{ $v->estado }}">
                        {{ ucfirst($v->estado) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center;color:#8a94a6;padding:28px">
                    No hay visitas próximas pendientes.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
</div>
 </div>
</div>

{{-- ═══════════════════════════════════════
     ÚLTIMOS CLIENTES REGISTRADOS
════════════════════════════════════════ --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Últimos clientes registrados</span>
    </div>
    <div class="table-responsive">
<div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
            @forelse($clientes as $c)
            <tr>
                <td style="color:#8a94a6;font-size:12px">{{ $c->id }}</td>
                <td>
                    <div class="agent-cell">
                        <div class="agent-avatar">
                            {{ strtoupper(substr($c->nombre, 0, 2)) }}
                        </div>
                        {{ $c->nombre }}
                    </div>
                </td>
                <td style="color:#8a94a6;font-size:12px">{{ $c->correo }}</td>
                <td style="font-size:12px">{{ $c->usuario }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center;color:#8a94a6;padding:28px">
                    No hay clientes registrados aún.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
</div>
 </div>
</div>

@endsection