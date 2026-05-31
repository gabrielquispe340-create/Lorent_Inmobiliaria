@extends('layouts.panel')
@section('titulo','Mis propiedades')
@section('titulo_pagina','Mis propiedades')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('contenido')
<div class="card">
    <div class="card-header">
        <span class="card-title">Lista de mis propiedades</span>
        <button class="btn-primary" onclick="abrirModal()">+ Registrar propiedad</button>
    </div>
<div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
        <thead><tr><th>#</th><th>Título</th><th>Tipo</th><th>Zona</th><th>Precio</th><th>Área</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody>
        @forelse($propiedades as $p)
        <tr>
            <td>{{ $p->id }}</td>
            <td>{{ $p->titulo }}</td>
            <td>{{ $p->tipo }}</td>
            <td>{{ $p->zona }}</td>
            <td>${{ number_format($p->precio,0,',','.') }}</td>
            <td>{{ $p->area ? $p->area.' m²' : '—' }}</td>
            <td><span class="badge badge-{{ strtolower($p->estado) }}">{{ $p->estado }}</span></td>
            <td>
                <div class="action-btns">
                    <button
                        type="button"
                        class="btn-edit btn-editar-propiedad w-full sm:w-auto"
                        data-id="{{ $p->id }}"
                        data-titulo="{{ $p->titulo }}"
                        data-tipo="{{ $p->tipo }}"
                        data-zona="{{ $p->zona }}"
                        data-precio="{{ $p->precio }}"
                        data-area="{{ $p->area }}"
                        data-descripcion="{{ $p->descripcion }}"
                        data-estado="{{ $p->estado }}"
                        data-imagen="{{ $p->imagen }}"
                        data-lat="{{ $p->latitud }}"
                        data-lng="{{ $p->longitud }}">
                        Editar
                    </button>
                    <form method="POST"
                        action="{{ route('admin.propiedades.destroy', $p) }}"
                        class="form-eliminar"
                        data-title="{{ $p->titulo }}">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                                class="btn-delete open-delete-modal w-full sm:w-auto"
                                data-name="{{ $p->titulo }}">
                            Eliminar
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;color:#6c757d;padding:20px">No tienes propiedades registradas.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px">
    <strong>Errores de validación:</strong>
    <ul style="margin:6px 0 0 18px;padding:0">
        @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="modal-overlay" id="modalOverlay">
<div class="modal w-[95%] max-w-lg mx-auto sm:w-full">
    <h2 id="modalTitulo">Registrar propiedad</h2>
    <form id="formPropiedad" method="POST" action="{{ route('agente.propiedades.store') }}" enctype="multipart/form-data">
        @csrf <span id="methodField"></span>
        <div class="form-grid">
            <div class="form-group full"><label>Título</label><input type="text" name="titulo" id="propTitulo" required></div>
            <div class="form-group"><label>Tipo</label>
                <select name="tipo" id="propTipo">
                    <option value="Venta">Venta</option><option value="Alquiler">Alquiler</option><option value="Anticretico">Anticretico</option>
                </select>
            </div>
            <div class="form-group"><label>Zona</label><input type="text" name="zona" id="propZona" required></div>
            <div class="form-group"><label>Precio ($)</label><input type="number" name="precio" id="propPrecio" step="0.01" min="0" required></div>
            <div class="form-group"><label>Área (m²)</label><input type="number" name="area" id="propArea" step="0.01" min="0" required></div>
            <div class="form-group"><label>Estado</label>
                <select name="estado" id="propEstado">
                    <option value="Disponible">Disponible</option><option value="Reservado">Reservado</option><option value="Vendido">Vendido</option>
                </select>
            </div>
            <div class="form-group full"><label>Descripción</label><textarea name="descripcion" id="propDescripcion" rows="3" required></textarea></div>
            <div class="form-group full">
                <label>Imagen <small style="color:#6c757d;font-weight:400">(dejar vacío para mantener la actual)</small></label>
                <img id="aImgActual" src="" alt="Foto actual" style="display:none;max-height:100px;border-radius:6px;object-fit:cover;margin-bottom:6px;">
                <input type="file" name="imagen" id="aImagen" accept="image/jpeg,image/png,image/jpg,image/webp">
                <img id="aPreview" src="" alt="Vista previa" style="display:none;margin-top:8px;max-height:100px;border-radius:6px;object-fit:cover">
            </div>

            {{-- MAPA --}}
            <div class="form-group full">
                <label>Ubicación en el mapa <small style="color:#6c757d;font-weight:400">(clic para marcar)</small></label>
                <div style="display:flex;gap:8px;margin-bottom:8px;">
                    <input type="text" id="aMapBuscar" placeholder="Ej: Plan 3000, Santa Cruz"
                        style="flex:1;padding:8px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;">
                    <button type="button" onclick="buscarUbicacionA()"
                        style="padding:8px 14px;background:#185FA5;color:#fff;border:none;border-radius:6px;font-size:13px;cursor:pointer;">
                        Buscar
                    </button>
                </div>
                <div id="mapaAgente" style="height:260px;border-radius:8px;border:1px solid #dee2e6;"></div>
                <input type="hidden" name="latitud"  id="aLatitud">
                <input type="hidden" name="longitud" id="aLongitud">
                <p id="aCoordsTexto" style="font-size:11px;color:#6c757d;margin-top:5px;"></p>
            </div>

        </div>
        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
            <button type="submit" class="btn-primary" id="btnSubmit">Registrar</button>
        </div>
    </form>
