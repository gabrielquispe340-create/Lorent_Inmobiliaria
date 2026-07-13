@extends('layouts.panel')
@section('titulo', 'Mis Comentarios')
@section('titulo_pagina', 'Mis Comentarios')

@section('contenido')

<div class="card">
    <div class="card-header">
        <span class="card-title">
            Historial de comentarios
            <span style="font-size:12px;color:#6c757d;font-weight:400;margin-left:6px">({{ $resenas->count() }} comentarios)</span>
        </span>
    </div>

    @if($resenas->isEmpty())
        <p style="color:#8a94a6;padding:36px 0;text-align:center;font-size:14px;">
            Aún no has dejado ninguna reseña.
        </p>
    @else
        @foreach($resenas as $resena)
        <div style="padding:16px 0;border-bottom:1px solid #f0f2f5;display:flex;flex-direction:column;gap:8px;">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <a href="{{ route('cliente.propiedades.detalle', $resena->propiedad) }}" style="font-size:14px;font-weight:600;color:#0f4c75;text-decoration:none;">
                        {{ $resena->propiedad->titulo }}
                    </a>
                    <span style="font-size:11px;color:#8a94a6;">{{ $resena->propiedad->zona }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:6px;">
                    @for($i = 1; $i <= 5; $i++)
                        <span style="font-size:16px;{{ $i <= $resena->puntuacion ? 'color:#f5b342;' : 'color:#ddd;' }}">★</span>
                    @endfor
                    <span style="font-size:11px;color:#8a94a6;margin-left:4px">— {{ \Carbon\Carbon::parse($resena->fecha)->format('d/m/Y') }}</span>
                </div>
            </div>
            @if($resena->comentario)
                <p style="font-size:13px;color:#444;line-height:1.6;margin:0;">{{ $resena->comentario }}</p>
            @endif
        </div>
        @endforeach
    @endif
</div>

@endsection
