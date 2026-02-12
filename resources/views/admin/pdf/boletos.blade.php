<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleto - {{ $rifa->nombre }}</title>
    <style>
        /* --- CONFIGURACIÓN GENERAL --- */
        @page {
            margin: 1cm; /* Margen estándar para imprimir */
            size: letter;
        }
        body {
            font-family: 'Arial', sans-serif; /* Fuente estándar y legible */
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* --- CONTENEDOR DEL BOLETO --- */
        .ticket-container {
            width: 100%;
            height: 160px; /* Altura más compacta (antes era 230px) */
            border: 2px solid #000; /* Borde negro sólido y grueso */
            margin-bottom: 10px; /* Espacio entre boletos */
            position: relative;
        }

        /* --- TABLA DE ESTRUCTURA (Talón vs Cuerpo) --- */
        table.layout {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
        }
        td {
            vertical-align: top;
            padding: 5px;
        }

        /* --- TALÓN (IZQUIERDA) --- */
        td.stub {
            width: 22%; /* Un poco más angosto */
            border-right: 1px dashed #000; /* Línea de corte simple */
            text-align: center;
            background-color: #fff; /* Sin fondo gris */
        }
        .stub-folio {
            font-size: 14px;
            font-weight: bold;
            color: #d30000; /* Rojo solo para el folio (común en imprenta) */
            margin-bottom: 5px;
            display: block;
        }
        .stub-data {
            text-align: left;
            font-size: 9px;
            line-height: 1.8;
            margin-top: 10px;
        }
        .stub-label {
            font-weight: bold;
        }

        /* --- CUERPO PRINCIPAL (DERECHA) --- */
        td.body {
            width: 78%;
            padding: 5px 10px;
            position: relative;
        }

        /* Encabezado */
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        .rifa-name {
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .rifa-info {
            font-size: 11px;
            margin-top: 2px;
        }

        /* Sección de Números y Precio */
        .content-row {
            width: 100%;
            margin-top: 5px;
        }
        
        /* El precio grande y visible */
        .price-box {
            float: right;
            border: 2px solid #000;
            padding: 5px 10px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            background: #eee; /* Un gris muy leve para resaltar precio */
        }

        /* Folio Principal */
        .main-folio {
            font-size: 22px;
            font-weight: bold;
            color: #d30000;
            margin-bottom: 5px;
        }

        /* Lista de números (Estilo PDF antiguo) */
        .numbers-list {
            margin-top: 5px;
            font-size: 14px;
            font-family: 'Courier New', monospace; /* Fuente tipo máquina de escribir */
            font-weight: bold;
        }
        .numbers-title {
            font-size: 10px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
        }

        /* Textos legales abajo */
        .legal-footer {
            position: absolute;
            bottom: 5px;
            left: 10px;
            right: 80px; /* Espacio para el QR */
            font-size: 9px;
            text-align: justify;
        }

        /* QR Simple */
        .qr-area {
            position: absolute;
            bottom: 5px;
            right: 5px;
        }

        /* Utilidad para salto de página */
        .page-break { page-break-after: always; }
        
        .clear { clear: both; }
    </style>
</head>
<body>

    @foreach($boletos as $index => $boleto)
        
        @php
            // Mantenemos tu lógica de números, pero se mostrarán diferente
            $oportunidades = [];
            while(count($oportunidades) < 5) {
                $n = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
                if(!in_array($n, $oportunidades)) $oportunidades[] = $n;
            }
            sort($oportunidades);
        @endphp

        <div class="ticket-container">
            <table class="layout">
                <tr>
                    <td class="stub">
                        <div style="font-size: 9px; font-weight: bold;">TALÓN</div>
                        <span class="stub-folio">Nº {{ $boleto->folio }}</span>
                        
                        <div class="stub-data">
                            <span class="stub-label">Nombre:</span><br>
                            __________________<br>
                            <span class="stub-label">Tel:</span><br>
                            __________________<br>
                            <span class="stub-label">Colonia:</span><br>
                            __________________
                        </div>
                    </td>

                    <td class="body">
                        
                        <div class="header">
                            <div class="rifa-name">{{ $rifa->nombre }}</div>
                            <div class="rifa-info">
                                📅 Sorteo: {{ date('d/m/Y') }} | 📍 {{ $rifa->sede }}
                            </div>
                        </div>

                        <div class="price-box">
                            ${{ number_format($rifa->precio_boleto, 0) }}
                        </div>
                        
                        <div class="main-folio">Folio: {{ $boleto->folio }}</div>

                        <div style="margin-top: 10px;">
                            <div class="numbers-title">SUS NÚMEROS:</div>
                            <div class="numbers-list">
                                {{ implode(' - ', $oportunidades) }}
                            </div>
                        </div>

                        <div class="legal-footer">
                            <strong>NOTA:</strong> La boleta se anulará si se encuentra rota, con tachones o enmendaduras.
                            Caducidad: 2 DÍAS después del sorteo. Se pagará al portador.
                            <br>Tel: 9541566004
                        </div>

                        <div class="qr-area">
                            <img src="data:image/svg+xml;base64, {{ base64_encode(QrCode::format('svg')->size(55)->generate($boleto->codigo_qr)) }}">
                        </div>

                    </td>
                </tr>
            </table>
        </div>

        @if(($index + 1) % 5 == 0)
            <div class="page-break"></div>
        @endif

    @endforeach

</body>
</html>