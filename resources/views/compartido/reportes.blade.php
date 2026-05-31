@extends('layouts.panel')
@section('titulo', 'Reportes')
@section('titulo_pagina', 'Reportes de actividad')

@push('styles')
<style>
/* Badges de acción */
.badge-accion-login    { background:#e8f5e9; color:#2e7d32; }
.badge-accion-logout   { background:#fff8e1; color:#e65100; }
.badge-accion-fallido  { background:#ffebee; color:#c62828; }
.badge-accion-propiedad{ background:#e3f2fd; color:#1565c0; }
.badge-accion-visita   { background:#f3e5f5; color:#6a1b9a; }
.badge-accion-usuario  { background:#e0f7fa; color:#00695c; }
.badge-accion-default  { background:#f5f5f5; color:#424242; }

/* Badges de rol */
.rol-badge         { font-size:11px; padding:3px 9px; border-radius:20px; font-weight:600; display:inline-block; }
.rol-administrador { background:#e3f0ff; color:#1565c0; }
.rol-agente        { background:#e8f5e9; color:#2e7d32; }
.rol-asistente     { background:#ede7f6; color:#512da8; }
.rol-cliente       { background:#fff8e1; color:#e65100; }

/* Filtros */
.filter-bar {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    align-items: flex-end;
}
.filter-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.filter-group label {
    font-size: 11px;
    color: #8a94a6;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.filter-group select,
.filter-group input[type="date"] {
    padding: 8px 11px;
    border: 1.5px solid #e8eaed;
    border-radius: 8px;
    font-size: 13px;
    font-family: inherit;
    outline: none;
    background: #fafbfc;
    color: #1a1a2e;
    transition: border-color 200ms;
    min-width: 160px;
}
.filter-group select:focus,
.filter-group input[type="date"]:focus {
    border-color: #1a3d8f;
    background: #fff;
}
.btn-filter {
    background: #1a3d8f; color: #fff; border: none;
    padding: 8px 20px; border-radius: 8px; font-size: 13px;
    cursor: pointer; font-family: inherit; font-weight: 500;
    transition: background 200ms; white-space: nowrap;
}
.btn-filter:hover { background: #163273; }

.btn-limpiar {
    padding: 8px 16px; border-radius: 8px;
    border: 1.5px solid #e8eaed; color: #8a94a6;
    font-size: 13px; text-decoration: none;
    background: #fff; transition: border-color 200ms, color 200ms;
    white-space: nowrap;
}
.btn-limpiar:hover { border-color:#1a3d8f; color:#1a3d8f; }

/* Acción badge inline */
.accion-badge {
    display: inline-flex;
    align-items: center;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    white-space: nowrap;
}
</style>
@endpush

@section('contenido')

{{-- ═══════════════════════════════════════
     TARJETAS DE RESUMEN
════════════════════════════════════════ --}}
<div class="stats grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" style="margin-bottom:20px">

    <div class="stat-card">
        <p class="stat-label">Inicios de sesión</p>
        <p class="stat-value">{{ $totalLogins }}</p>
        <span class="stat-icon">🔐</span>
    </div>

    <div class="stat-card">
        <p class="stat-label">Intentos fallidos</p>
        <p class="stat-value" style="color:#e53935">{{ $totalFallidos }}</p>
        <span class="stat-icon">⚠️</span>
    </div>

    <div class="stat-card">
        <p class="stat-label">Propiedades registradas</p>
        <p class="stat-value">{{ $totalProps }}</p>
        <span class="stat-icon">🏠</span>
    </div>

    <div class="stat-card">
        <p class="stat-label">Actividad hoy</p>
        <p class="stat-value">{{ $totalHoy }}</p>
        <span class="stat-icon">📊</span>
    </div>

</div>

{{-- ═══════════════════════════════════════
     FILTROS
════════════════════════════════════════ --}}
<div class="card" style="margin-bottom:20px">
    <p class="card-title" style="margin-bottom:16px">Filtrar registros</p>
    <form method="GET">
        <div class="filter-bar">
            <div class="filter-group">
                <label>Tipo de acción</label>
                <select name="accion">
                    <option value="todas">Todas las acciones</option>
                    @foreach($acciones as $a)
                    <option value="{{ $a }}" {{ $filtroAccion===$a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>Rol</label>
                <select name="rol">
                    <option value="todos">Todos los roles</option>
                    @foreach(['administrador','agente','asistente','cliente'] as $r)
                    <option value="{{ $r }}" {{ $filtroRol===$r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>Fecha</label>
                <input type="date" name="fecha" value="{{ $filtroFecha }}">
            </div>

            <div style="display:flex;gap:8px;align-items:flex-end">
                <button type="submit" class="btn-filter">Filtrar</button>
                <a href="{{ request()->url() }}" class="btn-limpiar">Limpiar</a>
                <div style="display:flex;gap:8px;align-items:center;margin-left:8px">
                    @php $qs = request()->getQueryString(); $qs = $qs ? ('?'.$qs) : '' ; @endphp
                    <a href="{{ request()->url() }}/export/pdf{{ $qs }}" class="btn-filter" style="background:#0b74d1">Descargar PDF</a>
                    <a href="{{ request()->url() }}/export/xlsx{{ $qs }}" class="btn-filter" style="background:#0b9a44">Descargar Excel</a>
                    <a href="{{ request()->url() }}/export/csv{{ $qs }}" class="btn-limpiar" style="border-color:#cbd5e1;color:#0b3b11">Descargar CSV</a>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- ═══════════════════════════════════════
     TABLA DE ACTIVIDAD
════════════════════════════════════════ --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">
            Registro de actividad
            <span style="font-size:12px;color:#8a94a6;font-weight:400;margin-left:6px">
                ({{ $actividad->count() }} registros)
            </span>
        </span>
    </div>

    <div class="table-responsive">
<div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha y hora</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Acción</th>
                    <th>Descripción</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
            @forelse($actividad as $r)
            @php
                $al = strtolower($r->accion);
                if      (str_contains($al,'inicio'))        $cls = 'badge-accion-login';
                elseif  (str_contains($al,'cierre'))        $cls = 'badge-accion-logout';
                elseif  (str_contains($al,'fallido'))       $cls = 'badge-accion-fallido';
                elseif  (str_contains($al,'propiedad'))     $cls = 'badge-accion-propiedad';
                elseif  (str_contains($al,'visita'))        $cls = 'badge-accion-visita';
                elseif  (str_contains($al,'usuario'))       $cls = 'badge-accion-usuario';
                else                                        $cls = 'badge-accion-default';
            @endphp
            <tr>
                <td style="color:#8a94a6;font-size:12px">{{ $r->id }}</td>

                <td style="white-space:nowrap;font-size:12px">
                    <strong>{{ \Carbon\Carbon::parse($r->fecha_hora)->format('d/m/Y') }}</strong><br>
                    <span style="color:#8a94a6">{{ \Carbon\Carbon::parse($r->fecha_hora)->format('H:i:s') }}</span>
                </td>

                <td>
                    <div style="font-size:13px;font-weight:500;color:#0f1e3c">{{ $r->nombre ?? '—' }}</div>
                    <div style="font-size:11px;color:#8a94a6">{{ $r->correo ?? '' }}</div>
                </td>

                <td>
                    @if($r->rol)
                        <span class="rol-badge rol-{{ $r->rol }}">{{ ucfirst($r->rol) }}</span>
                    @else
                        <span style="color:#8a94a6">—</span>
                    @endif
                </td>

                <td>
                    <span class="accion-badge {{ $cls }}">{{ $r->accion }}</span>
                </td>

                <td style="font-size:12px;color:#6c757d;max-width:260px">
                    {{ $r->descripcion }}
                </td>

                <td style="font-size:11px;color:#8a94a6;font-family:monospace">
                    {{ $r->ip ?? '—' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;color:#8a94a6;padding:36px">
                    No hay registros con los filtros seleccionados.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
</div>
 </div>
</div>

@endsection