<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu cita ha sido confirmada</title>
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
                                <span style="font-size:28px;">📅</span>
                            </div>
                            {{-- Logo si existe, nombre de la empresa siempre --}}
                            @if($appointment->company->logo)
                            <div style="margin:0 auto 12px;width:56px;height:56px;">
                                <img src="{{ asset('storage/' . $appointment->company->logo) }}"
                                    alt="{{ $appointment->company->name }}"
                                    style="width:56px;height:56px;object-fit:contain;border-radius:10px;">
                            </div>
                            @else
                            <div style="width:56px;height:56px;background:rgba(255,255,255,0.15);border-radius:14px;margin:0 auto 16px;text-align:center;line-height:56px;">
                                <span style="font-size:28px;">📅</span>
                            </div>
                            @endif
                            <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700;">
                                {{ $appointment->company->name }} · TurnosPRO
                            </h1>
                            <p style="margin:6px 0 0;color:rgba(255,255,255,0.75);font-size:13px;">
                                Gestión de citas profesional
                            </p>
                        </td>
                    </tr>

                    <!-- BODY -->
                    <tr>
                        <td style="background:#ffffff;padding:40px;">

                            <!-- Badge -->
                            <div style="text-align:center;margin-bottom:24px;">
                                <span style="display:inline-block;background:#fef3c7;color:#92400e;font-size:12px;font-weight:600;padding:6px 16px;border-radius:99px;border:1px solid #fcd34d;letter-spacing:0.3px;">
                                    ✅ CITA CONFIRMADA
                                </span>
                            </div>

                            <!-- Saludo -->
                            <h2 style="margin:0 0 8px;font-size:20px;color:#1c1b1f;font-weight:700;">
                                Hola, {{ $appointment->customer->name }} 👋
                            </h2>
                            <p style="margin:0 0 28px;color:#6b7280;font-size:14px;line-height:1.6;">
                                Tu cita ha sido <strong style="color:#b45309;">confirmada automáticamente</strong> por el sistema ya que no fue gestionada en las últimas 24 horas. Aquí tienes el resumen:
                            </p>

                            <!-- Card detalles -->
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="background:#fdf8f0;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;margin-bottom:28px;">

                                <!-- Empresa -->
                                <tr>
                                    <td style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="width:32px;vertical-align:middle;">
                                                    <span style="font-size:18px;">🏢</span>
                                                </td>
                                                <td style="vertical-align:middle;padding-left:12px;">
                                                    <p style="margin:0;font-size:11px;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Empresa</p>
                                                    <p style="margin:2px 0 0;font-size:14px;color:#1c1b1f;font-weight:600;">{{ $appointment->company->name }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Servicio -->
                                <tr>
                                    <td style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="width:32px;vertical-align:middle;">
                                                    <span style="font-size:18px;">🛎️</span>
                                                </td>
                                                <td style="vertical-align:middle;padding-left:12px;">
                                                    <p style="margin:0;font-size:11px;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Servicio(s)</p>
                                                    <p style="margin:2px 0 0;font-size:14px;color:#1c1b1f;font-weight:600;">{{ $appointment->services->pluck('name')->join(', ') }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Profesional -->
                                <tr>
                                    <td style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="width:32px;vertical-align:middle;">
                                                    <span style="font-size:18px;">👤</span>
                                                </td>
                                                <td style="vertical-align:middle;padding-left:12px;">
                                                    <p style="margin:0;font-size:11px;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Profesional</p>
                                                    <p style="margin:2px 0 0;font-size:14px;color:#1c1b1f;font-weight:600;">{{ $appointment->user->name }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Fecha y hora -->
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="width:32px;vertical-align:middle;">
                                                    <span style="font-size:18px;">🕐</span>
                                                </td>
                                                <td style="vertical-align:middle;padding-left:12px;">
                                                    <p style="margin:0;font-size:11px;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Fecha y hora</p>
                                                    <p style="margin:2px 0 0;font-size:14px;color:#1c1b1f;font-weight:600;">
                                                        {{ $appointment->start_time->format('d/m/Y') }}
                                                        &nbsp;·&nbsp;
                                                        {{ $appointment->start_time->format('H:i') }} – {{ $appointment->end_time->format('H:i') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Botón cancelar -->
                            <div style="text-align:center;margin-bottom:28px;">
                                <a href="{{ url('/appointments/cancel/' . $appointment->cancel_token) }}"
                                    style="display:inline-block;background:#b45309;color:#ffffff;font-size:14px;font-weight:600;padding:12px 32px;border-radius:10px;text-decoration:none;letter-spacing:0.2px;">
                                    Cancelar mi cita
                                </a>
                            </div>

                            <!-- Nota -->
                            <p style="margin:0;font-size:12px;color:#9ca3af;text-align:center;line-height:1.6;">
                                Si no solicitaste esta cita o tienes dudas, contáctanos respondiendo este correo.
                            </p>

                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background:#fdf8f0;border-radius:0 0 16px 16px;padding:20px 40px;text-align:center;border-top:1px solid #e5e7eb;">
                            <p style="margin:0 0 4px;font-size:13px;font-weight:600;color:#92400e;">
                                {{ $appointment->company->name }} · TurnosPRO
                            </p>
                            <p style="margin:0;font-size:12px;color:#9ca3af;">
                                © {{ date('Y') }} TurnosPRO · Todos los derechos reservados
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>