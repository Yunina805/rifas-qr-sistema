<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boletos Estructurados 3x3 - Dinámico</title>
    <style>
        /* --- CONFIGURACIÓN DE PÁGINA --- */
        @page { margin: 0; size: letter; }
        body {
            margin: 0; padding: 0;
            font-family: 'Helvetica', Arial, sans-serif; 
            background-color: #ffffff;
        }

        .page-container {
            position: relative;
            width: 21.59cm; height: 27.94cm;
            page-break-after: always;
        }

        /* --- CUADRÍCULA 3x3 --- */
        .ticket-box {
            position: absolute;
            width: 33.33%; height: 33.33%;
            box-sizing: border-box;
            border: 1px dotted #e0e0e0; 
            text-align: center;
        }

        /* --- EL BOLETO (AQUÍ ESTÁ LA MAGIA) --- */
        .inner {
            width: 240px; 
            height: 240px;
            margin-top: 15px; 
            display: inline-block;
            border-radius: 18px;
            
            /* 1. COLOR DINÁMICO DESDE EL CONTROLADOR */
            background-color: {{ $color }}; 
            
            border: 4px solid #ffffff;
            
            /* 2. CAMBIO DE COLOR DE TEXTO: Azul oscuro para que se lea en fondo claro */
            color: #1a2a6c; 
            
            position: relative;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* --- CABECERA DINÁMICA --- */
        .header-table {
            width: 100%;
            padding: 10px 12px;
            border-collapse: collapse;
        }
        .brand-cell {
            text-align: left;
            vertical-align: top;
        }
        .rifa-name {
            display: block;
            font-size: 10px; font-weight: 900; 
            text-transform: uppercase; letter-spacing: 0.5px;
            color: #1a2a6c; /* Ajustado para contraste */
            max-width: 130px;
        }
        .rifa-sede {
            display: block;
            font-size: 7px;
            color: #1a2a6c; /* Ajustado para contraste */
            opacity: 0.8;
            margin-top: 2px;
        }
        
        .folio-cell { text-align: right; vertical-align: top; }
        .folio-label { display: block; font-size: 6px; opacity: 0.7; text-transform: uppercase; font-weight: bold; }
        .folio-num { 
            font-size: 14px; font-weight: 900; color: #ffffff;
            background: #1a2a6c; /* Fondo oscuro para el folio para que resalte */
            padding: 2px 5px; border-radius: 4px;
        }

        /* --- CONTENIDO CENTRAL --- */
        .main-content { text-align: center; padding-top: 2px; }
        
        .qr-container {
            background: white;
            padding: 5px;
            border-radius: 10px;
            display: inline-block;
        }

        /* --- SECCIÓN NÚMEROS (4 DÍGITOS) --- */
        .numbers-section { margin-top: 10px; }
        .numbers-label { 
            font-size: 8px; font-weight: bold; text-transform: uppercase; 
            margin-bottom: 5px; 
            color: #1a2a6c; /* Ajustado para contraste */
        }
        
        .num-chip {
            display: inline-block;
            background: #ffffff;
            color: #1a2a6c;
            width: 42px; height: 22px; line-height: 22px;
            border-radius: 5px;
            font-size: 11px; font-weight: 900;
            margin: 0 1px;
            border-bottom: 2px solid #dce4ec;
        }

        /* --- FOOTER --- */
        .footer-bar {
            position: absolute;
            bottom: 0; width: 100%;
            /* Fondo semitransparente oscuro para que combine con cualquier color pastel */
            background: rgba(26, 42, 108, 0.1); 
            text-align: center; font-size: 8px; font-weight: 900;
            padding: 5px 0; text-transform: uppercase;
            color: #1a2a6c;
        }
    </style>
</head>
<body>

    @foreach($boletos->chunk(9) as $paginaBoletos)
        <div class="page-container">
            @foreach($paginaBoletos->values() as $index => $boleto)
                @php
                    $row = floor($index / 3); 
                    $col = $index % 3;
                    $top = $row * 33.33;
                    $left = $col * 33.33;
                    
                    // Generamos 5 números de 4 dígitos (1000 a 9999)
                    $oportunidades = [
                        rand(1000, 9999), 
                        rand(1000, 9999), 
                        rand(1000, 9999), 
                        rand(1000, 9999), 
                        rand(1000, 9999)
                    ];
                @endphp

                <div class="ticket-box" style="top: {{ $top }}%; left: {{ $left }}%;">
                    <div class="inner">
                        
                        <table class="header-table">
                            <tr>
                                <td class="brand-cell">
                                    {{-- Traemos el nombre y la sede desde la relación del sistema --}}
                                    <span class="rifa-name">{{ $boleto->rifa->nombre }}</span>
                                    <span class="rifa-sede">📍 {{ $boleto->rifa->sede ?? 'Sede por confirmar' }}</span>
                                </td>
                                <td class="folio-cell">
                                    <span class="folio-label">Folio Nº</span>
                                    <span class="folio-num">{{ str_pad($boleto->folio, 5, '0', STR_PAD_LEFT) }}</span>
                                </td>
                            </tr>
                        </table>

                        <div class="main-content">
                            <div class="qr-container">
                                <img src="data:image/svg+xml;base64, {{ base64_encode(QrCode::format('svg')->size(75)->margin(0)->generate($boleto->codigo_qr)) }}" width="75" height="75">
                            </div>
                            
                            <div class="numbers-section">
                                <div class="numbers-label">Tus Oportunidades</div>
                                <div>
                                    @foreach($oportunidades as $n)
                                        <span class="num-chip">{{ $n }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="footer-bar">¡Gracias por participar!</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

</body>
</html>