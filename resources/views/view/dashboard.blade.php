{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.panel')
@section('titulo', 'Dashboard — Admin')
@section('titulo_pagina', 'Dashboard')

@section('contenido')

<div class="stats grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="stat-card">
        <p class="stat-label">Total propiedades</p>
        <p class="stat-value">{{ $totalProps }}</p>
        <span class="badge badge-green" style="margin-top:6px;display:inline-block">{{ $disponibles }} disponibles</span>
    </div>
    <div class="stat-card">
        <p class="stat-label">Propiedades vendidas</p>
        <p class="stat-value">{{ $totalVentas }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Usuarios del sistema</p>
        <p class="stat-value">{{ $totalUsuarios }}</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Últimas propiedades</span>
        <a href="{{ route('admin.propiedades') }}" class="btn-primary">Ver todas</a>
    </div>
<div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
        <thead>
            <tr><th>Título</th><th>Zona</th><th>Tipo</th><th>Precio</th><th>Estado</th><th>Agente</th></tr>
        </thead>
        <tbody>
        @forelse($ultimas as $p)
        <tr>
            <td>{{ $p->titulo }}</td>
            <td>{{ $p->zona }}</td>
            <td>{{ $p->tipo }}</td>
            <td>${{ number_format($p->precio, 0, ',', '.') }}</td>
            <td><span class="badge badge-{{ strtolower($p->estado) }}">{{ $p->estado }}</span></td>
            <td>{{ $p->agente->nombre ?? 'Sin asignar' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:#6c757d;padding:20px">No hay propiedades aún.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

</div>

@endsection
