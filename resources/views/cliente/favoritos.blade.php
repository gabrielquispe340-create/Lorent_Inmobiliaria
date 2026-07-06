@extends('layouts.panel')
@section('titulo','Mis Favoritos')
@section('titulo_pagina','Mis Favoritos')

@section('contenido')

<div class="card">
    <div class="card-header">
        <span class="card-title">
            Propiedades favoritas
            <span style="font-size:12px;color:#6c757d;font-weight:400;margin-left:6px">({{ $favoritos->count() }} propiedades)</span>
        </span>
    </div>

    @if($favoritos->isEmpty())
        <p style="color:#8a94a6;padding:36px 0;text-align:center;font-size:14px;">
            Aún no tienes propiedades favoritas.
        </p>
    @else
    <div class="prop-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($favoritos as $p)
        <div class="prop-card" style="position:relative;overflow:hidden;">
            @include('compartido.badges', ['badges' => $p->badges, 'propiedad' => $p])
            <div class="prop-img prop-img-{{ strtolower($p->tipo) }}" style="position:relative;">
                @if($p->imagen)
                    <img src="{{ asset('storage/' . $p->imagen) }}" alt="{{ $p->titulo }}" style="width:100%;height:100%;object-fit:cover;display:block">
                @else
                    <span class="prop-img-placeholder">Sin foto</span>
                @endif
                <span class="prop-tag tag-{{ strtolower($p->tipo) }}">{{ $p->tipo }}</span>
                <form method="POST" action="{{ route('cliente.favoritos.destroy', $p->id) }}" style="position:absolute;bottom:8px;left:8px;z-index:5;margin:0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Quitar de favoritos" aria-label="Quitar de favoritos" style="background:none;border:none;cursor:pointer;font-size:24px;line-height:1;padding:2px;text-shadow:0 1px 4px rgba(0,0,0,0.4);transition:transform 0.15s;">
                        <span style="color:#ef4444;">❤️</span>
                    </button>
                </form>
            </div>
            <div class="prop-body">
                <p class="prop-title">{{ $p->titulo }}</p>
                <p class="prop-zona">{{ $p->zona }}</p>
                @if($p->resenas_count > 0)
                <div style="display:flex;align-items:center;gap:4px;margin-bottom:8px;">
                    @for($i = 1; $i <= 5; $i++)
                        <span style="font-size:14px;{{ $i <= round($p->resenas_avg_puntuacion) ? 'color:#f5b342;' : 'color:#ddd;' }}">★</span>
                    @endfor
                    <span style="font-size:11px;color:#8a94a6;margin-left:2px">({{ $p->resenas_count }})</span>
                </div>
                @endif
                <div class="prop-footer">
                    <div>
                        <p class="prop-price">${{ number_format($p->precio,0,',','.') }}</p>
                        <p class="prop-area">{{ $p->area ? $p->area.' m²' : '—' }}</p>
                    </div>
                    <a href="{{ route('cliente.propiedades.detalle', $p) }}" class="btn-detalle">Ver detalle</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@endsection
