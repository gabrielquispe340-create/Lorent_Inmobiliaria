@extends('layouts.panel')
@section('titulo','Propiedades')
@section('titulo_pagina','Propiedades disponibles')

@push('styles')
<style>
.filter-tag{display:inline-block;font-size:12px;padding:5px 14px;border:1px solid #dee2e6;border-radius:20px;color:#6c757d;margin-right:6px;transition:all 200ms}
.filter-tag:hover{border-color:#64b5f6;color:#185FA5}
.filter-tag.active{background:#E6F1FB;color:#185FA5;border-color:#85B7EB}
</style>
@endpush

@section('contenido')


<div class="card" style="margin-bottom:16px;padding:12px 18px">
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <span style="font-size:13px;color:#6c757d;margin-right:4px">Filtrar:</span>
        <a href="{{ route('cliente.propiedades') }}" class="filter-tag {{ $filtro=='Todas'?'active':'' }}">Todas</a>
        <a href="{{ route('cliente.propiedades') }}?tipo=Venta" class="filter-tag {{ $filtro=='Venta'?'active':'' }}">Venta</a>
        <a href="{{ route('cliente.propiedades') }}?tipo=Alquiler" class="filter-tag {{ $filtro=='Alquiler'?'active':'' }}">Alquiler</a>
        <a href="{{ route('cliente.propiedades') }}?tipo=Anticretico" class="filter-tag {{ $filtro=='Anticretico'?'active':'' }}">Anticréticos</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">
            {{ $filtro === 'Todas' ? 'Todas las propiedades' : "Propiedades: $filtro" }}
            <span style="font-size:12px;color:#6c757d;font-weight:400;margin-left:6px">({{ $propiedades->count() }} encontradas)</span>
        </span>
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
        </div>
        <div class="prop-body">
            <p class="prop-title">{{ $p->titulo }}</p>
            <p class="prop-zona">{{ $p->zona }}</p>
            <div class="prop-footer">
                <div>
                    <p class="prop-price">${{ number_format($p->precio,0,',','.') }}</p>
                    <p class="prop-area">{{ $p->area ? $p->area.' m²' : '—' }}</p>
                </div>
                <a href="{{ route('cliente.propiedades.detalle',$p) }}" class="btn-detalle">Ver detalle</a>
            </div>
        </div>
    </div>
    @empty
    <p style="color:#6c757d;padding:20px 0;grid-column:1/-1;text-align:center">No hay propiedades disponibles.</p>
    @endforelse
    </div>
</div>
@endsection
