<!DOCTYPE html>
<html>
<head>
    <title>Recuperación de contraseña</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .code { 
            font-size: 24px; 
            font-weight: bold; 
            letter-spacing: 3px; 
            color: #2563eb;
            margin: 20px 0;
            padding: 10px;
            background: #f3f4f6;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hola {{ $username }},</h2>
        <p>Hemos recibido una solicitud para restablecer tu contraseña.</p>
        <p>Tu código de verificación es:</p>
        <div class="code">{{ $code }}</div>
        <p>Este código es válido por 15 minutos. Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
        <p>Atentamente,<br>El equipo de {{ config('app.name') }}</p>
    </div>
</body>
</html>