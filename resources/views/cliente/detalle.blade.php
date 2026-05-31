@extends('layouts.panel')
@section('titulo', $propiedad->titulo)
@section('titulo_pagina', 'Detalle de propiedad')

@push('styles')
<style>
.detalle-hero{height:180px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:20px;position:relative}
.hero-venta{background:#1565c0}.hero-alquiler{background:#0f6e56}.hero-anticretico{background:#6a1b9a}
.hero-tag{position:absolute;top:12px;left:12px;font-size:12px;padding:4px 12px;border-radius:10px;font-weight:500}
.tag-venta{background:#e8f5e9;color:#2e7d32}.tag-alquiler{background:#fff8e1;color:#e65100}.tag-anticretico{background:#f3e5f5;color:#6a1b9a}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px}
.info-item label{font-size:11px;color:#6c757d;display:block;margin-bottom:3px}
.info-item p{font-size:14px;font-weight:500}
.precio-grande{font-size:28px;font-weight:700;color:#185FA5}
.form-group{display:flex;flex-direction:column;gap:5px;margin-bottom:14px}
.form-group label{font-size:12px;color:#6c757d;font-weight:500}
.form-group input,.form-group textarea{padding:9px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;outline:none;background:#f8f9fa}
.form-group input:focus,.form-group textarea:focus{border-color:#64b5f6;background:#fff}
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('contenido')
<div style="width:100%; padding:15px; display:flex; flex-direction:row; gap:20px; align-items:flex-start;">
    <div class="card" style="flex:0 0 360px; max-width:360px;">
        <div class="w-full relative overflow-hidden bg-slate-100 mb-5 rounded-lg" style="height:220px;">
            @if($propiedad->imagen)
                <img src="{{ asset('storage/' . $propiedad->imagen) }}" alt="{{ $propiedad->titulo }}" style="width:100%;height:100%;object-fit:cover;">
            @else
                <span style="color:#aaa;font-size:14px">Sin foto disponible</span>
            @endif
            <span class="hero-tag tag-{{ strtolower($propiedad->tipo) }}">{{ $propiedad->tipo }}</span>
        </div>
        <h2 style="font-size:20px;font-weight:600;color:#0f4c75;margin-bottom:6px">{{ $propiedad->titulo }}</h2>
        <p style="color:#6c757d;font-size:13px;margin-bottom:16px">{{ $propiedad->zona }}</p>
        <div class="info-grid">
            <div class="info-item"><label>Precio</label><p class="precio-grande">${{ number_format($propiedad->precio,0,',','.') }}</p></div>
            <div class="info-item"><label>Estado</label><p><span class="badge badge-{{ strtolower($propiedad->estado) }}">{{ $propiedad->estado }}</span></p></div>
            <div class="info-item"><label>Área</label><p>{{ $propiedad->area ? $propiedad->area.' m²' : '—' }}</p></div>
            <div class="info-item"><label>Agente</label><p>{{ $propiedad->agente->nombre ?? 'Sin asignar' }}</p></div>
        </div>
        <hr style="border:none;border-top:1px solid #e2e6ea;margin:20px 0">
        <p style="font-size:13px;font-weight:500;color:#0f4c75;margin-bottom:8px">Descripción</p>
        <p style="font-size:13px;color:#444;line-height:1.7">{{ $propiedad->descripcion ?? 'Sin descripción.' }}</p>

        @if($propiedad->latitud && $propiedad->longitud)
        <hr style="border:none;border-top:1px solid #e2e6ea;margin:20px 0">
        <p style="font-size:13px;font-weight:500;color:#0f4c75;margin-bottom:8px;">📍 Ubicación</p>
        <div id="mapaDetalle" style="height:220px;border-radius:8px;border:1px solid #dee2e6;"></div>
        @endif

        <div style="margin-top:20px">
            <a href="{{ route('cliente.propiedades') }}" class="btn-detalle">← Volver</a>
        </div>
    </div>

    @if(auth()->user()->rol === 'cliente')
    <div class="card" style="flex:1; min-width:0;">
        <p style="font-size:15px;font-weight:600;color:#0f4c75;margin-bottom:16px">Solicitar visita</p>
        <form method="POST" action="{{ route('cliente.solicitudes.store') }}">
            @csrf
            <input type="hidden" name="propiedad_id" value="{{ $propiedad->id }}">
            <div class="form-group">
                <label>Fecha preferida</label>
                <input type="date" name="fecha_solicitada" min="{{ date('Y-m-d') }}" required>
            </div>
            <div class="form-group">
                <label>Mensaje al agente</label>
                <textarea name="mensaje" rows="4" placeholder="Escribe tus preguntas o preferencias..." required></textarea>
            </div>
            <button type="submit" class="btn-primary" style="width:100%">Enviar solicitud</button>
        </form>
    </div>
    @else
    <div class="card" style="flex:1; min-width:0; text-align:center;color:#6c757d;padding:30px">
        <p style="font-size:13px">Solo los clientes pueden solicitar visitas.</p>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
@if($propiedad->latitud && $propiedad->longitud)
var propLat    = {{ $propiedad->latitud }};
var propLng    = {{ $propiedad->longitud }};
var propTitulo = "{{ $propiedad->titulo }}";
var propZona   = "{{ $propiedad->zona }}";

const mapaDet = L.map('mapaDetalle').setView([propLat, propLng], 16);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    { attribution: '© OpenStreetMap' }).addTo(mapaDet);
L.marker([propLat, propLng])
    .addTo(mapaDet)
    .bindPopup('<b>' + propTitulo + '</b><br>' + propZona)
    .openPopup();
@endif
</script>
@endpush