@extends('layouts.panel')

@section('titulo', 'Propiedades')
@section('titulo_pagina', 'Propiedades')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('contenido')

<div class="card">

    <div class="card-header">
        <span class="card-title">Lista de propiedades</span>

        <button class="btn-primary" onclick="abrirModal()">
            + Registrar propiedad
        </button>
    </div>
<div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">

        <thead>
            <tr>
                <th>#</th>
                <th>Título</th>
                <th>Tipo</th>
                <th>Zona</th>
                <th>Precio</th>
                <th>Área</th>
                <th>Estado</th>
                <th>Agente</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

        @forelse($propiedades as $p)

            <tr>

                <td>{{ $p->id }}</td>

                <td>{{ $p->titulo }}</td>

                <td>{{ $p->tipo }}</td>

                <td>{{ $p->zona }}</td>

                <td>
                    ${{ number_format($p->precio, 0, ',', '.') }}
                </td>

                <td>
                    {{ $p->area ? $p->area . ' m²' : '—' }}
                </td>

                <td>
                    <span class="badge badge-{{ strtolower($p->estado) }}">
                        {{ $p->estado }}
                    </span>
                </td>

                <td>
                    {{ $p->agente->nombre ?? 'Sin asignar' }}
                </td>

                <td>

                    <div class="action-btns flex flex-col sm:flex-row gap-2">

                        <button
    class="btn-edit w-full sm:w-auto"
    data-id="{{ $p->id }}"
    data-titulo="{{ $p->titulo }}"
    data-tipo="{{ $p->tipo }}"
    data-zona="{{ $p->zona }}"
    data-precio="{{ $p->precio }}"
    data-area="{{ $p->area }}"
    data-descripcion="{{ $p->descripcion }}"
    data-estado="{{ $p->estado }}"
    data-agente="{{ $p->agente_id }}"
    data-imagen="{{ $p->imagen }}"
    data-lat="{{ $p->latitud }}"
    data-lng="{{ $p->longitud }}"
>
    Editar
</button>

                        <form method="POST"
      action="{{ route('admin.propiedades.destroy', $p) }}"
      class="form-eliminar"
      data-title="{{ $p->titulo }}"
      style="display:inline">

    @csrf
    @method('DELETE')

    <button type="button" class="btn-delete open-delete-modal w-full sm:w-auto">
        Eliminar
    </button>

</form>
                        

                    </div>

                </td>

            </tr>

        @empty

            <tr>
                <td
                    colspan="9"
                    style="text-align:center;color:#6c757d;padding:20px"
                >
                    No hay propiedades registradas.
                </td>
            </tr>

        @endforelse

        </tbody>

    </table>
</div>

</div>

