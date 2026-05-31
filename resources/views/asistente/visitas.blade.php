@extends('layouts.panel')
@section('titulo','Visitas')
@section('titulo_pagina','Agenda de visitas')

@section('contenido')
<div class="card">
    <div class="card-header">
        <span class="card-title">
            Todas las solicitudes de visita
            <span style="font-size:12px;color:#8a94a6;font-weight:400;margin-left:6px">
                ({{ $solicitudes->count() }} registros)
            </span>
        </span>
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
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
            @forelse($solicitudes as $s)
            @php
                $cls = match($s->estado) {
                    'Aceptada'  => 'badge-disponible',
                    'Rechazada' => 'badge-vendido',
                    default     => 'badge-reservado'
                };
            @endphp
            <tr>
                <td>
                    <div class="agent-cell">
                        <div class="agent-avatar">
                            {{ strtoupper(substr($s->cliente->nombre ?? 'CL', 0, 2)) }}
                        </div>
                        {{ $s->cliente->nombre ?? '—' }}
                    </div>
                </td>
                <td>{{ $s->propiedad->titulo ?? '—' }}</td>
                <td style="white-space:nowrap;font-size:12px">
                    {{ \Carbon\Carbon::parse($s->fecha_solicitada)->format('d/m/Y') }}
                </td>
                <td>
                    <span class="badge {{ $cls }}">{{ ucfirst($s->estado) }}</span>
                </td>
                <td>
                    @if($s->estado === 'Pendiente')
                    <div class="action-btns">
                        <form method="POST" action="{{ route('asistente.visitas.estado', $s) }}" style="display:inline">
                            @csrf @method('PUT')
                            <input type="hidden" name="estado" value="Aceptada">
                            <button type="submit" class="btn-edit">Confirmar</button>
                        </form>
                        <form method="POST" action="{{ route('asistente.visitas.estado', $s) }}" style="display:inline">
                            @csrf @method('PUT')
                            <input type="hidden" name="estado" value="Rechazada">
                            <button type="submit" class="btn-delete">Cancelar</button>
                        </form>
                    </div>
                    @else
                    <span style="font-size:12px;color:#8a94a6">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;color:#8a94a6;padding:28px">
                    No hay solicitudes de visita.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
</div>
 </div>
</div>
@endsection