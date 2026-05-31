<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Reporte de actividad</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size:12px; color:#222 }
        table { width:100%; border-collapse: collapse; margin-top:12px }
        th, td { border: 1px solid #ddd; padding:6px; text-align:left }
        th { background:#f4f4f4; font-weight:700 }
        .header { display:flex; justify-content:space-between; align-items:center }
        .title { font-size:16px; font-weight:700 }
        .meta { font-size:12px; color:#666 }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="title">Reporte de actividad</div>
            <div class="meta">Generado: {{ now()->format('d/m/Y H:i:s') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha y hora</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Acción</th>
                <th>Descripción</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            @foreach($actividad as $r)
            <tr>
                <td>{{ $r->id }}</td>
                <td>{{ \Carbon\Carbon::parse($r->fecha_hora)->format('d/m/Y H:i:s') }}</td>
                <td>{{ $r->nombre ?? '—' }}{{ $r->correo ? ' (' . $r->correo . ')' : '' }}</td>
                <td>{{ $r->rol ?? '—' }}</td>
                <td>{{ $r->accion }}</td>
                <td>{{ $r->descripcion }}</td>
                <td>{{ $r->ip ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
