@extends('layouts.panel')
@section('titulo', 'Inicio — Cliente')
@section('titulo_pagina', 'Inicio')

@section('contenido')

{{-- ═══════════════════════════════════════
     BARRA DE BIENVENIDA
════════════════════════════════════════ --}}
<div class="welcome-bar">
    <div class="welcome-text">
        <h3>Bienvenido, {{ explode(' ', auth()->user()->nombre)[0] }} 👋</h3>
        <p>Encuentra tu próxima propiedad entre nuestras opciones disponibles</p>
    </div>
    <a href="{{ route('cliente.propiedades') }}" class="btn-primary">Ver todas las propiedades</a>
</div>

{{-- ═══════════════════════════════════════
     TARJETAS DE ESTADÍSTICAS
════════════════════════════════════════ --}}
<div class="stats grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    {{-- Card 1: Propiedades disponibles (destacada) --}}
    <div class="stat-card">
        <p class="stat-label">Propiedades disponibles</p>
        <p class="stat-value">{{ $totalDisp }}</p>
        <span class="stat-icon">🏘️</span>
    </div>

    {{-- Card 2: En venta --}}
    <div class="stat-card">
        <p class="stat-label">En venta</p>
        <p class="stat-value">{{ $totalVenta }}</p>
        <span class="stat-icon">🏷️</span>
    </div>

    {{-- Card 3: En alquiler --}}
    <div class="stat-card">
        <p class="stat-label">En alquiler</p>
        <p class="stat-value">{{ $totalAlquiler }}</p>
        <span class="stat-icon">🔑</span>
    </div>

</div>

{{-- ═══════════════════════════════════════
     PROPIEDADES DISPONIBLES
════════════════════════════════════════ --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Propiedades disponibles</span>
        <a href="{{ route('cliente.propiedades') }}" class="btn-primary" style="background:#1a3d8f;color:#fff;box-shadow:none;">Ver todas</a>
    </div>

    <div class="prop-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($propiedades as $p)
    <div class="prop-card">
        <div class="prop-img prop-img-{{ strtolower($p->tipo) }}">
            @if($p->imagen)
                <img src="{{ asset('storage/' . $p->imagen) }}" alt="{{ $p->titulo }}" style="width:100%;height:100%;object-fit:cover;display:block">
            @else
                <span class="prop-img-placeholder">Sin foto</span>
            @endif
            <span class="prop-tag tag-{{ strtolower($p->tipo) }}">{{ $p->tipo }}</span>
            <span class="prop-price-banner">${{ number_format($p->precio, 0, ',', '.') }}</span>
        </div>
        <div class="prop-body">
            <p class="prop-title">{{ $p->titulo }}</p>
            <p class="prop-zona">{{ $p->zona }}</p>
            <div class="prop-footer">
                <div>
                    <p class="prop-price">${{ number_format($p->precio, 0, ',', '.') }}</p>
                    <p class="prop-area">{{ $p->area ? $p->area.' m²' : '—' }}</p>
                </div>
                <a href="{{ route('cliente.propiedades.detalle', $p) }}" class="btn-detalle">Ver detalle</a>
            </div>
        </div>
    </div>
    @empty
    <p style="color:#8a94a6;padding:28px 0;grid-column:1/-1;text-align:center;font-size:13px;">
        No hay propiedades disponibles en este momento.
    </p>
    @endforelse
    </div>
</div>

@endsection