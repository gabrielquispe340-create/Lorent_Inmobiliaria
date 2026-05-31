@extends('layouts.panel')
@section('titulo','Visitas')
@section('titulo_pagina','Solicitudes de visita')

@push('styles')
<style>
.filter-tag{display:inline-block;font-size:12px;padding:5px 14px;border:1px solid #dee2e6;border-radius:20px;color:#6c757d;margin-right:6px;transition:all 200ms}
.filter-tag:hover{border-color:#46A2FD;color:#185FA5}
.filter-tag.active{background:#E6F1FB;color:#185FA5;border-color:#85B7EB}
</style>
@endpush

@section('contenido')
<div class="card" style="margin-bottom:16px;padding:12px 18px">
    <div style="display:flex;gap:4px;align-items:center;flex-wrap:wrap">
        <span style="font-size:13px;color:#6c757d;margin-right:6px">Filtrar:</span>
        <a href="{{ route('agente.visitas') }}" class="filter-tag {{ $filtro=='todas'?'active':'' }}">Todas</a>
        <a href="{{ route('agente.visitas') }}?estado=pendiente" class="filter-tag {{ $filtro=='pendiente'?'active':'' }}">Pendientes</a>
        <a href="{{ route('agente.visitas') }}?estado=confirmada" class="filter-tag {{ $filtro=='confirmada'?'active':'' }}">Confirmadas</a>
        <a href="{{ route('agente.visitas') }}?estado=cancelada" class="filter-tag {{ $filtro=='cancelada'?'active':'' }}">Canceladas</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Visitas <span style="font-size:12px;color:#6c757d;font-weight:400">({{ $solicitudes->count() }} registros)</span></span>
    </div>
<div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
        <thead><tr><th>Propiedad</th><th>Cliente</th><th>Correo</th><th>Fecha</th><th>Estado</th><th>Acción</th></tr></thead>
        <tbody>
        @forelse($solicitudes as $v)
        @php $cls = match($v->estado){'Aceptada'=>'badge-disponible','Rechazada'=>'badge-vendido',default=>'badge-reservado'}; @endphp
        <tr>
            <td>{{ $v->propiedad->titulo ?? '—' }}</td>
            <td>{{ $v->cliente->nombre ?? '—' }}</td>
            <td style="font-size:12px;color:#6c757d">{{ $v->cliente->correo ?? '—' }}</td>
            <td>{{ $v->fecha_solicitada }}</td>
            <td><span class="badge {{ $cls }}">{{ ucfirst($v->estado) }}</span></td>
            <td>
                @if($v->estado === 'Pendiente')
                <div class="action-btns">
                    <form method="POST" action="{{ route('agente.visitas.estado',$v) }}" style="display:inline">
                        @csrf @method('PUT')
                        <input type="hidden" name="estado" value="Aceptada">
                        <button type="submit" class="btn-edit">Confirmar</button>
                    </form>
                    <form method="POST" action="{{ route('agente.visitas.estado',$v) }}" style="display:inline">
                        @csrf @method('PUT')
                        <input type="hidden" name="estado" value="Rechazada">
                        <button type="submit" class="btn-delete">Cancelar</button>
                    </form>
                </div>
                @else
                <span style="font-size:12px;color:#6c757d">—</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:#6c757d;padding:20px">No hay solicitudes.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

</div>
@endsection
