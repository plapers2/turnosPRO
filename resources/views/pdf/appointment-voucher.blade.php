<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1c1b1f;
            background: #fff;
            padding: 24px;
        }

        .header {
            border-bottom: 2px solid #1c1b1f;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .company-name {
            font-size: 15px;
            font-weight: 700;
        }

        .company-sub {
            font-size: 9px;
            color: #555;
            margin-top: 2px;
        }

        .title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #555;
            margin-top: 6px;
        }

        .section {
            margin-bottom: 14px;
        }

        .label {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #888;
            margin-bottom: 3px;
        }

        .value {
            font-size: 12px;
            font-weight: 600;
            color: #1c1b1f;
        }

        .value-sub {
            font-size: 10px;
            color: #555;
            margin-top: 1px;
        }

        .divider {
            border: none;
            border-top: 1px dashed #ccc;
            margin: 14px 0;
        }

        .services {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .service-tag {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 10px;
            color: #333;
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            border: 1px solid #ccc;
            color: #333;
        }

        .footer {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 8px;
            color: #aaa;
            text-align: center;
        }

        .id-box {
            text-align: right;
            font-size: 9px;
            color: #aaa;
            margin-bottom: 12px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="company-name">{{ $appt->company->name }}</div>
        <div class="company-sub">{{ $appt->company->address }} · {{ $appt->company->phone }}</div>
        <div class="title">Comprobante de cita</div>
    </div>

    <div class="id-box"># {{ str_pad($appt->id, 6, '0', STR_PAD_LEFT) }}</div>

    <div class="section">
        <div class="label">Cliente</div>
        <div class="value">{{ $appt->customer->name }}</div>
        <div class="value-sub">{{ $appt->customer->email }} · {{ $appt->customer->phone }}</div>
    </div>

    <hr class="divider">

    <div class="section">
        <div class="label">Profesional</div>
        <div class="value">{{ $appt->user->name }}</div>
    </div>

    <div class="section">
        <div class="label">Servicio(s)</div>
        <div class="services">
            @foreach ($appt->services as $svc)
            <span class="service-tag">{{ $svc->name }}</span>
            @endforeach
        </div>
    </div>

    <hr class="divider">

    <div class="section">
        <div class="label">Fecha</div>
        <div class="value">{{ $appt->start_time->format('d/m/Y') }}</div>
    </div>

    <div class="section">
        <div class="label">Hora</div>
        <div class="value">{{ $appt->start_time->format('H:i') }} – {{ $appt->end_time->format('H:i') }}</div>
        <div class="value-sub">Duración: {{ $appt->start_time->diffInMinutes($appt->end_time) }} min</div>
    </div>

    <div class="section">
        <div class="label">Estado</div>
        <span class="badge">
            {{ match($appt->status) {
                'confirmed' => 'Confirmada',
                'completed' => 'Completada',
                'cancelled' => 'Cancelada',
                default     => $appt->status,
            } }}
        </span>
    </div>

    @if ($appt->notes)
    <hr class="divider">
    <div class="section">
        <div class="label">Notas</div>
        <div class="value-sub">{{ $appt->notes }}</div>
    </div>
    @endif

    <div class="footer">
        Generado el {{ $generado_en }} · TurnosPRO
    </div>

</body>

</html>