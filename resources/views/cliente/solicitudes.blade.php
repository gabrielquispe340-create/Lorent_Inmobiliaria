@extends('layouts.panel')
@section('titulo','Mis solicitudes')
@section('titulo_pagina','Mis solicitudes de visita')

@push('styles')
<style>
table{width:100%;border-collapse:collapse;font-size:13px}
th{text-align:left;color:#6c757d;font-weight:500;padding:10px 14px;border-bottom:2px solid #e2e6ea;background:#f8f9fa;white-space:nowrap}
td{padding:11px 14px;border-bottom:1px solid #f0f2f5;vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:#f8f9fa}
</style>
@endpush

@section('contenido')
<div class="card">
    <div class="card-header">
        <span class="card-title">Historial de solicitudes</span>
        <a href="{{ route('cliente.propiedades') }}" class="btn-primary">+ Nueva solicitud</a>
    </div>
    <div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
        <table class="w-full text-sm text-left">
            <thead>
                <tr>
                    <th>Propiedad</th>
                    <th class="hidden sm:table-cell">Zona</th>
                    <th class="hidden sm:table-cell">Tipo</th>
                    <th>Fecha</th>
                    <th class="hidden md:table-cell">Mensaje</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($solicitudes as $s)
            @php
                $cls = match($s->estado) {
                    'Aceptada'   => 'badge-disponible',
                    'Completada' => 'badge-disponible',
                    'Rechazada'  => 'badge-vendido',
                    'Pendiente'  => 'badge-reservado',
                    default      => 'badge-reservado'
                };
                $label = $s->estado === 'Rechazada' ? 'Cancelada' : $s->estado;
            @endphp
            <tr>
                <td>{{ $s->propiedad->titulo ?? '—' }}</td>
                <td class="hidden sm:table-cell">{{ $s->propiedad->zona ?? '—' }}</td>
                <td class="hidden sm:table-cell">{{ $s->propiedad->tipo ?? '—' }}</td>
                <td>{{ \Carbon\Carbon::parse($s->fecha_solicitada)->format('d/m/Y') }}</td>
                <td class="hidden md:table-cell" style="max-width:200px;color:#6c757d">{{ Str::limit($s->mensaje, 60) }}</td>
                <td><span class="badge {{ $cls }}">{{ $label }}</span></td>
                <td>
                    @if($s->estado === 'Pendiente')
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                        <button
                            type="button"
                            class="btn-reagendar-solicitud"
                            data-id="{{ $s->id }}"
                            data-fecha="{{ \Carbon\Carbon::parse($s->fecha_solicitada)->format('Y-m-d') }}"
                            data-titulo="{{ $s->propiedad->titulo ?? 'esta solicitud' }}"
                            style="background:#e0f2fe;color:#0369a1;border:1px solid #7dd3fc;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;"
                        >Reagendar</button>
                        <form method="POST" action="{{ route('cliente.solicitudes.cancelar', $s->id) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <button
                                type="button"
                                class="btn-cancelar-solicitud"
                                data-id="{{ $s->id }}"
                                data-titulo="{{ $s->propiedad->titulo ?? 'esta solicitud' }}"
                                style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;"
                            >Cancelar</button>
                        </form>
                    </div>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;color:#6c757d;padding:30px">
                    No tienes solicitudes aún.
                    <a href="{{ route('cliente.propiedades') }}" style="color:#185FA5;margin-left:6px">Ver propiedades →</a>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL CONFIRMAR CANCELACIÓN --}}
<div id="cancelOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:36px 32px;max-width:420px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
        <div style="width:72px;height:72px;border-radius:50%;background:#e8f0fe;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <span style="font-size:36px;">🏠</span>
        </div>
        <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:10px;">Cancelar solicitud</h2>
        <p id="cancelMsg" style="font-size:14px;color:#64748b;margin-bottom:28px;line-height:1.6;"></p>
        <div style="display:flex;gap:12px;justify-content:center;">
            <button onclick="cerrarCancelModal()" style="padding:10px 24px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;font-size:14px;font-weight:500;cursor:pointer;">Volver</button>
            <button id="btnConfirmarCancelar" style="padding:10px 24px;border:none;border-radius:8px;background:#dc2626;color:#fff;font-size:14px;font-weight:600;cursor:pointer;">Sí, cancelar</button>
        </div>
    </div>
</div>

{{-- MODAL REAGENDAR --}}
<div id="reagendarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:36px 32px;max-width:420px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
        <div style="width:72px;height:72px;border-radius:50%;background:#e0f2fe;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <span style="font-size:36px;">📅</span>
        </div>
        <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:6px;">Reagendar visita</h2>
        <p id="reagendarMsg" style="font-size:13px;color:#64748b;margin-bottom:20px;"></p>
        <form id="formReagendar" method="POST" action="#">
            @csrf @method('PATCH')
            <div style="text-align:left;margin-bottom:20px;">
                <label style="font-size:12px;color:#6c757d;font-weight:500;display:block;margin-bottom:6px;">Nueva fecha</label>
                <input type="date" name="fecha_solicitada" id="inputNuevaFecha"
                    min="{{ date('Y-m-d') }}" required
                    style="width:100%;padding:9px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;">
            </div>
            <div style="display:flex;gap:12px;justify-content:center;">
                <button type="button" onclick="cerrarReagendarModal()"
                    style="padding:10px 24px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;font-size:14px;font-weight:500;cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit"
                    style="padding:10px 24px;border:none;border-radius:8px;background:#0369a1;color:#fff;font-size:14px;font-weight:600;cursor:pointer;">
                    Confirmar fecha
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── CANCELAR ──────────────────────────────────────────
let pendingCancelForm = null;

document.querySelectorAll('.btn-cancelar-solicitud').forEach(btn => {
    btn.addEventListener('click', function() {
        pendingCancelForm = this.closest('form');
        document.getElementById('cancelMsg').textContent =
            `¿Seguro que deseas cancelar la solicitud para "${this.dataset.titulo}"? Esta acción no se puede deshacer.`;
        document.getElementById('cancelOverlay').style.display = 'flex';
    });
});

function cerrarCancelModal() {
    document.getElementById('cancelOverlay').style.display = 'none';
    pendingCancelForm = null;
}

document.getElementById('btnConfirmarCancelar').addEventListener('click', function() {
    if (pendingCancelForm) pendingCancelForm.submit();
});

document.getElementById('cancelOverlay').addEventListener('click', function(e) {
    if (e.target === this) cerrarCancelModal();
});

// ── REAGENDAR ─────────────────────────────────────────
document.querySelectorAll('.btn-reagendar-solicitud').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('reagendarMsg').textContent =
            `Selecciona una nueva fecha para la visita a "${this.dataset.titulo}".`;
        document.getElementById('inputNuevaFecha').value = this.dataset.fecha;
        document.getElementById('formReagendar').action =
            `/cliente/solicitudes/${this.dataset.id}/reagendar`;
        document.getElementById('reagendarOverlay').style.display = 'flex';
    });
});

function cerrarReagendarModal() {
    document.getElementById('reagendarOverlay').style.display = 'none';
}

document.getElementById('reagendarOverlay').addEventListener('click', function(e) {
    if (e.target === this) cerrarReagendarModal();
});
</script>
@endpush