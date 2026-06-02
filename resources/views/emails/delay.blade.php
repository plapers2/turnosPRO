<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aviso de atraso en tu cita</title>
</head>

<body style="margin:0;padding:0;background-color:#f5f3ef;font-family:'Segoe UI',Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f3ef;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

                    <!-- HEADER -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#92400e,#b45309);border-radius:16px 16px 0 0;padding:36px 40px;text-align:center;">
                            @if($appointment->company->logo)
                            <div style="margin:0 auto 12px;width:56px;height:56px;">
                                <img src="{{ asset('storage/' . $appointment->company->logo) }}"
                                    alt="{{ $appointment->company->name }}"
                                    style="width:56px;height:56px;object-fit:contain;border-radius:10px;">
                            </div>
                            @else
                            <div style="width:56px;height:56px;background:rgba(255,255,255,0.15);border-radius:14px;margin:0 auto 16px;text-align:center;line-height:56px;">
                                <span style="font-size:28px;">⏰</span>
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
                                    ⚠ AVISO DE ATRASO
                                </span>
                            </div>

                            <!-- Saludo -->
                            <h2 style="margin:0 0 8px;font-size:20px;color:#1c1b1f;font-weight:700;">
                                Hola, {{ $appointment->customer->user->name }} 👋
                            </h2>

                            <p style="margin:0 0 28px;color:#6b7280;font-size:14px;line-height:1.6;">
                                Te informamos que el servicio presenta un atraso aproximado de
                                <strong style="color:#b45309;">{{ $delayMinutes }} minutos</strong>.
                                Lamentamos los inconvenientes y agradecemos tu comprensión.
                            </p>

                            <!-- Card detalles -->
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="background:#fdf8f0;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;margin-bottom:28px;">

                                <!-- Empresa -->
                                <tr>
                                    <td style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
                                        <table width="100%">
                                            <tr>
                                                <td style="width:32px;">🏢</td>
                                                <td>
                                                    <p style="margin:0;font-size:11px;color:#9ca3af;">EMPRESA</p>
                                                    <p style="margin:2px 0 0;font-size:14px;font-weight:600;">
                                                        {{ $appointment->company->name }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Servicio -->
                                <tr>
                                    <td style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
                                        <table width="100%">
                                            <tr>
                                                <td style="width:32px;">🛎️</td>
                                                <td>
                                                    <p style="margin:0;font-size:11px;color:#9ca3af;">SERVICIO(S)</p>
                                                    <p style="margin:2px 0 0;font-size:14px;font-weight:600;">
                                                        {{ $appointment->services->pluck('name')->join(', ') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Profesional -->
                                <tr>
                                    <td style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
                                        <table width="100%">
                                            <tr>
                                                <td style="width:32px;">👤</td>
                                                <td>
                                                    <p style="margin:0;font-size:11px;color:#9ca3af;">PROFESIONAL</p>
                                                    <p style="margin:2px 0 0;font-size:14px;font-weight:600;">
                                                        {{ $appointment->user->name }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Hora original -->
                                <tr>
                                    <td style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
                                        <table width="100%">
                                            <tr>
                                                <td style="width:32px;">🕐</td>
                                                <td>
                                                    <p style="margin:0;font-size:11px;color:#9ca3af;">HORA ORIGINAL</p>
                                                    <p style="margin:2px 0 0;font-size:14px;font-weight:600;">
                                                        {{ $appointment->start_time->format('d/m/Y') }}
                                                        · {{ $appointment->start_time->format('H:i') }} – {{ $appointment->end_time->format('H:i') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Hora estimada con atraso -->
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <table width="100%">
                                            <tr>
                                                <td style="width:32px;">⏳</td>
                                                <td>
                                                    <p style="margin:0;font-size:11px;color:#9ca3af;">HORA ESTIMADA DE ATENCIÓN</p>
                                                    <p style="margin:2px 0 0;font-size:14px;font-weight:600;color:#b45309;">
                                                        {{ $appointment->start_time->addMinutes($delayMinutes)->format('H:i') }}
                                                        (aprox. +{{ $delayMinutes }} min)
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                            </table>

                            <!-- Nota informativa -->
                            <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:10px;padding:14px 18px;margin-bottom:28px;">
                                <p style="margin:0;font-size:13px;color:#92400e;line-height:1.6;">
                                    💡 <strong>¿Qué puedes hacer?</strong> Puedes presentarte aproximadamente a la hora estimada indicada arriba,
                                    o si lo prefieres, cancela tu cita con el botón de abajo y reagéndala en otro momento.
                                </p>
                            </div>

                            <!-- CTA -->
                            <div style="text-align:center;margin-bottom:28px;">
                                <a href="{{ url('/appointments/cancel/' . $appointment->cancel_token) }}"
                                    style="display:inline-block;background:#b45309;color:#fff;padding:12px 32px;border-radius:10px;text-decoration:none;font-weight:600;font-size:14px;">
                                    Cancelar cita
                                </a>
                            </div>

                            <!-- Disculpa -->
                            <p style="margin:0;font-size:12px;color:#9ca3af;text-align:center;line-height:1.6;">
                                Nos disculpamos por el atraso. Estamos trabajando para atenderte lo antes posible.<br>
                                Si tienes dudas, comunícate directamente con {{ $appointment->company->name }}.
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
                                © {{ date('Y') }} TurnosPRO
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>
