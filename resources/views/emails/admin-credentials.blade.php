<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bienvenido a TurnosPRO</title>
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

        .header img {
            height: 48px;
        }

        .header h1 {
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            margin: 12px 0 0;
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

        .credentials {
            background: #faf7f4;
            border: 1px solid #d6c3b3;
            border-radius: 12px;
            padding: 20px 24px;
            margin: 20px 0;
        }

        .cred-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #ede4d9;
        }

        .cred-row:last-child {
            border-bottom: none;
        }

        .cred-label {
            font-size: 12px;
            font-weight: 600;
            color: #847467;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .cred-value {
            font-size: 14px;
            font-weight: 700;
            color: #6b3a1f;
            font-family: monospace;
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

        .warning {
            background: #fff8f0;
            border: 1px solid #f5c07a;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            color: #7a4a10;
            margin-top: 16px;
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
            <h1>Bienvenido a TurnosPRO</h1>
        </div>
        <div class="body">
            <p class="greeting">Hola, {{ $admin->name }}</p>
            <p class="text">
                Has sido registrado como administrador de <strong>{{ $company->name }}</strong> en la plataforma TurnosPRO.
                A continuación encontrarás tus credenciales de acceso temporales:
            </p>

            <div class="credentials">
                <div class="cred-row">
                    <span class="cred-label">Correo</span>
                    <span class="cred-value">{{ $admin->email }}</span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Contraseña temporal</span>
                    <span class="cred-value">{{ $tempPassword }}</span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Empresa</span>
                    <span class="cred-value">{{ $company->name }}</span>
                </div>
            </div>

            <a href="{{ url('/login') }}" class="btn">Ingresar al sistema</a>

            <div class="warning">
                ⚠️ Al iniciar sesión por primera vez, el sistema te pedirá cambiar tu contraseña. Esto es obligatorio antes de acceder al panel.
            </div>
        </div>
        <div class="footer">
            TurnosPRO · Sistema de gestión de citas<br />
            Si no esperabas este correo, por favor ignóralo.
        </div>
    </div>
</body>

</html>