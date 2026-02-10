<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boletos - {{ $rifa->nombre }}</title>
    <style>
        /* --- CONFIGURACIÓN DE PÁGINA --- */
        @page {
            margin: 0.5cm;
            size: letter;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        /* --- CONTENEDOR PRINCIPAL DEL BOLETO --- */
        .ticket-wrapper {
            width: 100%;
            height: 230px; /* Un poco más alto para que respire el diseño */
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 12px; /* Bordes redondeados modernos */
            overflow: hidden;
            position: relative;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); /* Sombra sutil para vista web */
        }

        /* --- ESTRUCTURA DE TABLA (Crucial para PDF) --- */
        table.layout {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
        }
        td.section {
            vertical-align: top;
            padding: 0;
        }

        /* --- SECCIÓN IZQUIERDA: TALÓN (CONTROL) --- */
        .stub {
            width: 25%;
            background-color: #f4f4f4;
            border-right: 2px dashed #bbb; /* Línea de corte clásica */
            position: relative;
            padding: 15px !important;
            box-sizing: border-box;
        }
        .stub-header {
            text-transform: uppercase;
            font-size: 10px;
            color: #777;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .stub-folio {
            font-size: 16px;
            font-weight: 900;
            color: #d32f2f;
            background: #fff;
            display: inline-block;
            padding: 2px 8px;
            border: 1px solid #d32f2f;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .stub-form {
            font-size: 9px;
            color: #555;
            line-height: 2.2; /* Espaciado para escribir a mano */
        }
        .cut-icon {
            position: absolute;
            right: -9px;
            top: 50%;
            font-size: 14px;
            background: #fff;
            color: #999;
            height: 20px;
            line-height: 20px;
        }

        /* --- SECCIÓN DERECHA: CUERPO DEL BOLETO --- */
        .main-body {
            width: 75%;
            padding: 0 !important;
            position: relative;
        }

        /* Header Azul Superior */
        .ticket-header {
            background-color: #0d1b2a; /* Azul muy oscuro moderno */
            color: #fff;
            padding: 10px 20px;
            border-bottom: 3px solid #e63946; /* Acento rojo */
        }
        .header-title {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-sub {
            font-size: 10px;
            color: #ccc;
            margin-top: 2px;
        }

        /* Contenido Principal */
        .content-area {
            padding: 15px 20px;
            position: relative;
        }

        /* Precio Flotante Moderno */
        .price-badge {
            position: absolute;
            top: 15px;
            right: 20px;
            background-color: #e63946;
            color: white;
            padding: 5px 15px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 18px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* Folio Gigante */
        .main-folio-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #777;
            font-weight: bold;
        }
        .main-folio {
            font-size: 38px;
            font-weight: 900;
            color: #1d3557; /* Azul corporativo */
            letter-spacing: 2px;
            margin-top: -5px;
            font-family: 'Courier New', monospace; /* Fuente monoespaciada tipo impreso */
        }

        /* --- NUEVO DISEÑO: 5 NÚMEROS (OPORTUNIDADES) --- */
        .lucky-numbers-container {
            margin-top: 15px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 8px 12px;
            display: inline-block;
            width: 65%; /* Ocupa parte del ancho, deja espacio al QR */
        }
        .lucky-label {
            font-size: 9px;
            font-weight: bold;
            color: #457b9d;
            text-transform: uppercase;
            margin-bottom: 5px;
            display: block;
        }
        .numbers-row {
            width: 100%;
            text-align: left;
        }
        .num-badge {
            display: inline-block;
            background: #fff;
            border: 1px solid #457b9d;
            color: #1d3557;
            font-weight: bold;
            font-size: 12px;
            padding: 4px 0;
            width: 32px;
            text-align: center;
            border-radius: 4px;
            margin-right: 4px;
            font-family: 'Courier New', monospace;
        }

        /* QR y Legales */
        .qr-area {
            position: absolute;
            bottom: 10px;
            right: 15px;
            text-align: center;
        }
        .qr-box {
            border: 1px solid #ddd;
            padding: 4px;
            background: #fff;
            border-radius: 6px;
            display: inline-block;
        }
        
        .legal-text {
            position: absolute;
            bottom: 10px;
            left: 20px;
            width: 60%;
            font-size: 8px;
            color: #999;
            text-align: justify;
            line-height: 1.2;
        }

        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    @foreach($boletos as $index => $boleto)
        
        @php
            // --- LÓGICA PARA 5 NÚMEROS DE LA SUERTE ---
            // Generamos 5 números únicos
            $oportunidades = [];
            while(count($oportunidades) < 5) {
                $n = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT); // 3 dígitos
                if(!in_array($n, $oportunidades)) $oportunidades[] = $n;
            }
            sort($oportunidades); // Ordenados se ven mejor
        @endphp

        <div class="ticket-wrapper">
            <table class="layout">
                <tr>
                    <td class="section stub">
                        <div class="stub-header">Talón de Control</div>
                        <div class="stub-folio">{{ $boleto->folio }}</div>
                        
                        <div class="stub-form">
                            Nombre:<br>_______________________<br>
                            Teléfono:<br>_______________________<br>
                            Dirección:<br>_______________________
                        </div>
                        <div class="cut-icon">✂</div>
                    </td>

                    <td class="section main-body">
                        
                        <div class="ticket-header">
                            <div class="header-title">{{ $rifa->nombre }}</div>
                            <div class="header-sub">📍 {{ $rifa->sede }} | 📅 Fecha: {{ date('d/m/Y') }}</div>
                        </div>

                        <div class="content-area">
                            
                            <div class="price-badge">
                                ${{ number_format($rifa->precio_boleto, 0) }}
                            </div>

                            <div class="main-folio-label"># de Folio</div>
                            <div class="main-folio">Nº {{ $boleto->folio }}</div>

                            <div class="lucky-numbers-container">
                                <span class="lucky-label">✨ Tus 5 Oportunidades Adicionales:</span>
                                <div class="numbers-row">
                                    @foreach($oportunidades as $op)
                                        <span class="num-badge">{{ $op }}</span>
                                    @endforeach
                                </div>
                            </div>

                        </div>

                        <div class="legal-text">
                            <strong>CONDICIONES:</strong> Este boleto participa bajo las normas establecidas. 
                            Conserve este talón original para reclamar su premio. 
                            Caducidad: 30 días posteriores al sorteo.
                            <br><span style="color: #bbb;">ID Único: {{ substr($boleto->codigo_qr, 0, 12) }}</span>
                        </div>

                        <div class="qr-area">
                            <div class="qr-box">
                                <img src="data:image/svg+xml;base64, {{ base64_encode(QrCode::format('svg')->size(70)->generate(route('boleto.verificar', $boleto->codigo_qr))) }}" width="70" height="70">
                            </div>
                            <div style="font-size: 8px; font-weight: bold; color: #0d1b2a; margin-top: 2px;">Verificar</div>
                        </div>

                    </td>
                </tr>
            </table>
        </div>

        @if(($index + 1) % 4 == 0)
            <div class="page-break"></div>
        @endif

    @endforeach

</body>
</html>