@extends('layouts.panel')
@section('titulo', 'Buscar propiedades')
@section('titulo_pagina', 'Mis propiedades')

@section('contenido')

{{-- ══════════ BUSCADOR PRINCIPAL ══════════ --}}
<div style="
    background: linear-gradient(135deg, #0f4c35 0%, #083326 100%);
    border-radius: 14px;
    padding: 28px 24px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
">
    <div style="position:absolute;top:-30px;right:-30px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,0.04);pointer-events:none"></div>
    <div style="position:absolute;bottom:-50px;right:80px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.03);pointer-events:none"></div>

    <p style="color:rgba(255,255,255,0.6);font-size:12px;margin-bottom:6px;position:relative">
        Panel Agente
    </p>
    <h2 style="color:#fff;font-size:20px;font-weight:700;margin-bottom:4px;position:relative">
        Buscar mis propiedades
    </h2>
    <p style="color:rgba(255,255,255,0.55);font-size:13px;margin-bottom:20px;position:relative">
        Tienes {{ $totalPropiedades }} propiedades registradas a tu nombre
    </p>

    <form method="GET" action="{{ route('agente.buscar') }}" id="formBuscar" style="position:relative">
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
                    onfocus="this.style.borderColor='rgba(99,247,160,0.8)'"
                    onblur="this.style.borderColor='rgba(255,255,255,0.12)'"
                >
            </div>
            <button type="submit" style="
                padding:12px 22px;
                background:#1a9e5c; color:#fff;
                border:none; border-radius:10px;
                font-size:13px; font-weight:600; font-family:inherit;
                cursor:pointer; transition:background .18s;
                display:flex; align-items:center; gap:7px; white-space:nowrap;
            "
            onmouseover="this.style.background='#138348'"
            onmouseout="this.style.background='#1a9e5c'">
                <i class="ti ti-search" style="font-size:15px"></i>
                Buscar
            </button>
            {{-- Botón nueva propiedad (comentado) --}}
            <button type="button" onclick="abrirModal()" style="
                padding:12px 18px;
                background:rgba(255,255,255,0.12); color:#fff;
                border:1.5px solid rgba(255,255,255,0.2); border-radius:10px;
                font-size:13px; font-weight:600; font-family:inherit;
                cursor:pointer; transition:background .18s;
                display:flex; align-items:center; gap:7px; white-space:nowrap;
            "
            onmouseover="this.style.background='rgba(255,255,255,0.2)'"
            onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                <i class="ti ti-plus" style="font-size:15px"></i>
                Nueva propiedad
            </button>
            @if($q || $tipo !== 'Todas' || $estado !== 'Todas' || $precioMax)
            <a href="{{ route('agente.buscar') }}" style="
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
                <i class="ti ti-adjustments-horizontal" style="font-size:16px;color:#1a9e5c"></i>
                Filtros
            </span>
        </div>

        <form method="GET" action="{{ route('agente.buscar') }}" style="padding:16px">
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
                        style="accent-color:#1a9e5c;width:15px;height:15px;cursor:pointer"
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
                @foreach(['Todas'=>'Todos','Disponible'=>'Disponible','Reservado'=>'Reservado','Vendido'=>'Vendido'] as $val => $label)
                <label style="display:flex;align-items:center;gap:8px;padding:6px 0;cursor:pointer">
                    <input
                        type="radio" name="estado" value="{{ $val }}"
                        {{ $estado === $val ? 'checked' : '' }}
                        style="accent-color:#1a9e5c;width:15px;height:15px;cursor:pointer"
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
                    onfocus="this.style.borderColor='#1a9e5c'"
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
                    onfocus="this.style.borderColor='#1a9e5c'"
                    onblur="this.style.borderColor='#e2e8f0'"
                >
            </div>

            <button type="submit" style="
                width:100%; padding:10px;
                background:#1a9e5c; color:#fff;
                border:none; border-radius:8px;
                font-size:13px; font-weight:600; font-family:inherit;
                cursor:pointer; transition:background .15s;
            "
            onmouseover="this.style.background='#138348'"
            onmouseout="this.style.background='#1a9e5c'">
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
                <span style="display:inline-flex;align-items:center;gap:5px;background:#d1fae5;color:#065f46;font-size:11px;font-weight:500;padding:4px 10px;border-radius:20px">
                    <i class="ti ti-search" style="font-size:12px"></i>
                    "{{ $q }}"
                </span>
                @endif
                @if($tipo !== 'Todas')
                <span style="display:inline-flex;align-items:center;gap:5px;background:#d1fae5;color:#065f46;font-size:11px;font-weight:500;padding:4px 10px;border-radius:20px">
                    <i class="ti ti-tag" style="font-size:12px"></i>
                    {{ $tipo }}
                </span>
                @endif
            </div>
        </div>

        @forelse($propiedades as $p)
        <div class="card" style="margin-bottom:12px;transition:box-shadow .2s"
             onmouseover="this.style.boxShadow='0 4px 20px rgba(26,158,92,0.12)'"
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
                        <p style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:4px;margin-bottom:8px">
                            <i class="ti ti-map-pin" style="font-size:13px"></i>
                            {{ $p->zona }}
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
                        <p style="font-size:18px;font-weight:700;color:#1a9e5c;margin-bottom:10px">
                            ${{ number_format($p->precio,0,',','.') }}
                        </p>
                        {{-- Agente: puede editar y eliminar sus propias propiedades --}}
                        <div class="flex flex-col sm:flex-row gap-2 justify-end w-full sm:w-auto mt-2">
                            <button
                                type="button"
                                onclick="editarPropiedad(
                                    {{ $p->id }},
                                    '{{ addslashes($p->titulo) }}',
                                    '{{ $p->tipo }}',
                                    '{{ addslashes($p->zona) }}',
                                    '{{ $p->precio }}',
                                    '{{ $p->area }}',
                                    '{{ addslashes($p->descripcion) }}',
                                    '{{ $p->estado }}',
                                    '{{ $p->agente_id }}',
                                    '{{ $p->imagen }}'
                                    )"
                                style="
                                    padding:7px 14px;
                                    background:#f1f5f9; color:#475569;
                                    border:1px solid #e2e8f0; border-radius:8px;
                                    font-size:12px; font-weight:600; font-family:inherit;
                                    cursor:pointer;
                                ">
                                <i class="ti ti-pencil" style="font-size:13px"></i>
                                Editar
                            </button>
                            <form method="POST"
                                  action="{{ route('agente.propiedades.destroy', $p) }}"
                                  class="form-eliminar"
                                  data-title="{{ $p->titulo }}"
                                  style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-delete open-delete-modal w-full sm:w-auto px-4 py-2 bg-red-100 text-red-800 border border-red-200 rounded-lg text-xs font-semibold cursor-pointer">
                                    <i class="ti ti-trash" style="font-size:13px"></i>
                                    Eliminar
                                </button>
                            </form>
                        </div>
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
            <a href="{{ route('agente.buscar') }}" style="
                display:inline-flex; align-items:center; gap:6px;
                padding:9px 20px; background:#1a9e5c; color:#fff;
                border-radius:8px; font-size:13px; font-weight:600;
                text-decoration:none;
            ">
                <i class="ti ti-refresh" style="font-size:15px"></i>
                Ver todas mis propiedades
            </a>
        </div>
        @endforelse
    </div>
