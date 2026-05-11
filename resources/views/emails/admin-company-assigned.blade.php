<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nueva empresa asignada</title>
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: #f5f0eb;
            margin: 0;
            padding: 24px;
            color: #1c1208;
        }

        .container {
            max-width: 560px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(107, 58, 31, 0.08);
        }

        .header {
            background: #6b3a1f;
            padding: 32px 32px 24px;
            text-align: center;
        }

        .header h1 {
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .body {
            padding: 32px;
        }

        .greeting {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .text {
            font-size: 14px;
            color: #4a3728;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .company-box {
            background: #faf7f4;
            border: 1px solid #d6c3b3;
            border-radius: 12px;
            padding: 16px 20px;
            margin: 20px 0;
        }

        .company-name {
            font-size: 16px;
            font-weight: 700;
            color: #6b3a1f;
        }

        .btn {
            display: block;
            width: fit-content;
            margin: 24px auto;
            background: #6b3a1f;
            color: #fff;
            padding: 12px 28px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .footer {
            background: #faf7f4;
            padding: 20px 32px;
            text-align: center;
            font-size: 12px;
            color: #847467;
            border-top: 1px solid #ede4d9;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Nueva empresa asignada</h1>
        </div>
        <div class="body">
            <p class="greeting">Hola, {{ $admin->name }}</p>
            <p class="text">
                Se te ha asignado acceso de administrador a una nueva empresa en TurnosPRO.
                Ya puedes gestionarla desde tu panel seleccionando la empresa en el selector correspondiente.
            </p>

            <div class="company-box">
                <p class="company-name">{{ $company->name }}</p>
                @if ($company->address)
                <p style="font-size:13px;color:#847467;margin:4px 0 0;">{{ $company->address }}</p>
                @endif
            </div>

            <a href="{{ url('/select-company') }}" class="btn">Ir al panel</a>
        </div>
        <div class="footer">
            TurnosPRO · Sistema de gestión de citas<br />
            Si no esperabas este correo, contacta al administrador de la plataforma.
        </div>
    </div>
</body>

</html>