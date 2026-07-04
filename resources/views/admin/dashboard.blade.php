@extends('layouts.panel')
@section('titulo', 'Dashboard — Administrador')
@section('titulo_pagina', 'Dashboard')

@section('contenido')

<!-- Botón flotante "Comando con vos" (visible en móvil y escritorio) -->
<button id="openComandoBtn" title="Comando con vos"
        class="fixed bottom-6 right-6 md:bottom-8 md:right-8 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full p-3 shadow-lg z-50">
    Con vos
</button>

<!-- Modal comando -->
<div id="comandoModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40 p-4">
    <div class="bg-white rounded-lg w-full max-w-2xl mx-auto p-4 md:p-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold">Comando con vos</h3>
            <button id="closeComandoModal" class="text-gray-600 text-2xl leading-none">×</button>
        </div>

        <form id="comandoForm" class="space-y-3">
            @csrf
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <textarea name="comando" id="comandoInput" rows="4" class="w-full border rounded p-2" placeholder="Escribe tu comando, por ejemplo: 'Generar reporte de propiedades'..."></textarea>

            <div class="flex items-center justify-between">
                <small id="comandoStatus" class="text-sm text-gray-500">Escribe un comando y presiona Enviar.</small>
                <div class="flex gap-2">
                    <button type="button" id="sendComandoBtn" class="btn-primary">Enviar</button>
                    <button type="button" id="cancelComandoBtn" class="btn-secondary">Cancelar</button>
                </div>
            </div>
        </form>

        <div id="comandoResponse" class="mt-4 hidden bg-gray-50 p-3 rounded border text-sm"></div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     TARJETAS DE ESTADÍSTICAS
════════════════════════════════════════ --}}
<div class="stats grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    {{-- Card 1: Total propiedades (destacada) --}}
    <div class="stat-card">
        <span class="badge-available">● Available</span>
        <p class="stat-label">Total propiedades</p>
        <p class="stat-value">{{ $totalProps }}</p>
        <span class="badge badge-green" style="margin-top:10px;display:inline-flex">
            {{ $disponibles }} disponibles
        </span>
        <span class="stat-icon">🏠</span>
    </div>

    {{-- Card 2: Propiedades vendidas --}}
    <div class="stat-card">
        <p class="stat-label">Propiedades vendidas</p>
        <p class="stat-value">{{ $totalVentas }}</p>
        <span class="badge" style="margin-top:10px;background:#fff3e0;color:#e65100;display:inline-flex">
            {{ $totalVentas > 0 ? $totalVentas : '0' }} en proceso
        </span>
        <span class="stat-icon">💰</span>
    </div>

    {{-- Card 3: Usuarios del sistema --}}
    <div class="stat-card">
        <p class="stat-label">Usuarios del sistema</p>
        <p class="stat-value">{{ $totalUsuarios }}</p>
        <span class="stat-icon">👥</span>
    </div>

</div>

{{-- ═══════════════════════════════════════
     ACTIVIDAD RECIENTE
════════════════════════════════════════ --}}
<div class="activity-card">
    <p class="section-title">Actividad reciente</p>

    <div class="activity-list">

        {{-- Propiedades registradas --}}
        <div class="activity-item">
            <div class="activity-icon blue">🏡</div>
            <div class="activity-body">
                <p class="activity-title">Propiedades registradas</p>
                @if($totalProps > 0)
                    <p class="activity-desc">
                        {{ $totalProps }} propiedad(es) registrada(s) en el sistema.
                    </p>
                @else
                    <p class="activity-desc">Sin propiedades registradas aún.</p>
                @endif
            </div>
        </div>

        {{-- Ventas registradas --}}
        <div class="activity-item">
            <div class="activity-icon gold">💲</div>
            <div class="activity-body">
                <p class="activity-title">Ventas registradas</p>
                <p class="activity-desc">{{ $totalVentas }} ventas registradas este mes.</p>
            </div>
        </div>

        {{-- Usuarios activos --}}
        <div class="activity-item">
            <div class="activity-icon purple">👤</div>
            <div class="activity-body">
                <p class="activity-title">Usuarios activos</p>
                <p class="activity-desc">{{ $totalUsuarios }} usuarios actualmente en el sistema.</p>
            </div>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════
     ÚLTIMAS PROPIEDADES REGISTRADAS
════════════════════════════════════════ --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Últimas propiedades registradas</span>
        <a href="{{ route('admin.propiedades') }}" class="btn-primary">Ver todas</a>
    </div>

    <div class="table-responsive">
<div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Zona</th>
                    <th>Tipo</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Agente</th>
                </tr>
            </thead>
            <tbody>
            @forelse($ultimas as $p)
            <tr>
                <td>{{ $p->titulo }}</td>
                <td>{{ $p->zona }}</td>
                <td>
                    <span class="badge badge-{{ strtolower($p->tipo) === 'alquiler' ? 'alquiler' : 'venta' }}">
                        {{ $p->tipo }}
                    </span>
                </td>
                <td>${{ number_format($p->precio, 0, ',', '.') }}</td>
                <td>
                    <span class="badge badge-{{ strtolower($p->estado) }}">
                        {{ $p->estado }}
                    </span>
                </td>
                <td>
                    <div class="agent-cell">
                        <div class="agent-avatar">
                            {{ strtoupper(substr($p->agente->nombre ?? 'SA', 0, 2)) }}
                        </div>
                        {{ $p->agente->nombre ?? 'Sin asignar' }}
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;color:#8a94a6;padding:28px">
                    No hay propiedades registradas aún.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
</div>
 </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const openBtn = document.getElementById('openComandoBtn');
    const modal = document.getElementById('comandoModal');
    const closeBtn = document.getElementById('closeComandoModal');
    const cancelBtn = document.getElementById('cancelComandoBtn');
    const sendBtn = document.getElementById('sendComandoBtn');
    const input = document.getElementById('comandoInput');
    const respDiv = document.getElementById('comandoResponse');
    const status = document.getElementById('comandoStatus');
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        input.focus();
        respDiv.classList.add('hidden');
        respDiv.textContent = '';
        status.textContent = 'Escribe un comando y presiona Enviar.';
    }

    function closeModal() {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    openBtn?.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    modal?.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    sendBtn?.addEventListener('click', async () => {
        const comando = input.value.trim();
        if (!comando) { status.textContent = 'Escribe un comando.'; input.focus(); return; }
        status.textContent = 'Enviando...';
        sendBtn.disabled = true;
        try {
            const res = await fetch("{{ route('admin.comando') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ comando })
            });

            const data = await res.json();

            if (res.ok) {
                respDiv.classList.remove('hidden');
                respDiv.textContent = data.response || data.message || 'Comando procesado.';
                status.textContent = 'Listo';
            } else {
                const err = data?.message || 'Error al procesar el comando.';
                status.textContent = err;
            }
        } catch (e) {
            status.textContent = 'Error de red.';
        } finally {
            sendBtn.disabled = false;
        }
    });


});
</script>
@endpush