{{-- MODAL --}}

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

        <h2 id="modalTitulo">
            Registrar propiedad
        </h2>

        {{-- FORMULARIO REGISTRAR --}}

        <form
            id="formRegistrar"
            method="POST"
            action="{{ route('admin.propiedades.store') }}"
            enctype="multipart/form-data"
        >

            @csrf

            <div class="form-grid">

                <div class="form-group full">

                    <label>Título</label>

                    <input
                        type="text"
                        name="titulo"
                        id="rTitulo"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Tipo</label>

                    <select name="tipo" id="rTipo">
                        <option value="Venta">Venta</option>
                        <option value="Alquiler">Alquiler</option>
                        <option value="Anticretico">Anticretico</option>
                    </select>

                </div>

                <div class="form-group">

                    <label>Zona</label>

                    <input
                        type="text"
                        name="zona"
                        id="rZona"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Precio ($)</label>

                    <input
                        type="number"
                        name="precio"
                        id="rPrecio"
                        step="0.01"
                        min="0"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Área (m²)</label>

                    <input
                        type="number"
                        name="area"
                        id="rArea"
                        step="0.01"
                        min="0"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Estado</label>

                    <select name="estado" id="rEstado">
                        <option value="Disponible">Disponible</option>
                        <option value="Reservado">Reservado</option>
                        <option value="Vendido">Vendido</option>
                    </select>

                </div>

                <div class="form-group">

                    <label>Agente</label>

                    <select name="agente_id" id="rAgente">

                        <option value="">
                            Sin asignar
                        </option>

                        @foreach($agentes as $a)

                            <option value="{{ $a->id }}">
                                {{ $a->nombre }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="form-group full">

                    <label>Descripción</label>

                    <textarea
                        name="descripcion"
                        id="rDescripcion"
                        rows="3"
                        required
                    ></textarea>

                </div>

                <div class="form-group full">
                    <label>Imagen</label>
                    <input type="file" name="imagen" id="rImagen" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <img id="rPreview" src="" alt="Vista previa" style="display:none;margin-top:8px;max-height:100px;border-radius:6px;object-fit:cover">
                </div>
                {{-- MAPA REGISTRAR --}}
                <div class="form-group full">
                    <label>Ubicación en el mapa <small style="color:#6c757d;font-weight:400">(clic para marcar)</small></label>
                    <div style="display:flex;gap:8px;margin-bottom:8px;">
                        <input type="text" id="rMapBuscar" placeholder="Ej: Plan 3000, Santa Cruz"
                            style="flex:1;padding:8px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;">
                        <button type="button" onclick="buscarUbicacionR()"
                            style="padding:8px 14px;background:#185FA5;color:#fff;border:none;border-radius:6px;font-size:13px;cursor:pointer;">
                            Buscar
                        </button>
                    </div>
                    <div id="mapaRegistro" style="height:260px;border-radius:8px;border:1px solid #dee2e6;"></div>
                    <input type="hidden" name="latitud"  id="rLatitud">
                    <input type="hidden" name="longitud" id="rLongitud">
                    <p id="rCoordsTexto" style="font-size:11px;color:#6c757d;margin-top:5px;"></p>
                </div>

            </div>

            <div class="form-actions flex flex-col sm:flex-row gap-2 mt-4">

                <button
                    type="button"
                    class="btn-cancel"
                    onclick="cerrarModal()"
                >
                    Cancelar
                </button>

                <button type="submit" class="btn-primary">
                    Registrar
                </button>

            </div>

        </form>

        {{-- FORMULARIO EDITAR --}}

        <form
            id="formEditar"
            method="POST"
            style="display:none"
            enctype="multipart/form-data"
        >

            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-group full">

                    <label>Título</label>

                    <input
                        type="text"
                        name="titulo"
                        id="eTitulo"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Tipo</label>

                    <select name="tipo" id="eTipo">
                        <option value="Venta">Venta</option>
                        <option value="Alquiler">Alquiler</option>
                        <option value="Anticretico">Anticretico</option>
                    </select>

                </div>

                <div class="form-group">

                    <label>Zona</label>

                    <input
                        type="text"
                        name="zona"
                        id="eZona"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Precio ($)</label>

                    <input
                        type="number"
                        name="precio"
                        id="ePrecio"
                        step="0.01"
                        min="0"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Área (m²)</label>

                    <input
                        type="number"
                        name="area"
                        id="eArea"
                        step="0.01"
                        min="0"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Estado</label>

                    <select name="estado" id="eEstado">
                        <option value="Disponible">Disponible</option>
                        <option value="Reservado">Reservado</option>
                        <option value="Vendido">Vendido</option>
                    </select>

                </div>

                <div class="form-group">

                    <label>Agente</label>

                    <select name="agente_id" id="eAgente">

                        <option value="">
                            Sin asignar
                        </option>

                        @foreach($agentes as $a)

                            <option value="{{ $a->id }}">
                                {{ $a->nombre }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="form-group full">
                    <label>Imagen <small style="color:#6c757d;font-weight:400">(dejar vacío para mantener la actual)</small></label>
                    <img id="eImgActual" src="" alt="Foto actual" style="display:none;max-height:100px;border-radius:6px;object-fit:cover;margin-bottom:6px">
                    <input type="file" name="imagen" id="eImagen" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <img id="ePreview" src="" alt="Vista previa" style="display:none;margin-top:8px;max-height:100px;border-radius:6px;object-fit:cover">
                </div>

                <div class="form-group full">

                    <label>Descripción</label>

                    <textarea
                        name="descripcion"
                        id="eDescripcion"
                        rows="3"
                        required
                    ></textarea>

                </div>

                {{-- MAPA EDITAR --}}
                <div class="form-group full">
                    <label>Ubicación en el mapa <small style="color:#6c757d;font-weight:400">(clic para mover el marcador)</small></label>
                    <div style="display:flex;gap:8px;margin-bottom:8px;">
                        <input type="text" id="eMapBuscar" placeholder="Ej: Plan 3000, Santa Cruz"
                            style="flex:1;padding:8px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;">
                        <button type="button" onclick="buscarUbicacionE()"
                            style="padding:8px 14px;background:#185FA5;color:#fff;border:none;border-radius:6px;font-size:13px;cursor:pointer;">
                            Buscar
                        </button>
                    </div>
                    <div id="mapaEditar" style="height:260px;border-radius:8px;border:1px solid #dee2e6;"></div>
                    <input type="hidden" name="latitud"  id="eLatitud">
                    <input type="hidden" name="longitud" id="eLongitud">
                    <p id="eCoordsTexto" style="font-size:11px;color:#6c757d;margin-top:5px;"></p>
                </div>

            </div>

            <div class="form-actions flex flex-col sm:flex-row gap-2 mt-4">

                <button
                    type="button"
                    class="btn-cancel"
                    onclick="cerrarModal()"
                >
                    Cancelar
                </button>

                <button type="submit" class="btn-primary">
                    Guardar cambios
                </button>

            </div>

        </form>

    </div>

</div>



@endsection

@push('scripts')

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>     

<script>

const SCZ = [-17.7833, -63.1822]; // Coordenadas aproximadas del centro de Santa Cruz para centrar los mapas inicialmente

const overlay = document.getElementById('modalOverlay');

const formRegistrar = document.getElementById('formRegistrar');

const formEditar = document.getElementById('formEditar');

const titulo = document.getElementById('modalTitulo');

function abrirModal() {

    formRegistrar.reset();

    formRegistrar.style.display = 'block';

    formEditar.style.display = 'none';

    titulo.textContent = 'Registrar propiedad';

    overlay.classList.add('open');
}

function cerrarModal() {

    overlay.classList.remove('open');

    formRegistrar.reset();

    formEditar.reset();

    formRegistrar.style.display = 'block';

    formEditar.style.display = 'none';
}

overlay.addEventListener('click', function(e) {

    if (e.target === overlay) {

        cerrarModal();
    }
});

function editarPropiedad(
    id,
    t,
    tipo,
    zona,
    precio,
    area,
    desc,
    estado,
    agenteId,
    imagen,
    lat,
    lng
) {

    formEditar.action = `/admin/propiedades/${id}`;

    document.getElementById('eTitulo').value = t;
    document.getElementById('eTipo').value = tipo;
    document.getElementById('eZona').value = zona;
    document.getElementById('ePrecio').value = precio;
    document.getElementById('eArea').value = area;
    document.getElementById('eDescripcion').value = desc;
    document.getElementById('eEstado').value = estado;
    document.getElementById('eAgente').value = agenteId || '';

    const imgActual = document.getElementById('eImgActual');
    if (imagen) {
        imgActual.src = '/storage/' + imagen;
        imgActual.style.display = 'block';
    } else {
        imgActual.src = '';
        imgActual.style.display = 'none';
    }
    document.getElementById('ePreview').style.display = 'none';
    document.getElementById('eImagen').value = '';

    formRegistrar.style.display = 'none';

    formEditar.style.display = 'block';

    titulo.textContent = 'Editar propiedad';

    overlay.classList.add('open');

    iniciarMapaEditar(
        lat ? parseFloat(lat) : null,
        lng ? parseFloat(lng) : null
    );
}

// BOTONES EDITAR

document.querySelectorAll('.btn-edit')
.forEach(button => {

    button.addEventListener('click', function () {

        editarPropiedad(

            this.dataset.id,
            this.dataset.titulo,
            this.dataset.tipo,
            this.dataset.zona,
            this.dataset.precio,
            this.dataset.area,
            this.dataset.descripcion,
            this.dataset.estado,
            this.dataset.agente,
            this.dataset.imagen,    
            this.dataset.lat,   // ← nuevo
            this.dataset.lng    // ← nuevo
        );

    });

});

document.getElementById('rImagen').addEventListener('change', function(){
    const prev = document.getElementById('rPreview');
    if (this.files && this.files[0]) {
        prev.src = URL.createObjectURL(this.files[0]);
        prev.style.display = 'block';
    }
});

document.getElementById('eImagen').addEventListener('change', function(){
    const prev = document.getElementById('ePreview');
    if (this.files && this.files[0]) {
        prev.src = URL.createObjectURL(this.files[0]);
        prev.style.display = 'block';
    }
});

let pendingDeleteForm = null;

document.querySelectorAll('.open-delete-modal').forEach(btn => {
    btn.addEventListener('click', function() {
        const form  = this.closest('.form-eliminar');
        const title = form.dataset.title || 'este registro';
        pendingDeleteForm = form;
        document.getElementById('deleteMsg').textContent =
            `¿Seguro que deseas eliminar "${title}"? Esta acción no se puede deshacer.`;
        const overlay = document.getElementById('deleteOverlay');
        overlay.style.display = 'flex';
    });
});

function cerrarDeleteModal() {
    document.getElementById('deleteOverlay').style.display = 'none';
    pendingDeleteForm = null;
}

document.getElementById('btnConfirmarEliminar').addEventListener('click', function() {
    if (pendingDeleteForm) pendingDeleteForm.submit();
});

document.getElementById('deleteOverlay').addEventListener('click', function(e) {
    if (e.target === this) cerrarDeleteModal();
});

// ── LEAFLET ───────────────────────────────────────────
// Centro Santa Cruz

// ── MAPA REGISTRAR ────────────────────────────────────
let mapaR = null, marcadorR = null;

function iniciarMapaRegistro() {
    if (mapaR) return;
    setTimeout(() => {
        mapaR = L.map('mapaRegistro', { preferCanvas: true }).setView(SCZ, 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            { attribution: '© OpenStreetMap' }).addTo(mapaR);
        mapaR.on('click', e => colocarMarcadorR(e.latlng.lat, e.latlng.lng));
        setTimeout(() => mapaR.invalidateSize(), 200);
    }, 500);
}

function colocarMarcadorR(lat, lng) {
    if (marcadorR) mapaR.removeLayer(marcadorR);
    marcadorR = L.marker([lat, lng], { draggable: true }).addTo(mapaR);
    marcadorR.on('dragend', () => {
        const p = marcadorR.getLatLng();
        guardarCoordsR(p.lat, p.lng);
    });
    guardarCoordsR(lat, lng);
}

function guardarCoordsR(lat, lng) {
    document.getElementById('rLatitud').value  = lat.toFixed(7);
    document.getElementById('rLongitud').value = lng.toFixed(7);
    document.getElementById('rCoordsTexto').textContent =
        `📍 ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
}

function buscarUbicacionR() {
    const q = document.getElementById('rMapBuscar').value.trim();
    if (!q) return;
    fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=1`)
        .then(r => r.json()).then(data => {
            if (data.length) {
                const lat = parseFloat(data[0].lat), lng = parseFloat(data[0].lon);
                mapaR.setView([lat, lng], 16);
                colocarMarcadorR(lat, lng);
            } else alert('Ubicación no encontrada.');
        });
}

// ── MAPA EDITAR ───────────────────────────────────────
let mapaE = null, marcadorE = null;

function iniciarMapaEditar(lat, lng) {
    const centro = (lat && lng) ? [lat, lng] : SCZ;
    const zoom   = (lat && lng) ? 16 : 13;

    setTimeout(() => {
        if (mapaE) { mapaE.remove(); mapaE = null; marcadorE = null; }
        mapaE = L.map('mapaEditar').setView(centro, zoom);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            { attribution: '© OpenStreetMap' }).addTo(mapaE);
        if (lat && lng) colocarMarcadorE(lat, lng);
        mapaE.on('click', e => colocarMarcadorE(e.latlng.lat, e.latlng.lng));
        mapaE.invalidateSize();
    }, 350);
}

