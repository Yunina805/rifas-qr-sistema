<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleto Digital - {{ $boleto->rifa->nombre }}</title>
    <style>
        /* --- RESET Y BASES --- */
        @page { margin: 0.5cm; size: letter; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background-color: #fff;
            margin: 0; padding: 0;
        }

        /* --- TICKET CONTENEDOR --- */
        .ticket {
            width: 100%;
            max-width: 700px; /* Un poco más ancho para que respire */
            border: 2px solid #ef6c00; /* Borde Naranja */
            border-radius: 12px;
            margin-bottom: 20px;
            overflow: hidden; /* Mantiene todo dentro de las curvas */
            position: relative;
            background-color: #fff;
        }

        /* --- HEADER (TÍTULO) --- */
        .header {
            background-color: #ef6c00;
            color: white;
            padding: 15px 20px;
            border-bottom: 5px solid #0d47a1; /* Contraste Azul */
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-meta {
            margin-top: 5px;
            font-size: 11px;
            opacity: 0.9;
        }

        /* --- CUERPO (TABLA MAESTRA) --- */
        .layout-table {
            width: 100%;
            border-collapse: collapse;
        }
        .col-left {
            width: 68%;
            padding: 20px;
            vertical-align: top;
        }
        .col-right {
            width: 32%;
            background-color: #f4f6f8; /* Gris muy tenue para separar visualmente */
            border-left: 2px dashed #ccc;
            text-align: center;
            vertical-align: middle;
            position: relative;
            padding: 10px;
        }

        /* --- SECCIONES DE DATOS (IZQUIERDA) --- */
        
        /* 1. Folio Principal */
        .section-folio {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .label {
            font-size: 10px;
            text-transform: uppercase;
            color: #7f8c8d;
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
        }
        .folio-number {
            font-size: 42px; /* Más grande e impactante */
            color: #0d47a1;
            font-weight: 900;
            line-height: 1;
            letter-spacing: 2px;
        }

        /* 2. Oportunidades Extra */
        .section-lucky {
            margin-bottom: 15px;
        }
        .lucky-grid {
            margin-top: 5px;
        }
        .lucky-badge {
            display: inline-block;
            background-color: #fff3e0; /* Fondo naranja muy suave */
            color: #e65100;
            border: 1px solid #ffcc80;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        /* 3. Footer Legal */
        .footer-legal {
            margin-top: 20px;
            font-size: 9px;
            color: #95a5a6;
            line-height: 1.3;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        /* --- SECCIÓN VISUAL (DERECHA) --- */
        .price-tag {
            background-color: #0d47a1;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 18px;
            display: inline-block;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .qr-container img {
            border: 4px solid #fff;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .scan-caption {
            display: block;
            margin-top: 8px;
            font-size: 10px;
            color: #555;
            font-weight: bold;
            text-transform: uppercase;
        }

    </style>
</head>
<body>

    @php
        // Simulación de datos para lógica (igual que la tuya)
        $oportunidades = [];
        while(count($oportunidades) < 5) {
            $n = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
            if(!in_array($n, $oportunidades)) $oportunidades[] = $n;
        }
        sort($oportunidades);
    @endphp

    <div class="ticket">
        
        <div class="header">
            <h1>{{ $boleto->rifa->nombre }}</h1>
            <div class="header-meta">
                📅 <strong>Sorteo:</strong> {{ date('d/m/Y') }} &nbsp;|&nbsp; 
                📍 <strong>Sede:</strong> {{ $boleto->rifa->sede }}
            </div>
        </div>

        <table class="layout-table">
            <tr>
                <td class="col-left">
                    
                    <div class="section-folio">
                        <span class="label">Tu Boleto Ganador</span>
                        <div class="folio-number">{{ $boleto->folio }}</div>
                    </div>

                    <div class="section-lucky">
                        <div class="lucky-grid">
                            @foreach($oportunidades as $op)
                                <span class="lucky-badge">{{ $op }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="footer-legal">
                        <strong>ID Único:</strong> {{ substr($boleto->codigo_qr, 0, 16) }}...<br>
                        Este boleto digital acredita tu participación. Debe presentarse legible para reclamar premios. 
                        Caducidad: 2 días post-evento.
                    </div>
                </td>

                <td class="col-right">
                    
                    <div class="price-tag">
                        ${{ number_format($boleto->rifa->precio_boleto, 0) }}
                    </div>

                    <div class="qr-container">
                        <img src="data:image/svg+xml;base64, {{ base64_encode(QrCode::format('svg')->size(120)->generate($boleto->codigo_qr)) }}" width="120">
                    </div>
                    <span class="scan-caption">Escanear para verificar</span>

                </td>
            </tr>
        </table>
    </div>

</body>
</html>