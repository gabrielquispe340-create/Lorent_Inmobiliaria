@php
    $badges = $badges ?? ($propiedad->badges ?? []);
@endphp

@if(auth()->check() && auth()->user()->rol !== 'administrador' && !empty($badges))
<div style="position:absolute;top:8px;right:8px;display:flex;flex-direction:column;gap:4px;z-index:10;">
    @foreach($badges as $badge)
        @php
            $color = match($badge['tipo'] ?? '') {
                'danger'  => '#dc2626',
                'success' => '#059669',
                'warning' => '#d97706',
                default   => '#6b7280',
            };
        @endphp
        <span style="
            display:inline-flex;align-items:center;gap:3px;
            background:{{ $color }};
            color:#fff;
            font-size:9px;font-weight:700;
            padding:2px 7px;border-radius:10px;
            text-transform:uppercase;
            letter-spacing:0.03em;
            white-space:nowrap;
            box-shadow:0 1px 3px rgba(0,0,0,0.2);
        ">
            <i class="{{ $badge['icono'] ?? '' }}" style="font-size:10px;line-height:1"></i>
            {{ $badge['texto'] ?? '' }}
        </span>
    @endforeach
</div>
@endif
