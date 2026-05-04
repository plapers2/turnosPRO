<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Citas</title>
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
        }

        /* HEADER */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 30px;
            border-bottom: 3px solid #ba7517;
            margin-bottom: 20px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .header-left img {
            height: 48px;
            width: auto;
        }

        .header-left .company-name {
            font-size: 18px;
            font-weight: 700;
            color: #1c1b1f;
        }

        .header-left .company-sub {
            font-size: 10px;
            color: #847467;
            margin-top: 2px;
        }

        .header-right {
            text-align: right;
        }

        .header-right .report-title {
            font-size: 14px;
            font-weight: 700;
            color: #ba7517;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-right .report-meta {
            font-size: 9px;
            color: #847467;
            margin-top: 4px;
        }

        /* FILTROS APLICADOS */
        .filters-bar {
            background: #fdf6ec;
            border: 1px solid #f0d9b0;
            border-radius: 6px;
            padding: 8px 16px;
            margin: 0 30px 16px;
            font-size: 9px;
            color: #847467;
        }

        .filters-bar span {
            color: #ba7517;
            font-weight: 600;
        }

        /* STATS */
        .stats-row {
            margin: 0 30px 16px;
            width: calc(100% - 60px);
        }

        .stat-box {
            display: inline-block;
            width: 16.6%;
            margin-right: 1%;
            background: #fdf6ec;
            border: 1px solid #f0d9b0;
            border-radius: 6px;
            padding: 8px 10px;
            text-align: center;
            vertical-align: top;
        }

        .stat-box .stat-number {
            font-size: 20px;
            font-weight: 700;
            color: #ba7517;
        }

        .stat-box .stat-label {
            font-size: 8px;
            color: #847467;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        /* TABLA */
        .table-wrapper {
            margin: 0 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: #ba7517;
            color: #fff;
        }

        thead th {
            padding: 9px 10px;
            text-align: left;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid #f0ebe4;
        }

        tbody tr:nth-child(even) {
            background: #fdf9f5;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody td {
            padding: 8px 10px;
            vertical-align: middle;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 99px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-confirmed {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-completed {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        /* FOOTER */
        .footer {
            margin-top: 24px;
            padding: 12px 30px;
            border-top: 1px solid #f0ebe4;
            display: flex;
            justify-content: space-between;
            font-size: 8px;
            color: #847467;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #847467;
            font-size: 12px;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('logo-turnos-pro.png') }}" alt="Logo">
            <div>
                <div class="company-name">{{ $company->name }}</div>
                <div class="company-sub">{{ $company->email }} · {{ $company->phone }}</div>
            </div>
        </div>
        <div class="header-right">
            <div class="report-title">Reporte de Citas</div>
            <div class="report-meta">Generado el {{ $generado_en }}</div>
            @if ($desde || $hasta)
            <div class="report-meta">
                Período:
                {{ $desde ? \Carbon\Carbon::parse($desde)->format('d/m/Y') : '—' }}
                al
                {{ $hasta ? \Carbon\Carbon::parse($hasta)->format('d/m/Y') : '—' }}
            </div>
            @endif
        </div>
    </div>

    <!-- STATS -->
    @php
    $total = $appointments->count();
    $completadas = $appointments->where('status', 'completed')->count();
    $confirmadas = $appointments->where('status', 'confirmed')->count();
    $pendientes = $appointments->where('status', 'pending')->count();
    $canceladas = $appointments->where('status', 'cancelled')->count();
    @endphp

    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-number">{{ $total }}</div>
            <div class="stat-label">Total citas</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $completadas }}</div>
            <div class="stat-label">Completadas</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $confirmadas }}</div>
            <div class="stat-label">Confirmadas</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $pendientes }}</div>
            <div class="stat-label">Pendientes</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $canceladas }}</div>
            <div class="stat-label">Canceladas</div>
        </div>
    </div>

    <!-- TABLA -->
    <div class="table-wrapper">
        @if ($appointments->isEmpty())
        <div class="empty-state">No hay citas para el período seleccionado.</div>
        @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Servicio(s)</th>
                    <th>Profesional</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($appointments as $i => $appt)
                @php
                $badgeClass = match($appt->status) {
                'pending' => 'badge-pending',
                'confirmed' => 'badge-confirmed',
                'completed' => 'badge-completed',
                'cancelled' => 'badge-cancelled',
                default => '',
                };
                $statusLabel = match($appt->status) {
                'pending' => 'Pendiente',
                'confirmed' => 'Confirmada',
                'completed' => 'Completada',
                'cancelled' => 'Cancelada',
                default => $appt->status,
                };
                @endphp
                <tr>
                    <td style="color:#847467;">{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $appt->customer->name }}</strong><br>
                        <span style="color:#847467;font-size:9px;">{{ $appt->customer->email }}</span>
                    </td>
                    <td>{{ $appt->services->pluck('name')->join(', ') }}</td>
                    <td>{{ $appt->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($appt->start_time)->format('d/m/Y') }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($appt->start_time)->format('H:i') }}
                        –
                        {{ \Carbon\Carbon::parse($appt->end_time)->format('H:i') }}
                    </td>
                    <td><span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <span>TurnosPRO · Reporte generado automáticamente</span>
        <span>{{ $generado_en }}</span>
    </div>

</body>

</html>