</div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const SCZ = [-17.7833, -63.1822];

const overlay = document.getElementById('modalOverlay');
const form    = document.getElementById('formPropiedad');

function abrirModal() {
    form.reset();
    form.action = '{{ route("agente.propiedades.store") }}';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('modalTitulo').textContent = 'Registrar propiedad';
    document.getElementById('btnSubmit').textContent = 'Registrar';
    overlay.classList.add('open');
    setTimeout(() => {
        iniciarMapaAgente();
        setTimeout(() => { if (mapaA) mapaA.invalidateSize(); }, 300);
    }, 500);
}

function cerrarModal() { overlay.classList.remove('open'); }

overlay.addEventListener('click', e => { if (e.target === overlay) cerrarModal(); });

function editarPropiedad(id, titulo, tipo, zona, precio, area, descripcion, estado, imagen, lat, lng) {
    form.action = `/agente/propiedades/${id}`;
    document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('propTitulo').value      = titulo;
    document.getElementById('propTipo').value        = tipo;
    document.getElementById('propZona').value        = zona;
    document.getElementById('propPrecio').value      = precio;
    document.getElementById('propArea').value        = area;
    document.getElementById('propDescripcion').value = descripcion;
    document.getElementById('propEstado').value      = estado;

    const imgActual = document.getElementById('aImgActual');
    if (imagen) { imgActual.src = '/storage/' + imagen; imgActual.style.display = 'block'; }
    else        { imgActual.src = ''; imgActual.style.display = 'none'; }
    document.getElementById('aPreview').style.display = 'none';
    document.getElementById('aImagen').value = '';

    document.getElementById('modalTitulo').textContent = 'Editar propiedad';
    document.getElementById('btnSubmit').textContent   = 'Guardar cambios';
    overlay.classList.add('open');

    iniciarMapaAgente(lat ? parseFloat(lat) : null, lng ? parseFloat(lng) : null);
}

document.getElementById('aImagen').addEventListener('change', function() {
    const prev = document.getElementById('aPreview');
    if (this.files && this.files[0]) {
        prev.src = URL.createObjectURL(this.files[0]);
        prev.style.display = 'block';
    }
});

document.querySelectorAll('.btn-editar-propiedad').forEach(btn => {
    btn.addEventListener('click', function() {
        editarPropiedad(
            this.dataset.id,
            this.dataset.titulo,
            this.dataset.tipo,
            this.dataset.zona,
            this.dataset.precio,
            this.dataset.area,
            this.dataset.descripcion,
            this.dataset.estado,
            this.dataset.imagen,
            this.dataset.lat,
            this.dataset.lng
        );
    });
});

// ── MAPA AGENTE ───────────────────────────────────────
let mapaA = null, marcadorA = null;

function iniciarMapaAgente(lat = null, lng = null) {
    const centro = (lat && lng) ? [lat, lng] : SCZ;
    const zoom   = (lat && lng) ? 16 : 13;

    setTimeout(() => {
        if (mapaA) { mapaA.remove(); mapaA = null; marcadorA = null; }
        mapaA = L.map('mapaAgente').setView(centro, zoom);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            { attribution: '© OpenStreetMap' }).addTo(mapaA);
        if (lat && lng) colocarMarcadorA(lat, lng);
        mapaA.on('click', e => colocarMarcadorA(e.latlng.lat, e.latlng.lng));
        setTimeout(() => mapaA.invalidateSize(), 200);
    }, 500);
}

function colocarMarcadorA(lat, lng) {
    if (marcadorA) mapaA.removeLayer(marcadorA);
    marcadorA = L.marker([lat, lng], { draggable: true }).addTo(mapaA);
    marcadorA.on('dragend', () => {
        const p = marcadorA.getLatLng();
        guardarCoordsA(p.lat, p.lng);
    });
    guardarCoordsA(lat, lng);
}

function guardarCoordsA(lat, lng) {
    document.getElementById('aLatitud').value  = lat.toFixed(7);
    document.getElementById('aLongitud').value = lng.toFixed(7);
    document.getElementById('aCoordsTexto').textContent =
        `📍 ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
}

function buscarUbicacionA() {
    const q = document.getElementById('aMapBuscar').value.trim();
    if (!q) return;
    fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=1`)
        .then(r => r.json()).then(data => {
            if (data.length) {
                const lat = parseFloat(data[0].lat), lng = parseFloat(data[0].lon);
                mapaA.setView([lat, lng], 16);
                colocarMarcadorA(lat, lng);
            } else alert('Ubicación no encontrada.');
        });
}
</script>
@endpush