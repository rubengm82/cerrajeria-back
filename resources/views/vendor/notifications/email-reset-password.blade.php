<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>Restabliment de contrasenya</title>
    <style>
        /* Base Reset */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        html, body { 
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            background-color: #f8fafc; 
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .wrapper {
            width: 100%;
            height: 100%;
            table-layout: fixed;
            background-color: #f8fafc;
            padding: 20px 15px;
        }
        
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background-color: #ffffff; 
            border-radius: 16px; 
            overflow: hidden; 
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            padding: 0 30px;
        }
        
        .header { 
            background-color: #ffffff;
            padding: 30px 0;
            text-align: center; 
        }
        
        .content { 
            padding: 30px 0;
            color: #334155;
        }
        
        .content h2 {
            color: #0f172a;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 28px;
            letter-spacing: -0.025em;
        }
        
        .content p {
            margin-bottom: 20px;
            font-size: 16px;
            color: #475569;
            line-height: 1.7;
        }
        
        .button-container {
            text-align: center;
            margin: 40px 0;
        }
        
        .button { 
            display: inline-block; 
            background-color: #ea580c; 
            color: #ffffff !important; 
            padding: 16px 36px; 
            text-decoration: none; 
            border-radius: 10px; 
            font-weight: 600; 
            font-size: 16px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(234, 88, 12, 0.2);
        }
        
        .divider {
            border: none;
            border-top: 1px solid #f1f5f9;
            margin: 40px 0;
        }
        
        .link-text {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.6;
            word-break: break-all;
        }
        
        .link-text a {
            color: #ea580c;
            text-decoration: none;
        }
        
        .footer { 
            padding: 30px 0; 
            text-align: center; 
            font-size: 13px; 
            color: #64748b; 
        }

        /* Responsive */
        @media only screen and (max-width: 640px) {
            .wrapper { padding: 20px 10px; }
            .container { padding: 0 15px !important; }
            .content { padding: 20px 0 !important; }
            .button { width: 100% !important; box-sizing: border-box; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <!-- Header con logo (descomenta si tienes el logo) -->
            <div class="header">
                <!-- <img src="{{ config('app.url') }}/images/logoweb.png" alt="Logo" style="max-width: 150px; height: auto;"> -->
            </div>
            
            <div class="content">
                <h2>Hola!</h2>

                <p>Rebs aquest correu perquè hem rebut una sol·licitud de restabliment de contrasenya per al teu compte.</p>

                <div class="button-container">
                    <a href="{{ $url }}" class="button">Restablir contrasenya</a>
                </div>

                <p>Aquest enllaç de restabliment caducarà en 60 minuts.</p>
                <p>Si no has sol·licitat un restabliment de contrasenya, no cal que facis res.</p>

                <div style="margin-top: 40px; padding-top: 10px;">
                    <p style="margin-bottom: 0; color: #64748b; font-size: 15px;">Cordialment,</p>
                    <p style="font-weight: 700; color: #ea580c; font-size: 18px; margin-top: 5px;">Serralleria Solidaria</p>
                </div>

                <hr class="divider">
                <p class="link-text">
                    <strong>¿Tens problemes amb el botó?</strong><br>
                    Si no pots fer clic, copia i enganxa aquest enllaç al teu navegador:<br>
                    <a href="{{ $url }}">{{ $url }}</a>
                </p>
            </div>
        </div>
        
        {{-- <div class="footer">
            <p>&copy; {{ date('Y') }} <strong>Serralleria Solidaria</strong>. Tots els drets reservats.</p>
        </div> --}}
    </div>
</body>
</html>
