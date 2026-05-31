@php 
$rol = auth()->user()->rol; 
$ruta = Route::currentRouteName(); 
$pendientes = 0;
if ($rol === 'agente') {
    $pendientes = \App\Models\SolicitudVisita::where('estado', 'Pendiente')->whereHas('propiedad', function($q) { $q->where('agente_id', auth()->id()); })->count();
} elseif ($rol === 'asistente') {
    $pendientes = \App\Models\SolicitudVisita::where('estado', 'Pendiente')->count();
}
@endphp

@if(in_array($rol, ['administrador','agente','asistente']))
<aside id="sidebar-mobile" class="md:translate-x-0 md:static md:inset-0" style="transition: transform 0.3s ease-in-out; position: fixed; top: 0; left: 0; bottom: 0; width: 260px; background: #1e293b; z-index: 9999; transform: translateX(-100%);">
    <div class="logo modern-logo">
    <div class="logo-icon">
        🏠
    </div>

    <div>
        <span class="logo-title">Lorent</span>
        <span class="logo-sub">Inmobiliaria</span>
    </div>
</div>
    <nav class="nav">
    @if($rol === 'administrador')
        <p class="nav-section">Principal</p>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ str_starts_with($ruta,'admin.dashboard') ? 'active':'' }}">
            <span class="nav-dot" style="background:#46A2FD"></span>Dashboard
        </a>
        <p class="nav-section">Gestión</p>
        <a href="{{ route('admin.propiedades') }}" class="nav-item {{ str_starts_with($ruta,'admin.propiedades') ? 'active':'' }}">
            <span class="nav-dot" style="background:#9FE1CB"></span>Propiedades
        </a>
        {{-- CU8: Buscar propiedades — ADMIN --}}
        <a href="{{ route('admin.buscar') }}" class="nav-item {{ str_starts_with($ruta,'admin.buscar') ? 'active':'' }}">
            <span class="nav-dot" style="background:#F4C0D1"></span>Buscar propiedades
        </a>
        <p class="nav-section">Admin</p>
        <a href="{{ route('admin.usuarios') }}" class="nav-item {{ str_starts_with($ruta,'admin.usuarios') ? 'active':'' }}">
            <span class="nav-dot" style="background:#AFA9EC"></span>Usuarios
        </a>
        <a href="{{ route('admin.reportes') }}" class="nav-item {{ str_starts_with($ruta,'admin.reportes') ? 'active':'' }}">
            <span class="nav-dot" style="background:#FAC775"></span>Reportes
        </a>

    @elseif($rol === 'agente')
        <p class="nav-section">Gestión</p>
        <a href="{{ route('agente.dashboard') }}" class="nav-item {{ str_starts_with($ruta,'agente.dashboard') ? 'active':'' }}">
            <span class="nav-dot" style="background:#64b5f6"></span>Inicio
        </a>
        <a href="{{ route('agente.propiedades') }}" class="nav-item {{ str_starts_with($ruta,'agente.propiedades') ? 'active':'' }}">
            <span class="nav-dot" style="background:#9FE1CB"></span>Mis propiedades
        </a>
        {{-- CU8: Buscar propiedades — AGENTE --}}
        <a href="{{ route('agente.buscar') }}" class="nav-item {{ str_starts_with($ruta,'agente.buscar') ? 'active':'' }}">
            <span class="nav-dot" style="background:#F4C0D1"></span>Buscar propiedades
        </a>
        <a href="{{ route('agente.visitas') }}" class="nav-item {{ str_starts_with($ruta,'agente.visitas') ? 'active':'' }}" style="display:flex;align-items:center;">
            <span class="nav-dot" style="background:#FAC775"></span>Visitas
            @if($pendientes > 0)<span class="relative flex h-3 w-3 ml-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-red-600"></span></span>@endif
        </a>
        <a href="{{ route('agente.clientes') }}" class="nav-item {{ str_starts_with($ruta,'agente.clientes') ? 'active':'' }}">
            <span class="nav-dot" style="background:#F4C0D1"></span>Clientes
        </a>
        <a href="{{ route('agente.prospectos.index') }}" class="nav-item {{ str_starts_with($ruta,'agente.prospectos') ? 'active':'' }}">
            <span class="nav-dot" style="background:#AFA9EC"></span>Prospectos CRM
        </a>
        <a href="{{ route('agente.calendario') }}" class="nav-item {{ str_starts_with($ruta,'agente.calendario') ? 'active':'' }}">
            <span class="nav-dot" style="background:#818cf8"></span>Calendario
        </a>

    @elseif($rol === 'asistente')
        <p class="nav-section">Operaciones</p>
        <a href="{{ route('asistente.dashboard') }}" class="nav-item {{ str_starts_with($ruta,'asistente.dashboard') ? 'active':'' }}">
            <span class="nav-dot" style="background:#64b5f6"></span>Inicio
        </a>
        {{-- CU8: Buscar propiedades — ASISTENTE --}}
        <a href="{{ route('asistente.buscar') }}" class="nav-item {{ str_starts_with($ruta,'asistente.buscar') ? 'active':'' }}">
            <span class="nav-dot" style="background:#F4C0D1"></span>Buscar propiedades
        </a>
        <a href="{{ route('asistente.visitas') }}" class="nav-item {{ str_starts_with($ruta,'asistente.visitas') ? 'active':'' }}" style="display:flex;align-items:center;">
            <span class="nav-dot" style="background:#FAC775"></span>Agenda / Visitas
            @if($pendientes > 0)<span class="relative flex h-3 w-3 ml-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-red-600"></span></span>@endif
        </a>
        <a href="{{ route('asistente.calendario') }}" class="nav-item {{ str_starts_with($ruta,'asistente.calendario') ? 'active':'' }}">
            <span class="nav-dot" style="background:#818cf8"></span>Calendario
        </a>
        <a href="{{ route('asistente.reportes') }}" class="nav-item {{ str_starts_with($ruta,'asistente.reportes') ? 'active':'' }}">
            <span class="nav-dot" style="background:#AFA9EC"></span>Reportes
        </a>
    @endif

        <p class="nav-section">Cuenta</p>
        <a href="{{ route('perfil') }}" class="nav-item {{ $ruta === 'perfil' ? 'active':'' }}">
            <span class="nav-dot" style="background:#AFA9EC"></span>Mi perfil
        </a>
    </nav>
    
