<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f6f8; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .header { background-color: #1e293b; color: #ffffff; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; color: #ffffff; }
        .content { padding: 30px; }
        .footer { background-color: #f1f5f9; padding: 15px; text-align: center; font-size: 12px; color: #64748b; }
        .status-badge { display: inline-block; padding: 6px 12px; border-radius: 4px; font-weight: bold; font-size: 14px; text-transform: uppercase; margin-bottom: 20px; }
        .status-Cerrado { background-color: #dcfce7; color: #16a34a; }
        .status-Contactado { background-color: #eef2ff; color: #4f46e5; }
        .status-Prospecto { background-color: #e0f2fe; color: #0284c7; }
        .status-Perdido { background-color: #fee2e2; color: #dc2626; }
        .btn { display: inline-block; background-color: #4f46e5; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PS Refer</h1>
        </div>
        <div class="content">
            <h2 style="color: #1e293b; margin-top: 0;">Actualización de Referencia</h2>
            <p>Hola {{ $userName }},</p>
            <p>Te informamos que el estado de tu referencia para <strong>{{ $clientName }}</strong> ha cambiado:</p>

            <div style="text-align: center; margin: 20px 0;">
                <span class="status-badge status-{{ $status }}">{{ $status }}</span>
            </div>

            @if($note)
            <div style="margin-top: 20px; padding: 15px; background-color: #fff7ed; border-left: 4px solid #fdba74; color: #9a3412; font-style: italic;">
                <strong>Comentario del equipo:</strong><br>
                "{{ $note }}"
            </div>
            @endif

            <p>Puedes ver más detalles en tu panel de asociado.</p>
            <div style="text-align: center;">
                <a href="{{ config('app.url') }}/dashboard" class="btn">Ir al Dashboard</a>
            </div>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} PS Refer. Todos los derechos reservados.</p>
            <p>Este es un mensaje automático, por favor no respondas a este correo.</p>
        </div>
    </div>
</body>
</html>