function colocarMarcadorE(lat, lng) {
    if (marcadorE) mapaE.removeLayer(marcadorE);
    marcadorE = L.marker([lat, lng], { draggable: true }).addTo(mapaE);
    marcadorE.on('dragend', () => {
        const p = marcadorE.getLatLng();
        guardarCoordsE(p.lat, p.lng);
    });
    guardarCoordsE(lat, lng);
}

function guardarCoordsE(lat, lng) {
    document.getElementById('eLatitud').value  = lat.toFixed(7);
    document.getElementById('eLongitud').value = lng.toFixed(7);
    document.getElementById('eCoordsTexto').textContent =
        `📍 ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
}

function buscarUbicacionE() {
    const q = document.getElementById('eMapBuscar').value.trim();
    if (!q) return;
    fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=1`)
        .then(r => r.json()).then(data => {
            if (data.length) {
                const lat = parseFloat(data[0].lat), lng = parseFloat(data[0].lon);
                mapaE.setView([lat, lng], 16);
                colocarMarcadorE(lat, lng);
            } else alert('Ubicación no encontrada.');
        });
}

// Iniciar mapa registrar al abrir modal
document.querySelector('[onclick="abrirModal()"]').addEventListener('click', () => {
    setTimeout(() => {
        iniciarMapaRegistro();
        setTimeout(() => {
            if (mapaR) mapaR.invalidateSize();
        }, 300);
    }, 500);
});

</script>

@endpush