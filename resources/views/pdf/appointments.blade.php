<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Citas</title>
    <style>
        @page {
            margin: 40px 30px 20px 30px;
        }

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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            padding: 9px 10px;
            text-align: left;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #fff;
        }

        tbody td {
            padding: 8px 10px;
            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 99px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
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

    @php
    $isColor = $modo === 'color';
    $accent = $isColor ? '#ba7517' : '#1c1b1f';
    $muted = $isColor ? '#847467' : '#555';
    $statBg = $isColor ? '#fdf6ec' : '#f5f5f5';
    $statBorder = $isColor ? '#f0d9b0' : '#ccc';
    $rowEven = $isColor ? '#fdf9f5' : '#f9f9f9';
    $rowBorder = $isColor ? '#f0ebe4' : '#ddd';

    $total = $appointments->count();
    $completadas = $appointments->where('status', 'completed')->count();
    $confirmadas = $appointments->where('status', 'confirmed')->count();
    $canceladas = $appointments->where('status', 'cancelled')->count();
    @endphp

    {{-- HEADER --}}
    <div style="display:flex; align-items:center; justify-content:space-between;
            padding:20px 30px; border-bottom:3px solid {{ $accent }}; margin-bottom:20px;">
        <div style="display:flex; align-items:center; gap:14px;">
            <img src="{{ public_path('logo-turnos-pro.png') }}" alt="Logo" style="height:48px; width:auto;">
            <div>
                <div style="font-size:18px; font-weight:700; color:#1c1b1f;">{{ $company->name }}</div>
                <div style="font-size:10px; color:{{ $muted }}; margin-top:2px;">{{ $company->email }} · {{ $company->phone }}</div>
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:14px; font-weight:700; color:{{ $accent }}; text-transform:uppercase; letter-spacing:1px;">Reporte de Citas</div>
            <div style="font-size:9px; color:{{ $muted }}; margin-top:4px;">Generado el {{ $generado_en }}</div>
            @if ($desde || $hasta)
            <div style="font-size:9px; color:{{ $muted }}; margin-top:2px;">
                Período: {{ $desde ? \Carbon\Carbon::parse($desde)->format('d/m/Y') : '—' }}
                al {{ $hasta ? \Carbon\Carbon::parse($hasta)->format('d/m/Y') : '—' }}
            </div>
            @endif
        </div>
    </div>

    {{-- STATS --}}
    <table style="width:calc(100% - 60px); margin:0 30px 16px; border-collapse:separate; border-spacing:6px 0;">
        <tr>
            @foreach ([
            ['label' => 'Total citas', 'value' => $total],
            ['label' => 'Completadas', 'value' => $completadas],
            ['label' => 'Confirmadas', 'value' => $confirmadas],
            ['label' => 'Canceladas', 'value' => $canceladas],
            ] as $stat)
            <td style="background:{{ $statBg }}; border:1px solid {{ $statBorder }};
                   border-radius:6px; padding:8px 10px; text-align:center; width:25%;">
                <div style="font-size:20px; font-weight:700; color:{{ $accent }};">{{ $stat['value'] }}</div>
                <div style="font-size:8px; color:{{ $muted }}; text-transform:uppercase; letter-spacing:0.5px; margin-top:2px;">{{ $stat['label'] }}</div>
            </td>
            @endforeach
        </tr>
    </table>

    {{-- TABLA --}}
    @php $chunks = $appointments->chunk(20); @endphp

    @foreach ($chunks as $chunkIndex => $chunk)

    @if ($chunkIndex > 0)
    <div style="page-break-before: always; padding-top: 30px;"></div>
    @endif

    <div style="margin:0 30px;">
        <table>
            <thead>
                <tr style="background:{{ $accent }};">
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
                @foreach ($chunk as $i => $appt)
                @php
                $globalIndex = $chunkIndex * 20 + $i + 1;
                $isEven = $i % 2 === 1;
                $rowBg = $isEven ? $rowEven : '#fff';

                if ($isColor) {
                $badgeStyle = match($appt->status) {
                'confirmed' => 'background:#d1fae5;color:#065f46;',
                'completed' => 'background:#dbeafe;color:#1e40af;',
                'cancelled' => 'background:#fee2e2;color:#991b1b;',
                default => 'background:#f5f5f5;color:#555;',
                };
                } else {
                $badgeStyle = 'background:#e8e8e8;color:#1c1b1f;border:1px solid #aaa;';
                }

                $statusLabel = match($appt->status) {
                'confirmed' => 'Confirmada',
                'completed' => 'Completada',
                'cancelled' => 'Cancelada',
                default => $appt->status,
                };
                @endphp
                <tr style="background:{{ $rowBg }}; border-bottom:1px solid {{ $rowBorder }};">
                    <td style="color:{{ $muted }};">{{ $globalIndex }}</td>
                    <td>
                        <strong>{{ $appt->customer->name }}</strong><br>
                        <span style="color:{{ $muted }};font-size:9px;">{{ $appt->customer->email }}</span>
                    </td>
                    <td>{{ $appt->services->pluck('name')->join(', ') }}</td>
                    <td>{{ $appt->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($appt->start_time)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($appt->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($appt->end_time)->format('H:i') }}</td>
                    <td><span class="badge" style="{{ $badgeStyle }}">{{ $statusLabel }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @endforeach

    {{-- FOOTER --}}
    <div style="margin-top:24px; padding:12px 30px; border-top:1px solid {{ $rowBorder }};
            display:flex; justify-content:space-between; font-size:8px; color:{{ $muted }};">
        <span>TurnosPRO · Reporte generado automáticamente</span>
        <span>{{ $generado_en }}</span>
    </div>

</body>

</html>