</div>

{{-- MODAL EDITAR --}}
<div class="modal-overlay" id="modalOverlay">
    <div class="modal w-[95%] max-w-lg mx-auto sm:w-full">
        <h2 id="modalTitulo">Editar propiedad</h2>
        <form id="formEditar" method="POST" action="#" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-group full"><label>Título</label><input type="text" name="titulo" id="eTitulo" required></div>
                <div class="form-group"><label>Tipo</label>
                    <select name="tipo" id="eTipo">
                        <option value="Venta">Venta</option>
                        <option value="Alquiler">Alquiler</option>
                        <option value="Anticretico">Anticretico</option>
                    </select>
                </div>
                <div class="form-group"><label>Zona</label><input type="text" name="zona" id="eZona" required></div>
                <div class="form-group"><label>Precio ($)</label><input type="number" name="precio" id="ePrecio" step="0.01" min="0" required></div>
                <div class="form-group"><label>Área (m²)</label><input type="number" name="area" id="eArea" step="0.01" min="0"></div>
                <div class="form-group"><label>Estado</label>
                    <select name="estado" id="eEstado">
                        <option value="Disponible">Disponible</option>
                        <option value="Reservado">Reservado</option>
                        <option value="Vendido">Vendido</option>
                    </select>
                </div>
                <div class="form-group full"><label>Descripción</label><textarea name="descripcion" id="eDescripcion" rows="3" required></textarea></div>
                <div class="form-group full">
                    <label>Imagen <small style="color:#6c757d;font-weight:400">(dejar vacío para mantener la actual)</small></label>
                    <img id="cImgActual" src="" alt="Foto actual" style="display:none;max-height:80px;border-radius:6px;object-fit:cover;margin-bottom:6px">
                    <input type="file" name="imagen" id="cImagen" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <img id="cPreview" src="" alt="Vista previa" style="display:none;margin-top:6px;max-height:80px;border-radius:6px;object-fit:cover">
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
                <button type="button" class="btn-primary" onclick="guardarEdicion()">Guardar cambios</button>
            </div>
        </form>
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
const overlay = document.getElementById('modalOverlay');

function cerrarModal() { overlay.classList.remove('open'); }

overlay.addEventListener('click', e => { if (e.target === overlay) cerrarModal(); });

function editarPropiedad(id, titulo, tipo, zona, precio, area, descripcion, estado, imagen) {
    document.getElementById('formEditar').action = `/agente/propiedades/${id}`;
    document.getElementById('eTitulo').value      = titulo;
    document.getElementById('eTipo').value        = tipo;
    document.getElementById('eZona').value        = zona;
    document.getElementById('ePrecio').value      = precio;
    document.getElementById('eArea').value        = area;
    document.getElementById('eDescripcion').value = descripcion;
    document.getElementById('eEstado').value      = estado;
    const imgActual = document.getElementById('cImgActual');
    if (imagen) { imgActual.src = '/storage/' + imagen; imgActual.style.display = 'block'; }
    else { imgActual.src = ''; imgActual.style.display = 'none'; }
    document.getElementById('cPreview').style.display = 'none';
    document.getElementById('cImagen').value = '';
    overlay.classList.add('open');
}

function guardarEdicion() {
    const form = document.getElementById('formEditar');
    if (form.action && form.action !== '#' && form.action !== '') {
        form.submit();
    }
}

document.getElementById('cImagen').addEventListener('change', function(){
    const prev = document.getElementById('cPreview');
    if (this.files && this.files[0]) { prev.src = URL.createObjectURL(this.files[0]); prev.style.display = 'block'; }
});
</script>
@endpush