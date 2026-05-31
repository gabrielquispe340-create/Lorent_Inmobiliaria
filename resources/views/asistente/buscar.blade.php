@extends('layouts.panel')
@section('titulo', 'Buscar propiedades')
@section('titulo_pagina', 'Propiedades')

@section('contenido')

{{-- ══════════ BUSCADOR PRINCIPAL ══════════ --}}
<div style="
    background: linear-gradient(135deg, #2e1a5f 0%, #1a0f3a 100%);
    border-radius: 14px;
    padding: 28px 24px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
">
    <div style="position:absolute;top:-30px;right:-30px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,0.04);pointer-events:none"></div>
    <div style="position:absolute;bottom:-50px;right:80px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.03);pointer-events:none"></div>

    <p style="color:rgba(255,255,255,0.6);font-size:12px;margin-bottom:6px;position:relative">
        Panel Asistente
    </p>
    <h2 style="color:#fff;font-size:20px;font-weight:700;margin-bottom:4px;position:relative">
        Buscar propiedades
    </h2>
    <p style="color:rgba(255,255,255,0.55);font-size:13px;margin-bottom:20px;position:relative">
        Consulta las {{ $totalPropiedades }} propiedades disponibles en el sistema
    </p>

    <form method="GET" action="{{ route('asistente.buscar') }}" id="formBuscar" style="position:relative">
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <div style="flex:1;min-width:220px;position:relative">
                <i class="ti ti-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:17px;pointer-events:none"></i>
                <input
                    type="text"
                    name="q"
                    value="{{ $q }}"
                    placeholder="Título, zona, descripción..."
                    autocomplete="off"
                    style="
                        width:100%; padding:12px 14px 12px 40px;
                        border:1.5px solid rgba(255,255,255,0.12);
                        border-radius:10px;
                        background:rgba(255,255,255,0.08);
                        color:#fff; font-size:13px; font-family:inherit;
                        outline:none; transition:border-color .2s;
                    "
                    onfocus="this.style.borderColor='rgba(174,144,247,0.8)'"
                    onblur="this.style.borderColor='rgba(255,255,255,0.12)'"
                >
            </div>
            <button type="submit" style="
                padding:12px 22px;
                background:#7c5cbf; color:#fff;
                border:none; border-radius:10px;
                font-size:13px; font-weight:600; font-family:inherit;
                cursor:pointer; transition:background .18s;
                display:flex; align-items:center; gap:7px; white-space:nowrap;
            "
            onmouseover="this.style.background='#6547a8'"
            onmouseout="this.style.background='#7c5cbf'">
                <i class="ti ti-search" style="font-size:15px"></i>
                Buscar
            </button>
            @if($q || $tipo !== 'Todas' || $estado !== 'Disponible' || $precioMax)
            <a href="{{ route('asistente.buscar') }}" style="
                padding:12px 18px;
                background:rgba(255,255,255,0.08); color:rgba(255,255,255,0.7);
                border:1.5px solid rgba(255,255,255,0.12); border-radius:10px;
                font-size:13px; font-family:inherit; cursor:pointer;
                display:flex; align-items:center; gap:6px; white-space:nowrap;
                text-decoration:none;
            ">
                <i class="ti ti-x" style="font-size:14px"></i>
                Limpiar
            </a>
            @endif
        </div>
    </form>
</div>

{{-- ══════════ FILTROS + RESULTADOS ══════════ --}}
<div style="display:grid;grid-template-columns:220px 1fr;gap:16px;align-items:start">

    {{-- PANEL FILTROS --}}
    <div class="card" style="padding:0;overflow:hidden" id="panelFiltros">
        <div style="padding:14px 16px;border-bottom:1px solid #e2e8f0">
            <span style="font-size:13px;font-weight:600;color:#0f172a;display:flex;align-items:center;gap:7px">
                <i class="ti ti-adjustments-horizontal" style="font-size:16px;color:#7c5cbf"></i>
                Filtros
            </span>
        </div>

        <form method="GET" action="{{ route('asistente.buscar') }}" style="padding:16px">
            <input type="hidden" name="q" value="{{ $q }}">

            {{-- Tipo --}}
            <div style="margin-bottom:16px">
                <label style="font-size:11px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:8px">
                    Tipo
                </label>
                @foreach(['Todas','Venta','Alquiler','Anticretico'] as $t)
                <label style="display:flex;align-items:center;gap:8px;padding:6px 0;cursor:pointer">
                    <input
                        type="radio" name="tipo" value="{{ $t }}"
                        {{ $tipo === $t ? 'checked' : '' }}
                        style="accent-color:#7c5cbf;width:15px;height:15px;cursor:pointer"
                        onchange="this.form.submit()"
                    >
                    <span style="font-size:13px;color:#374151">{{ $t }}</span>
                </label>
                @endforeach
            </div>

            <div style="height:1px;background:#e2e8f0;margin-bottom:16px"></div>

            {{-- Estado --}}
            <div style="margin-bottom:16px">
                <label style="font-size:11px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:8px">
                    Estado
                </label>
                @foreach(['Disponible'=>'Disponible','Reservado'=>'Reservado','Todas'=>'Todos'] as $val => $label)
                <label style="display:flex;align-items:center;gap:8px;padding:6px 0;cursor:pointer">
                    <input
                        type="radio" name="estado" value="{{ $val }}"
                        {{ $estado === $val ? 'checked' : '' }}
                        style="accent-color:#7c5cbf;width:15px;height:15px;cursor:pointer"
                        onchange="this.form.submit()"
                    >
                    <span style="font-size:13px;color:#374151">{{ $label }}</span>
                </label>
                @endforeach
            </div>

            <div style="height:1px;background:#e2e8f0;margin-bottom:16px"></div>

            {{-- Precio máximo --}}
            <div style="margin-bottom:16px">
                <label style="font-size:11px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:8px">
                    Precio máximo ($)
                </label>
                <input
                    type="number"
                    name="precio_max"
                    value="{{ $precioMax }}"
                    placeholder="Ej: 200000"
                    min="0"
                    style="
                        width:100%; padding:9px 10px;
                        border:1.5px solid #e2e8f0; border-radius:8px;
                        font-size:13px; font-family:inherit; color:#0f172a;
                        background:#f8fafc; outline:none;
                        transition:border-color .15s;
                    "
                    onfocus="this.style.borderColor='#7c5cbf'"
                    onblur="this.style.borderColor='#e2e8f0'"
                >
            </div>

            {{-- Área mínima --}}
            <div style="margin-bottom:18px">
                <label style="font-size:11px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:8px">
                    Área mínima (m²)
                </label>
                <input
                    type="number"
                    name="area_min"
                    value="{{ $areaMin }}"
                    placeholder="Ej: 100"
                    min="0"
                    style="
                        width:100%; padding:9px 10px;
                        border:1.5px solid #e2e8f0; border-radius:8px;
                        font-size:13px; font-family:inherit; color:#0f172a;
                        background:#f8fafc; outline:none;
                        transition:border-color .15s;
                    "
                    onfocus="this.style.borderColor='#7c5cbf'"
                    onblur="this.style.borderColor='#e2e8f0'"
                >
            </div>

            <button type="submit" style="
                width:100%; padding:10px;
                background:#7c5cbf; color:#fff;
                border:none; border-radius:8px;
                font-size:13px; font-weight:600; font-family:inherit;
                cursor:pointer; transition:background .15s;
            "
            onmouseover="this.style.background='#6547a8'"
            onmouseout="this.style.background='#7c5cbf'">
                Aplicar filtros
            </button>
        </form>
    </div>

    {{-- RESULTADOS --}}
    <div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px">
            <div>
                <span style="font-size:14px;font-weight:600;color:#0f172a">Resultados</span>
                <span style="font-size:13px;color:#64748b;margin-left:6px">
                    ({{ $propiedades->count() }} encontradas)
                </span>
            </div>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
                @if($q)
                <span style="display:inline-flex;align-items:center;gap:5px;background:#ede9fe;color:#5b21b6;font-size:11px;font-weight:500;padding:4px 10px;border-radius:20px">
                    <i class="ti ti-search" style="font-size:12px"></i>
                    "{{ $q }}"
                </span>
                @endif
                @if($tipo !== 'Todas')
                <span style="display:inline-flex;align-items:center;gap:5px;background:#ede9fe;color:#5b21b6;font-size:11px;font-weight:500;padding:4px 10px;border-radius:20px">
                    <i class="ti ti-tag" style="font-size:12px"></i>
                    {{ $tipo }}
                </span>
                @endif
            </div>
        </div>

        @forelse($propiedades as $p)
        <div class="card" style="margin-bottom:12px;transition:box-shadow .2s"
             onmouseover="this.style.boxShadow='0 4px 20px rgba(124,92,191,0.12)'"
             onmouseout="this.style.boxShadow=''">
            <div class="flex flex-col sm:flex-row gap-0 overflow-hidden rounded-xl">

                <div class="w-full sm:w-48 flex-shrink-0 relative" style="background:{{ $p->tipo==='Venta' ? '#1e3a5f' : ($p->tipo==='Alquiler' ? '#0f4c35' : '#2e1a5f') }};">
                    @if($p->imagen)
                        <img src="{{ asset('storage/' . $p->imagen) }}" alt="{{ $p->titulo }}" class="w-full h-48 object-cover rounded-t-lg sm:h-full sm:rounded-l-lg sm:rounded-tr-none">
                    @else
                        <i class="ti ti-building-estate" style="font-size:32px;color:rgba(255,255,255,0.2)"></i>
                    @endif
                    <span style="
                        position:absolute; bottom:8px; left:8px;
                        font-size:10px; font-weight:600; padding:3px 8px; border-radius:20px;
                        {{ $p->tipo==='Venta' ? 'background:#dbeafe;color:#1e40af' : ($p->tipo==='Alquiler' ? 'background:#d1fae5;color:#065f46' : 'background:#ede9fe;color:#5b21b6') }}
                    ">{{ $p->tipo }}</span>
                </div>

                <div class="flex-1 p-4 flex flex-col justify-between gap-3 sm:gap-4">
                    <div style="flex:1;min-width:150px">
                        <p style="font-size:14px;font-weight:600;color:#0f172a;margin-bottom:3px">
                            {{ $p->titulo }}
                        </p>
                        <p style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:4px;margin-bottom:4px">
                            <i class="ti ti-map-pin" style="font-size:13px"></i>
                            {{ $p->zona }}
                        </p>
                        {{-- Asistente puede ver el agente pero no editar --}}
                        <p style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:4px;margin-bottom:8px">
                            <i class="ti ti-user" style="font-size:13px"></i>
                            {{ $p->agente?->nombre ?? 'Sin agente' }}
                        </p>
                        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                            @if($p->area)
                            <span style="font-size:11px;color:#64748b;display:flex;align-items:center;gap:4px">
                                <i class="ti ti-ruler" style="font-size:13px"></i>
                                {{ $p->area }} m²
                            </span>
                            @endif
                            <span style="
                                font-size:11px; font-weight:600; padding:2px 9px; border-radius:20px;
                                {{ $p->estado==='Disponible' ? 'background:#d1fae5;color:#065f46' : ($p->estado==='Reservado' ? 'background:#fef3c7;color:#92400e' : 'background:#fee2e2;color:#991b1b') }}
                            ">{{ $p->estado }}</span>
                        </div>
                    </div>

                    <div style="text-align:right;flex-shrink:0">
                        <p style="font-size:18px;font-weight:700;color:#7c5cbf;margin-bottom:10px">
                            ${{ number_format($p->precio,0,',','.') }}
                        </p>
                        {{-- ASISTENTE: solo puede agendar visita, NO editar ni eliminar --}}
                        @if($p->estado === 'Disponible')
                        <a href="#" onclick="agendarVisita({{ $p->id }}, '{{ addslashes($p->titulo) }}')" style="
                            display:inline-flex; align-items:center; gap:6px;
                            padding:8px 16px;
                            background:#7c5cbf; color:#fff;
                            border-radius:8px; font-size:12px; font-weight:600;
                            text-decoration:none; transition:background .15s; cursor:pointer;
                        "
                        onmouseover="this.style.background='#6547a8'"
                        onmouseout="this.style.background='#7c5cbf'">
                            <i class="ti ti-calendar-plus" style="font-size:14px"></i>
                            Agendar visita
                        </a>
                        @else
                        <span style="
                            display:inline-flex; align-items:center; gap:6px;
                            padding:8px 16px;
                            background:#f1f5f9; color:#94a3b8;
                            border-radius:8px; font-size:12px; font-weight:600;
                        ">
                            <i class="ti ti-calendar-off" style="font-size:14px"></i>
                            No disponible
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="card" style="padding:48px 24px;text-align:center">
            <div style="width:64px;height:64px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                <i class="ti ti-building-off" style="font-size:28px;color:#94a3b8"></i>
            </div>
            <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:6px">
                No se encontraron propiedades
            </p>
            <p style="font-size:13px;color:#64748b;margin-bottom:20px">
                Intenta con otros términos o ajusta los filtros
            </p>
            <a href="{{ route('asistente.buscar') }}" style="
                display:inline-flex; align-items:center; gap:6px;
                padding:9px 20px; background:#7c5cbf; color:#fff;
                border-radius:8px; font-size:13px; font-weight:600;
                text-decoration:none;
            ">
                <i class="ti ti-refresh" style="font-size:15px"></i>
                Ver todas las propiedades
            </a>
        </div>
        @endforelse
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
@media (max-width: 768px) {
    #panelFiltros { display: none; }
    #panelFiltros.abierto { display: block; }
}
</style>
@endpush

@push('scripts')
<script>
function agendarVisita(propiedadId, titulo) {
    // Redirige a la página de visitas con la propiedad preseleccionada
    window.location.href = '{{ route("asistente.visitas") }}?propiedad_id=' + propiedadId;
}
</script>
@endpush