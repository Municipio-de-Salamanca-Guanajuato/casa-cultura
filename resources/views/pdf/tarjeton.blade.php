<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tarjetón</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }

        .hoja {
            width: 842pt;
            height: 595pt;
            position: relative;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .tarjeton,
        .reglamento {
            position: absolute;
            top: 0;
            width: 421pt;
            height: 595pt;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .tarjeton {
            left: 0;
        }

        .reglamento {
            left: 421pt;
        }

        .fondo-tarjeton {
            position: absolute;
            top: 18pt;
            left: 10pt;
            width: 401pt;
            height: 560pt;
        }

        .fondo-reglamento {
            position: absolute;
            top: 0;
            left: 0;
            width: 421pt;
            height: 595pt;
        }

        img {
            display: block;
        }

        .fondo {
            width: 100%;
            height: 100%;
        }


        /* DATOS */
        .campo {
            position: absolute;
            font-size: 11px;
        }

        .nombre {
            top: 86px;
            left: 260px;
        }

        .clase {
            top: 110px;
            left: 234px;
        }

        /* Ancho fijo de la línea de HORARIO; el tamaño de letra se calcula en el controlador */
        .horario {
            top: 131px;
            left: 260px;
            width: 240px;
            white-space: nowrap;
        }

        .maestro {
            top: 152px;
            left: 265px;
        }

        .fecha {
            top: 175px;
            left: 402px;
        }

        .sello {
            position: absolute;
            width: 115px;
            opacity: 0.8;
        }

        /* POSICIONES DE MESES IMAGENES */
        .marzo {
            top: 250px;
            left: 40px;
        }

        .abril {
            top: 250px;
            left: 220px;
        }

        .mayo {
            top: 250px;
            left: 399px;
        }

        .junio {
            top: 405px;
            left: 40px;
        }

        .julio {
            top: 405px;
            left: 220px;
        }

        .agosto {
            top: 405px;
            left: 399px;
        }

        .septiembre {
            top: 560px;
            left: 40px;
        }

        .octubre {
            top: 560px;
            left: 220px;
        }

        .noviembre {
            top: 560px;
            left: 399px;
        }


        .texto {
            position: absolute;
            font-size: 10px;
            font-family: Arial, sans-serif;
        }


        /* MARZO FECHAS Y NO. RECIBOS */
        .fecha-marzo {
            top: 355px;
            left: 60px;
        }

        .recibo-marzo {
            top: 366px;
            left: 95px;
        }

        /* ABRIL */
        .fecha-abril {
            top: 355px;
            left: 245px;
        }

        .recibo-abril {
            top: 366px;
            left: 273px;
        }

        .fecha-mayo {
            top: 355px;
            left: 420px;
        }

        .recibo-mayo {
            top: 366px;
            left: 455px;
        }

        .fecha-junio {
            top: 508px;
            left: 60px;
        }

        .recibo-junio {
            top: 520px;
            left: 95px;
        }

        .fecha-julio {
            top: 508px;
            left: 245px;
        }

        .recibo-julio {
            top: 519px;
            left: 273px;
        }

        .fecha-agosto {
            top: 508px;
            left: 420px;
        }

        .recibo-agosto {
            top: 520px;
            left: 455px;
        }

        .fecha-septiembre {
            top: 665px;
            left: 60px;
        }

        .recibo-septiembre {
            top: 676px;
            left: 95px;
        }

        .fecha-octubre {
            top: 665px;
            left: 245px;
        }

        .recibo-octubre {
            top: 676px;
            left: 273px;
        }

        .fecha-noviembre {
            top: 665px;
            left: 420px;
        }

        .recibo-noviembre {
            top: 676px;
            left: 455px;
        }
    </style>


</head>

<body>
    <div class="hoja">
        <div class="tarjeton">

            {{-- Detectar si es PDF --}}
            @php
                $isPdf = app()->runningInConsole();

                $pathFondo = public_path('images/tarjeton_base.jpg');
                $pathSello = public_path('images/sello_digital.jpeg');

                $meses = ['marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre'];

                // $mesesPagadosMap = array_flip($mesesPagados);

            @endphp

            <!-- FONDO -->
            <img src="{{ $pathFondo }}" class="fondo-tarjeton">

            <!-- DATOS -->
            <div class="campo nombre">
                {{ $inscripcion->alumno->name }} {{ $inscripcion->alumno->apellidop }}
                {{ $inscripcion->alumno->apellidom }}
            </div>

            <div class="campo clase">
                {{ $inscripcion->taller->nametaller }}
            </div>

            {{-- Con letra más chica se baja un poco para que quede sobre la línea --}}
            <div class="campo horario"
                style="font-size: {{ $tamHorario }}px; margin-top: {{ round((11 - $tamHorario) * 0.8, 1) }}px;">
                {{ $horario }}
            </div>

            <div class="campo maestro">
                {{ $inscripcion->taller->maestro }}
            </div>

            <div class="campo fecha">
                {{ $inscripcion->created_at->format('d/m/Y') }}
            </div>


            <!-- SELLOS DINÁMICOS -->
            @foreach ($meses as $mes)
                {{-- @if (isset($mesesPagadosMap[$mes])) --}}
                @if (isset($mesesPagados[$mes]))
                    <!-- SELLO -->
                    {{-- <img src="{{ asset('images/sello_digital.jpeg') }}" class="sello {{ $mes }}"> --}}
                    <img src="{{ $pathSello }}" class="sello {{ $mes }}">

                    <!-- FECHA -->
                    <div class="texto fecha-{{ $mes }}">
                        {{ $mesesPagados[$mes]['fecha'] }}
                    </div>

                    <!-- RECIBO -->
                    <div class="texto recibo-{{ $mes }}">
                        {{ $mesesPagados[$mes]['recibo'] }}
                    </div>
                @endif
            @endforeach
        </div>

        <div class="reglamento">
            @php
                $pathReglamento = public_path('images/tarjeton_r.png');
            @endphp

            <img src="{{ $pathReglamento }}" class="fondo-reglamento">
            {{-- <img src="{{ asset('images/tarjeton_r.png') }}" style="width:100%;"> --}}
        </div>
    </div>
</body>

</html>
