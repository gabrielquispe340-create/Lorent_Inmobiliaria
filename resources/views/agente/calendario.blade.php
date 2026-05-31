@extends('layouts.panel')
@section('titulo','Calendario')
@section('titulo_pagina','Calendario de visitas')

@section('contenido')
<div class="card">

    {{-- Leyenda --}}
    <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #e2e6ea;">
        <span style="font-size:12px;font-weight:500;color:#6c757d;">Estado:</span>
        <span style="display:flex;align-items:center;gap:5px;font-size:12px;">
            <span style="width:12px;height:12px;border-radius:3px;background:#f59e0b;display:inline-block;"></span> Pendiente
        </span>
        <span style="display:flex;align-items:center;gap:5px;font-size:12px;">
            <span style="width:12px;height:12px;border-radius:3px;background:#10b981;display:inline-block;"></span> Aceptada
        </span>
        <span style="display:flex;align-items:center;gap:5px;font-size:12px;">
            <span style="width:12px;height:12px;border-radius:3px;background:#ef4444;display:inline-block;"></span> Rechazada
        </span>
        <span style="display:flex;align-items:center;gap:5px;font-size:12px;">
            <span style="width:12px;height:12px;border-radius:3px;background:#6366f1;display:inline-block;"></span> Completada
        </span>
    </div>

    {{-- Calendario --}}
    <div id="calendario"></div>
</div>

{{-- Modal detalle del evento --}}
<div id="eventoOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:32px;max-width:400px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h2 id="eventoTitulo" style="font-size:16px;font-weight:700;color:#0f172a;"></h2>
            <button onclick="cerrarEvento()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8;">✕</button>
        </div>
        <div style="display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;gap:10px;">
                <span style="font-size:13px;color:#6c757d;min-width:70px;">Estado</span>
                <span id="eventoEstado" style="font-size:13px;font-weight:600;"></span>
            </div>
            <div style="display:flex;gap:10px;">
                <span style="font-size:13px;color:#6c757d;min-width:70px;">Fecha</span>
                <span id="eventoFecha" style="font-size:13px;font-weight:500;"></span>
            </div>
            <div id="eventoClienteRow" style="display:flex;gap:10px;">
                <span style="font-size:13px;color:#6c757d;min-width:70px;">Cliente</span>
                <span id="eventoCliente" style="font-size:13px;font-weight:500;"></span>
            </div>
            <div style="display:flex;gap:10px;">
                <span style="font-size:13px;color:#6c757d;min-width:70px;">Zona</span>
                <span id="eventoZona" style="font-size:13px;font-weight:500;"></span>
            </div>
            <div style="display:flex;gap:10px;">
                <span style="font-size:13px;color:#6c757d;min-width:70px;">Mensaje</span>
                <span id="eventoMensaje" style="font-size:13px;color:#444;"></span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    const eventosUrl = '{{ route("agente.calendario.eventos") }}';

    const calendar = new FullCalendar.Calendar(document.getElementById('calendario'), {
        locale: 'es',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,timeGridWeek,listWeek'
        },
        buttonText: {
            today:    'Hoy',
            month:    'Mes',
            week:     'Semana',
            list:     'Lista'
        },
        events: eventosUrl,
        eventClick: function(info) {
            const p = info.event.extendedProps;

            document.getElementById('eventoTitulo').textContent  = info.event.title;
            document.getElementById('eventoFecha').textContent   = info.event.startStr;
            document.getElementById('eventoEstado').textContent  = p.estado;
            document.getElementById('eventoZona').textContent    = p.zona;
            document.getElementById('eventoMensaje').textContent = p.mensaje;

            const clienteRow = document.getElementById('eventoClienteRow');
            if (p.cliente) {
                document.getElementById('eventoCliente').textContent = p.cliente;
                clienteRow.style.display = 'flex';
            } else {
                clienteRow.style.display = 'none';
            }

            document.getElementById('eventoOverlay').style.display = 'flex';
        }
    });

    calendar.render();
});

function cerrarEvento() {
    document.getElementById('eventoOverlay').style.display = 'none';
}

document.getElementById('eventoOverlay').addEventListener('click', function(e) {
    if (e.target === this) cerrarEvento();
});
</script>
@endpush