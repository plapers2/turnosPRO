<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar cancelación</title>
</head>

<body style="margin:0;padding:0;background-color:#f5f3ef;font-family:'Segoe UI',Arial,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;">
    <div style="max-width:480px;width:100%;margin:40px auto;padding:0 20px;">
        <div style="background:#fff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">
            <div style="background:linear-gradient(135deg,#92400e,#b45309);padding:32px;text-align:center;">
                <p style="font-size:40px;margin:0;">⚠️</p>
                <h1 style="margin:12px 0 0;color:#fff;font-size:20px;font-weight:700;">TurnosPRO</h1>
            </div>
            <div style="padding:32px;text-align:center;">
                <div style="display:inline-block;background:#fef3e2;color:#92400e;font-size:12px;font-weight:600;padding:6px 16px;border-radius:99px;border:1px solid #fcd69a;margin-bottom:20px;">
                    CONFIRMAR CANCELACIÓN
                </div>
                <h2 style="margin:0 0 8px;font-size:18px;color:#1c1b1f;">¿Seguro que deseas cancelar?</h2>
                <p style="margin:0 0 24px;color:#6b7280;font-size:14px;line-height:1.6;">
                    Estás a punto de cancelar tu cita en <strong>{{ $appointment->company->name }}</strong> del
                    <strong>{{ $appointment->start_time->format('d/m/Y') }}</strong> a las
                    <strong>{{ $appointment->start_time->format('H:i') }}</strong>.
                    Esta acción no se puede deshacer.
                </p>
                <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                    <form method="POST" action="{{ route('appointments.cancel.confirm', $token) }}">
                        @csrf
                        <button type="submit"
                            style="background:#dc2626;color:#fff;font-size:14px;font-weight:600;padding:12px 28px;border-radius:10px;border:none;cursor:pointer;">
                            Sí, cancelar cita
                        </button>
                    </form>
                    <a href="{{ route('appointment.index') }}"
                        style="display:inline-block;background:#f3f4f6;color:#374151;font-size:14px;font-weight:600;padding:12px 28px;border-radius:10px;text-decoration:none;border:1px solid #e5e7eb;">
                        No, volver
                    </a>
                </div>
            </div>
            <div style="background:#fdf8f0;padding:16px;text-align:center;border-top:1px solid #e5e7eb;">
                <p style="margin:0;font-size:12px;color:#9ca3af;">© {{ date('Y') }} TurnosPRO</p>
            </div>
        </div>
    </div>
</body>

</html>