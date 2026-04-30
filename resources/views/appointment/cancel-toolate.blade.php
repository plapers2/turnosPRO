<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No se puede cancelar</title>
</head>

<body style="margin:0;padding:0;background-color:#f5f3ef;font-family:'Segoe UI',Arial,sans-serif;min-height:100vh;">
    <div style="max-width:480px;width:100%;margin:40px auto;padding:0 20px;">
        <div style="background:#fff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">
            <div style="background:linear-gradient(135deg,#92400e,#b45309);padding:32px;text-align:center;">
                <p style="font-size:40px;margin:0;">🚫</p>
                <h1 style="margin:12px 0 0;color:#fff;font-size:20px;font-weight:700;">TurnosPRO</h1>
            </div>
            <div style="padding:32px;text-align:center;">
                <div style="display:inline-block;background:#fef2f2;color:#991b1b;font-size:12px;font-weight:600;padding:6px 16px;border-radius:99px;border:1px solid #fca5a5;margin-bottom:20px;">
                    CANCELACIÓN NO PERMITIDA
                </div>
                <h2 style="margin:0 0 8px;font-size:18px;color:#1c1b1f;">Demasiado tarde para cancelar</h2>
                <p style="margin:0 0 24px;color:#6b7280;font-size:14px;line-height:1.6;">
                    Solo puedes cancelar tu cita con al menos <strong>2 horas de anticipación</strong>. Si necesitas ayuda contáctanos directamente.
                </p>
                <a href="{{ route('appointment.index') }}"
                    style="display:inline-block;background:#b45309;color:#fff;font-size:14px;font-weight:600;padding:12px 28px;border-radius:10px;text-decoration:none;">
                    Ir al inicio
                </a>
            </div>
            <div style="background:#fdf8f0;padding:16px;text-align:center;border-top:1px solid #e5e7eb;">
                <p style="margin:0;font-size:12px;color:#9ca3af;">© {{ date('Y') }} TurnosPRO</p>
            </div>
        </div>
    </div>
</body>

</html>