</aside>

@else
{{-- SIDEBAR CLIENTE --}}
<aside id="sidebar-mobile" class="md:translate-x-0 md:static md:inset-0" style="transition: transform 0.3s ease-in-out; position: fixed; top: 0; left: 0; bottom: 0; width: 260px; background: #1e293b; z-index: 9999; transform: translateX(-100%);">
    <div class="logo">
        <span class="logo-title">Lorent</span>
        <span class="logo-sub">Inmobiliaria</span>
    </div>
    <nav class="nav">
        <p class="nav-section">Menú</p>
        <a href="{{ route('cliente.dashboard') }}" class="nav-item {{ str_starts_with($ruta,'cliente.dashboard') ? 'active':'' }}">
            <span class="nav-dot" style="background:#64b5f6"></span>Inicio
        </a>
        <a href="{{ route('cliente.propiedades') }}" class="nav-item {{ str_starts_with($ruta,'cliente.propiedades') ? 'active':'' }}">
            <span class="nav-dot" style="background:#9FE1CB"></span>Propiedades
        </a>
        <a href="{{ route('cliente.buscar') }}" class="nav-item {{ str_starts_with($ruta,'cliente.buscar') ? 'active':'' }}">
            <span class="nav-dot" style="background:#F4C0D1"></span>Buscar
        </a>
        <a href="{{ route('cliente.solicitudes') }}" class="nav-item {{ str_starts_with($ruta,'cliente.solicitudes') ? 'active':'' }}">
            <span class="nav-dot" style="background:#FAC775"></span>Mis solicitudes
        </a>
        <a href="{{ route('cliente.calendario') }}" class="nav-item {{ str_starts_with($ruta,'cliente.calendario') ? 'active':'' }}">
            <span class="nav-dot" style="background:#818cf8"></span>Calendario
        </a>
        <p class="nav-section">Cuenta</p>
        <a href="{{ route('perfil') }}" class="nav-item {{ $ruta === 'perfil' ? 'active':'' }}">
            <span class="nav-dot" style="background:#AFA9EC"></span>Mi perfil
        </a>
    </nav>
    
</aside>
@endif