<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu cita ha sido completada</title>
</head>

<body style="margin:0;padding:0;background-color:#f5f3ef;font-family:'Segoe UI',Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f3ef;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

                    <!-- HEADER -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#92400e,#b45309);border-radius:16px 16px 0 0;padding:36px 40px;text-align:center;">
                            <div style="width:56px;height:56px;background:rgba(255,255,255,0.15);border-radius:14px;margin:0 auto 16px;text-align:center;line-height:56px;">
                                <span style="font-size:28px;">✅</span>
                            </div>
                            <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700;">TurnosPRO</h1>
                            <p style="margin:6px 0 0;color:rgba(255,255,255,0.75);font-size:13px;">Gestión de citas profesional</p>
                        </td>
                    </tr>

                    <!-- BODY -->
                    <tr>
                        <td style="background:#ffffff;padding:40px;">

                            <!-- Badge -->
                            <div style="text-align:center;margin-bottom:24px;">
                                <span style="display:inline-block;background:#dcfce7;color:#166534;font-size:12px;font-weight:600;padding:6px 16px;border-radius:99px;border:1px solid #86efac;">
                                    ✔ CITA COMPLETADA
                                </span>
                            </div>

                            <!-- Saludo -->
                            <h2 style="margin:0 0 8px;font-size:20px;color:#1c1b1f;font-weight:700;">
                                Hola, {{ $appointment->customer->name }} 👋
                            </h2>

                            <p style="margin:0 0 28px;color:#6b7280;font-size:14px;line-height:1.6;">
                                Tu cita ha sido <strong style="color:#1e40af;">marcada como completada</strong>.
                                Gracias por confiar en nosotros. Aquí tienes el resumen:
                            </p>

                            <!-- Card detalles -->
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="background:#fdf8f0;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;margin-bottom:28px;">

                                <tr>
                                    <td style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
                                        <table width="100%">
                                            <tr>
                                                <td style="width:32px;">🏢</td>
                                                <td>
                                                    <p style="margin:0;font-size:11px;color:#9ca3af;">EMPRESA</p>
                                                    <p style="margin:2px 0 0;font-size:14px;font-weight:600;">{{ $appointment->company->name }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
                                        <table width="100%">
                                            <tr>
                                                <td style="width:32px;">🛎️</td>
                                                <td>
                                                    <p style="margin:0;font-size:11px;color:#9ca3af;">SERVICIO(S)</p>
                                                    <p style="margin:2px 0 0;font-size:14px;font-weight:600;">{{ $appointment->services->pluck('name')->join(', ') }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
                                        <table width="100%">
                                            <tr>
                                                <td style="width:32px;">👤</td>
                                                <td>
                                                    <p style="margin:0;font-size:11px;color:#9ca3af;">PROFESIONAL</p>
                                                    <p style="margin:2px 0 0;font-size:14px;font-weight:600;">{{ $appointment->user->name }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:16px 20px;">
                                        <table width="100%">
                                            <tr>
                                                <td style="width:32px;">🕐</td>
                                                <td>
                                                    <p style="margin:0;font-size:11px;color:#9ca3af;">FECHA Y HORA</p>
                                                    <p style="margin:2px 0 0;font-size:14px;font-weight:600;">
                                                        {{ $appointment->start_time->format('d/m/Y') }}
                                                        · {{ $appointment->start_time->format('H:i') }} – {{ $appointment->end_time->format('H:i') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0;font-size:12px;color:#9ca3af;text-align:center;">
                                Esperamos verte pronto. ¡Gracias por tu visita!
                            </p>

                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background:#fdf8f0;border-radius:0 0 16px 16px;padding:20px 40px;text-align:center;border-top:1px solid #e5e7eb;">
                            <p style="margin:0 0 4px;font-size:13px;font-weight:600;color:#92400e;">TurnosPRO</p>
                            <p style="margin:0;font-size:12px;color:#9ca3af;">© {{ date('Y') }} TurnosPRO · Todos los derechos reservados</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>