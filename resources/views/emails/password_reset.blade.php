{{-- resources/views/emails/password_reset.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperación de contraseña</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f7fb; color: #1f2937; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);">
        <h1 style="margin-top: 0; font-size: 24px; color: #0f172a;">Recuperación de contraseña</h1>
        <p style="font-size: 16px; line-height: 1.75; color: #334155;">
            Hola {{ $name ?? 'usuario' }},
        </p>
        <p style="font-size: 16px; line-height: 1.75; color: #334155;">
            Hemos recibido una solicitud para restablecer tu contraseña. Usa el código a continuación para continuar.
        </p>
        <div style="display: inline-block; padding: 18px 24px; background: #1d4ed8; color: #ffffff; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 24px; letter-spacing: 0.12em;">
            {{ $code }}
        </div>
        <p style="margin-top: 24px; font-size: 16px; line-height: 1.75; color: #334155;">
            Ingresa este código en el formulario de recuperación para continuar con el cambio de contraseña.
        </p>
        <p style="margin-top: 24px; font-size: 14px; color: #64748b;">
            Si no solicitaste este cambio, ignora este correo.
        </p>
        <p style="font-size: 14px; color: #64748b;">Lorent Inmobiliaria</p>
    </div>
</body>
</html>
