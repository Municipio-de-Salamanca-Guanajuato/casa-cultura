@extends('layouts.carcasa')


@section('title', 'Alumnos')
@section('styles')
    <style>
        /* From Uiverse.io by gustavofusco */
        .pencil {
            display: block;
            width: 10em;
            height: 10em;
        }

        .pencil__body1,
        .pencil__body2,
        .pencil__body3,
        .pencil__eraser,
        .pencil__eraser-skew,
        .pencil__point,
        .pencil__rotate,
        .pencil__stroke {
            animation-duration: 3s;
            animation-timing-function: linear;
            animation-iteration-count: infinite;
        }

        .pencil__body1,
        .pencil__body2,
        .pencil__body3 {
            transform: rotate(-90deg);
        }

        .pencil__body1 {
            animation-name: pencilBody1;
        }

        .pencil__body2 {
            animation-name: pencilBody2;
        }

        .pencil__body3 {
            animation-name: pencilBody3;
        }

        .pencil__eraser {
            animation-name: pencilEraser;
            transform: rotate(-90deg) translate(49px, 0);
        }

        .pencil__eraser-skew {
            animation-name: pencilEraserSkew;
            animation-timing-function: ease-in-out;
        }

        .pencil__point {
            animation-name: pencilPoint;
            transform: rotate(-90deg) translate(49px, -30px);
        }

        .pencil__rotate {
            animation-name: pencilRotate;
        }

        .pencil__stroke {
            animation-name: pencilStroke;
            transform: translate(100px, 100px) rotate(-113deg);
        }

        /* Animations */
        @keyframes pencilBody1 {

            from,
            to {
                stroke-dashoffset: 351.86;
                transform: rotate(-90deg);
            }

            50% {
                stroke-dashoffset: 150.8;
                /* 3/8 of diameter */
                transform: rotate(-225deg);
            }
        }

        @keyframes pencilBody2 {

            from,
            to {
                stroke-dashoffset: 406.84;
                transform: rotate(-90deg);
            }

            50% {
                stroke-dashoffset: 174.36;
                transform: rotate(-225deg);
            }
        }

        @keyframes pencilBody3 {

            from,
            to {
                stroke-dashoffset: 296.88;
                transform: rotate(-90deg);
            }

            50% {
                stroke-dashoffset: 127.23;
                transform: rotate(-225deg);
            }
        }

        @keyframes pencilEraser {

            from,
            to {
                transform: rotate(-45deg) translate(49px, 0);
            }

            50% {
                transform: rotate(0deg) translate(49px, 0);
            }
        }

        @keyframes pencilEraserSkew {

            from,
            32.5%,
            67.5%,
            to {
                transform: skewX(0);
            }

            35%,
            65% {
                transform: skewX(-4deg);
            }

            37.5%,
            62.5% {
                transform: skewX(8deg);
            }

            40%,
            45%,
            50%,
            55%,
            60% {
                transform: skewX(-15deg);
            }

            42.5%,
            47.5%,
            52.5%,
            57.5% {
                transform: skewX(15deg);
            }
        }

        @keyframes pencilPoint {

            from,
            to {
                transform: rotate(-90deg) translate(49px, -30px);
            }

            50% {
                transform: rotate(-225deg) translate(49px, -30px);
            }
        }

        @keyframes pencilRotate {
            from {
                transform: translate(100px, 100px) rotate(0);
            }

            to {
                transform: translate(100px, 100px) rotate(720deg);
            }
        }

        @keyframes pencilStroke {
            from {
                stroke-dashoffset: 439.82;
                transform: translate(100px, 100px) rotate(-113deg);
            }

            50% {
                stroke-dashoffset: 164.93;
                transform: translate(100px, 100px) rotate(-113deg);
            }

            75%,
            to {
                stroke-dashoffset: 439.82;
                transform: translate(100px, 100px) rotate(112deg);
            }
        }

        .global-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.85);
            z-index: 9999;

            display: flex;
            align-items: center;
            justify-content: center;

            visibility: hidden;
            opacity: 0;
            transition: opacity .2s;
        }

        .global-loader.active {
            visibility: visible;
            opacity: 1;
        }

        .loader-content {
            text-align: center;
        }

        .loader-text {
            margin-top: 10px;
            font-weight: 600;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <p>
                            <b style="text-transform: uppercase">Inscribir Alumno a Taller</b>
                            <br>
                            Listado de inscripciones
                        </p>
                        <div style=" text-align: right; mt-2">
                            <button type="button" onclick="cargarAlumnosTalleres()" class="btn btn-md btn-success me-2"
                                data-bs-toggle="modal" data-bs-target="#addInscripcion" title="Inscribir alumnos">
                                <i class="bi bi-backpack"></i>
                            </button>
                            <a href="/exportar-reporte-general" class="btn btn-warning me-2" title="Repote general"
                                style="color: white;">
                                <i class="fa-solid fa-file-arrow-down"></i>
                            </a>
                            <button id="btnPreviewBajas" type="button" class="btn btn-md btn-danger me-2"
                                title="Dar bajas masivas">
                                <i class="bi bi-person-fill-down"></i>
                            </button>

                        </div>
                    </div>
                    {{-- Inscripciones activas --}}
                    <div class="card-body">
                        <h2 class="text-center">Inscripciones activas</h2>
                        <table id="tblinscripcionesact" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Alumno</th>
                                    <th>Taller</th>
                                    <th>Fecha de Inscripción</th>
                                    <th>Periodo</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    {{-- Inscripciones inactivas --}}
                    <div class="card-body">
                        <h2 class="text-center">Inscripciones inactivas</h2>
                        <table id="tblinscripcionesina" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Alumno</th>
                                    <th>Taller</th>
                                    <th>Fecha de Inscripción</th>
                                    <th>Periodo</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MENSAJE DE EXITO AL AGREGAR UNA INSCRIPCIÓN --}}
    {{-- @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif --}}

    {{-- MENSAJE DE ALERTA --}}
    {{-- @if (session('status'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif --}}

    <div id="globalLoader" class="global-loader">
        <div class="loader-content">
            <!-- Tu animación -->
            <svg xmlns="http://www.w3.org/2000/svg" height="200px" width="200px" viewBox="0 0 200 200" class="pencil">
                <defs>
                    <clipPath id="pencil-eraser">
                        <rect height="30" width="30" ry="5" rx="5"></rect>
                    </clipPath>
                </defs>
                <circle transform="rotate(-113,100,100)" stroke-linecap="round" stroke-dashoffset="439.82"
                    stroke-dasharray="439.82 439.82" stroke-width="2" stroke="currentColor" fill="none" r="70"
                    class="pencil__stroke"></circle>
                <g transform="translate(100,100)" class="pencil__rotate">
                    <g fill="none">
                        <circle transform="rotate(-90)" stroke-dashoffset="402" stroke-dasharray="402.12 402.12"
                            stroke-width="30" stroke="hsl(223,90%,50%)" r="64" class="pencil__body1"></circle>
                        <circle transform="rotate(-90)" stroke-dashoffset="465" stroke-dasharray="464.96 464.96"
                            stroke-width="10" stroke="hsl(223,90%,60%)" r="74" class="pencil__body2"></circle>
                        <circle transform="rotate(-90)" stroke-dashoffset="339" stroke-dasharray="339.29 339.29"
                            stroke-width="10" stroke="hsl(223,90%,40%)" r="54" class="pencil__body3"></circle>
                    </g>
                    <g transform="rotate(-90) translate(49,0)" class="pencil__eraser">
                        <g class="pencil__eraser-skew">
                            <rect height="30" width="30" ry="5" rx="5" fill="hsl(223,90%,70%)">
                            </rect>
                            <rect clip-path="url(#pencil-eraser)" height="30" width="5" fill="hsl(223,90%,60%)">
                            </rect>
                            <rect height="20" width="30" fill="hsl(223,10%,90%)"></rect>
                            <rect height="20" width="15" fill="hsl(223,10%,70%)"></rect>
                            <rect height="20" width="5" fill="hsl(223,10%,80%)"></rect>
                            <rect height="2" width="30" y="6" fill="hsla(223,10%,10%,0.2)"></rect>
                            <rect height="2" width="30" y="13" fill="hsla(223,10%,10%,0.2)"></rect>
                        </g>
                    </g>
                    <g transform="rotate(-90) translate(49,-30)" class="pencil__point">
                        <polygon points="15 0,30 30,0 30" fill="hsl(33,90%,70%)"></polygon>
                        <polygon points="15 0,6 30,0 30" fill="hsl(33,90%,50%)"></polygon>
                        <polygon points="15 0,20 10,10 10" fill="hsl(223,10%,10%)"></polygon>
                    </g>
                </g>
            </svg>

            <p class="loader-text">Dando de baja las inscripciones...</p>
        </div>
    </div>


    {{-- Modales --}}
    @include('modals.inscripciones.addinscripcion')
    @include('modals.inscripciones.editinscripcion')
    @include('modals.inscripciones.bajainscripciones')
@endsection

@section('scripts')
    {{-- TABLA DE INSCRIPCIONES ACTIVAS --}}
    {{-- <script>
        $(document).ready(function() {
            // Detecta cuando el modal se abre
            $(document).on('shown.bs.modal', '#addInscripcion', function() {
                // Verifica si el select ya tiene Select2 antes de inicializar
                if (!$.fn.select2.isInitialized) {
                    $('.js-example-basic-single').select2({
                        width: '100%' // Ajusta el ancho del Select2
                    });
                    console.log('Select2 inicializado dentro del modal');
                }
            });
        });

        // Extiende jQuery para verificar si Select2 ya está inicializado
        if (!$.fn.select2.isInitialized) {
            $.fn.select2.isInitialized = true;
        }
    </script> --}}
    <script>
        $(document).ready(function() {
            $("#id_alumno").select2({
                dropdownParent: $('#addInscripcion'),
                width: '100%' // Ajusta el ancho al 100%
            });
        });
        $(document).ready(function() {
            $("#id_taller").select2({
                dropdownParent: $('#addInscripcion'),
                width: '100%' // Ajusta el ancho al 100%
            });
        });

        $(document).ready(function() {
            $("#id_alumno_2").select2({
                dropdownParent: $('#editInscripcion'),
                width: '100%' // Ajusta el ancho al 100%
            });
        });
        $(document).ready(function() {
            $("#id_taller_2").select2({
                dropdownParent: $('#editInscripcion'),
                width: '100%' // Ajusta el ancho al 100%
            });
        });


        var table = $('#tblinscripcionesact').DataTable({
            scrollX: true,
            "lengthMenu": [
                [15, 20, 50, -1],
                [15, 20, 50, "Todos"]
            ],
            language: {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            ajax: {
                url: '/ajaxConsulta/Inscripciones/act',
                dataSrc: 'data'
            },
            columns: [{
                    data: 'nombreCompleto'
                },
                {
                    data: 'nametaller'
                },
                {
                    data: 'created_at'
                },
                {
                    data: 'periodo'
                },
                {
                    data: 'estatus',
                    render: function(data, type, row) {
                        return data == 1 ? 'Activo' : 'Inactivo';
                    }
                },
                {
                    data: null
                },
            ],
            columnDefs: [{
                targets: 5,
                orderable: false,
                render: function(data, type, row) {
                    return '<a href="/tarjeton/' + row.idinscripcion +
                        '" class="btn btn-primary me-2" title="Ver tarjetón"><i class="bi bi-file-earmark-spreadsheet-fill"></i></a>' +
                        '<a href="/upInscripcion/' + row.idinscripcion +
                        '/baja" class="btn btn-danger me-2" onclick="return confirm(\'¿Estás seguro de dar de baja la inscripción: ' +
                        row.nombreCompleto + '?\')" ><i class="bi bi-chevron-bar-down"></i></a>'
                    // '<button onclick="buscarInscripcion(' + data.idinscripcion +
                    // ');" type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editInscripcion"><i class="bi bi-pencil-square"></i></button>' +

                    // +
                    // '<a href="/deleteInscripcion/' + row.idinscripcion +
                    // '" class="btn btn-danger me-2" onclick="return confirm(\'¿Estás seguro de eliminar la inscripción del alumno: ' +
                    // row.nombreCompleto + ' del taller: ' + row.nametaller +
                    // '?\')" ><i class="bi bi-trash3"></i></a>';
                }
            }, ],
        });
        // });

        function buscarInscripcion(idinscripcion) {
            //alert('Holis: ' + id);
            // Limpiar los select antes de llenarlos
            $('#id_alumno_2').empty().append('<option value="" selected>Seleccione un alumno</option>');
            $('#id_taller_2').empty().append('<option value="" selected>Seleccione un taller</option>');
            // Hacer solicitud AJAX para obtener los datos personales
            $.ajax({
                url: "/ajaxConsulta/edit/" + idinscripcion + "/inscripcion",
                // url: /datospersonales/${idDato}/edit,
                type: 'GET',
                success: function(data) {
                    console.log(data.inscripcion.idinscripcion);
                    $("#idinscripcion_2").val(data.inscripcion.idinscripcion);

                    // Llenar el select de alumnos
                    data.alumnos.forEach(function(alumno) {
                        $('#id_alumno_2').append(
                            `<option value="${alumno.idalumno}" ${alumno.idalumno == data.inscripcion.idalumno ? 'selected' : ''}>${alumno.nombreCompleto}</option>`
                        );
                    });

                    // Llenar el select de talleres
                    data.talleres.forEach(function(taller) {
                        $('#id_taller_2').append(
                            `<option value="${taller.idtaller}" ${taller.idtaller == data.inscripcion.idtaller ? 'selected' : ''}>${taller.nametaller} (${taller.fechainicio} - ${taller.fechafin})</option>`
                        );
                    });

                    // Mostrar el modal
                    $('#editInscripcion').modal('show');
                },
                error: function() {
                    alert('Error al cargar los datos del alumno.');
                }
            });
        }

        // function configurarValidaciones() {
        //     const form = document.getElementById('editAlumnoForm');
        //     const submitBtn = document.getElementById('submitBtnEdit');
        //     const inputs = form.querySelectorAll('input');

        //     // Guardar los valores iniciales
        //     const valoresIniciales = {};
        //     inputs.forEach(input => {
        //         valoresIniciales[input.id] = input.value;
        //     });

        //     // Habilitar el botón si se detecta un cambio
        //     inputs.forEach(input => {
        //         input.addEventListener('input', function() {
        //             let hayCambios = false;
        //             inputs.forEach(i => {
        //                 if (i.value !== valoresIniciales[i.id]) {
        //                     hayCambios = true;
        //                 }
        //             });
        //             submitBtn.disabled = !hayCambios;
        //         });
        //     });
        // }

        // In your Javascript (external .js resource or <script> tag)


        // $('.js-example-basic-single').select2();
        // });
    </script>
    {{-- TABLA DE INSCRIPCIONES INACTIVAS --}}
    <script>
        var table = $('#tblinscripcionesina').DataTable({
            scrollX: true,
            "lengthMenu": [
                [15, 20, 50, -1],
                [15, 20, 50, "Todos"]
            ],
            language: {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            ajax: {
                url: '/ajaxConsulta/Inscripciones/ina',
                dataSrc: 'data'
            },
            columns: [{
                    data: 'nombreCompleto'
                },
                {
                    data: 'nametaller'
                },
                {
                    data: 'created_at'
                },
                {
                    data: 'periodo'
                },
                {
                    data: 'estatus',
                    render: function(data, type, row) {
                        return data == 1 ? 'Activo' : 'Inactivo';
                    }
                },
                {
                    data: null
                },
            ],
            columnDefs: [{
                targets: 5,
                orderable: false,
                render: function(data, type, row) {
                    return '<a href="/upInscripcion/' + row.idinscripcion +
                        '/alta" class="btn btn-success me-2" onclick="return confirm(\'¿Estás seguro de dar de alta la inscripción: ' +
                        row.nombreCompleto + '?\')" ><i class="bi bi-chevron-bar-up"></i></a>';
                }
            }, ],
        });
    </script>
    <script>
        // Datos del modal de inscripción (se llenan al abrirlo)
        let alumnosInscripcion = [];
        let talleresInscripcion = [];

        function escaparHtml(texto) {
            return $('<div>').text(texto ?? '').html();
        }

        function tieneValor(valor) {
            return valor !== null && valor !== undefined && valor !== '';
        }

        function textoRangoEdad(min, max) {
            if (tieneValor(min) && tieneValor(max)) return min + ' a ' + max + ' años';
            if (tieneValor(min)) return 'desde ' + min + ' años';
            if (tieneValor(max)) return 'hasta ' + max + ' años';
            return 'sin límite de edad';
        }

        // Misma regla que Taller::admiteEdad() en el backend (límites inclusivos)
        function tallerAdmiteEdad(taller, edad) {
            const hayMin = tieneValor(taller.edad_min);
            const hayMax = tieneValor(taller.edad_max);

            if (!hayMin && !hayMax) return true;
            if (edad === null || edad === undefined) return false;

            return (!hayMin || edad >= parseInt(taller.edad_min, 10)) &&
                (!hayMax || edad <= parseInt(taller.edad_max, 10));
        }

        // Folio de la última consulta de choques (si cambian de alumno a media consulta, se ignora la respuesta vieja)
        let folioChoques = 0;

        // Llena el select de talleres solo con los que admiten la edad del alumno,
        // y deshabilita (con el motivo) los que le chocan de horario con otra inscripción activa de la misma persona
        function filtrarTalleresPorEdad() {
            const idAlumno = $('#id_alumno').val();
            const $taller = $('#id_taller');
            const $infoAlumno = $('#infoEdadAlumno');
            const $infoTalleres = $('#infoTalleresEdad');

            $taller.empty();
            $infoAlumno.removeClass('text-danger').text('');
            $infoTalleres.removeClass('text-danger').text('');

            if (!idAlumno) {
                folioChoques++; // descarta cualquier consulta de choques pendiente
                $taller.append('<option value="" selected>Primero selecciona un alumno</option>')
                    .prop('disabled', true).trigger('change');
                return;
            }

            const alumno = alumnosInscripcion.find(a => String(a.idalumno) === String(idAlumno));
            const edad = alumno ? alumno.edad : null;

            if (edad === null || edad === undefined) {
                $infoAlumno.addClass('text-danger')
                    .text('No se pudo calcular la edad con la CURP del alumno. Solo se muestran talleres sin límite de edad.');
            } else {
                $infoAlumno.text('Edad del alumno: ' + edad + ' años.');
            }

            const disponibles = talleresInscripcion.filter(t => tallerAdmiteEdad(t, edad));
            const ocultos = talleresInscripcion.length - disponibles.length;
            const folio = ++folioChoques;

            if (disponibles.length === 0) {
                pintarTalleres([], {}, ocultos);
                return;
            }

            // Mientras se revisan los horarios del alumno
            $taller.append('<option value="" selected>Revisando horarios del alumno...</option>')
                .prop('disabled', true).trigger('change');

            $.get('/ajaxConsulta/horarios/choques-alumno/' + idAlumno)
                .done(function(data) {
                    if (folio !== folioChoques) return;
                    pintarTalleres(disponibles, data.choques || {}, ocultos);
                })
                .fail(function() {
                    if (folio !== folioChoques) return;
                    // Si falla la consulta se muestran todos; el servidor vuelve a validar al guardar
                    pintarTalleres(disponibles, {}, ocultos);
                    $infoTalleres.addClass('text-danger')
                        .append(' No se pudieron revisar los horarios del alumno; se validarán al guardar.');
                });
        }

        // Pinta el select de talleres: primero los que puede elegir y al final los que le chocan (deshabilitados)
        function pintarTalleres(disponibles, choques, ocultos) {
            const $taller = $('#id_taller');
            const $infoTalleres = $('#infoTalleresEdad');
            const libres = disponibles.filter(t => !choques[t.idtaller]);
            const chocan = disponibles.filter(t => choques[t.idtaller]);
            const texto = t => `${escaparHtml(t.nametaller)} (${t.fechainicio} - ${t.fechafin}) · ${textoRangoEdad(t.edad_min, t.edad_max)}`;

            $taller.empty().append('<option value="" selected>Seleccione un taller</option>');
            libres.forEach(function(t) {
                $taller.append(`<option value="${t.idtaller}">${texto(t)}</option>`);
            });
            chocan.forEach(function(t) {
                $taller.append(`<option value="${t.idtaller}" disabled>${texto(t)} — No disponible: ${escaparHtml(choques[t.idtaller])}</option>`);
            });

            const avisos = [];
            if (disponibles.length === 0) {
                avisos.push('No hay talleres disponibles para la edad de este alumno.');
            } else if (libres.length === 0) {
                avisos.push('Todos los talleres para su edad le chocan con el horario de otro taller en el que ya está inscrito.');
            }
            if (ocultos > 0 && disponibles.length > 0) {
                avisos.push(ocultos + ' taller(es) no se muestran porque no corresponden a la edad del alumno.');
            }
            if (chocan.length > 0 && libres.length > 0) {
                avisos.push(chocan.length + ' taller(es) aparecen deshabilitados porque le chocan de horario.');
            }

            $infoTalleres.toggleClass('text-danger', libres.length === 0).text(avisos.join(' '));
            $taller.prop('disabled', libres.length === 0).trigger('change');
        }

        $(document).ready(function() {
            $('#id_alumno').on('change', filtrarTalleresPorEdad);
        });

        function cargarAlumnosTalleres() {
            // Limpiar los select antes de llenarlos
            $('#id_alumno').empty().append('<option value="" selected>Seleccione un alumno</option>');

            // Llamada Ajax
            $.ajax({
                url: "/ajaxConsulta/Inscripciones/mostrar", // URL definida en la ruta
                type: 'GET',
                success: function(data) {
                    alumnosInscripcion = data.alumnos;
                    talleresInscripcion = data.talleres;

                    // Llenar el select de alumnos (con su edad)
                    data.alumnos.forEach(function(alumno) {
                        const edad = (alumno.edad === null || alumno.edad === undefined) ? 'edad no disponible' : alumno.edad + ' años';
                        $('#id_alumno').append(
                            `<option value="${alumno.idalumno}">${escaparHtml(alumno.nombreCompleto)} (${edad})</option>`);
                    });

                    // Los talleres se llenan al elegir al alumno
                    $('#id_alumno').trigger('change');

                    // Mostrar el modal
                    $('#addInscripcion').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Ocurrió un error al cargar los datos.');
                }
            });
        }
    </script>
    {{-- SCRIPT BTN BAJAS MASIVAS --}}
    <script>
        $(function() {
            // Setup CSRF para AJAX (jQuery)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            // Click preview
            $('#btnPreviewBajas').on('click', function(e) {
                e.preventDefault();

                // bloquear botón mientras carga
                $('#btnExecuteBajas').prop('disabled', true);
                
                // Llamada para preview
                $.ajax({
                    url: '{{ route('previewBajasInscripciones') }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        // mostrar modal y rellenar datos
                        $('#previewCount').text(res.count || 0);
                        const tbody = $('#previewList tbody').empty();
                        if (res.data && res.data.length > 0) {
                            res.data.forEach(function(row, idx) {
                                const periodo = `${row.fechainicio} - ${row.fechafin}`;
                                tbody.append(
                                    `<tr>
                                        <td>${idx+1}</td>
                                        <td>${row.nombreCompleto}</td>
                                        <td>${row.nametaller}</td>
                                        <td>${periodo}</td>
                                    </tr>`
                                );
                            });
                        } else if (res.ids && res.ids.length > 0) {
                            // fallback si solo hay ids
                            res.ids.forEach(function(id, idx) {
                                tbody.append(
                                    `<tr><td>${idx+1}</td><td>Inscripción #${id}</td><td>-</td><td>-</td></tr>`
                                );
                            });
                        } else {
                            tbody.append(
                                '<tr><td colspan="4">No se encontraron registros.</td></tr>'
                            );
                        }

                        // habilitar o deshabilitar botón ejecutar
                        // Calcular total de registros
                        let total = 0;

                        if (res.count !== undefined && res.count !== null) {
                            total = res.count;
                        } else if (res.data && res.data.length > 0) {
                            total = res.data.length;
                        } else if (res.ids && res.ids.length > 0) {
                            total = res.ids.length;
                        }

                        // Activar o desactivar botón
                        $('#btnExecuteBajas').prop('disabled', total === 0);

                        $('#modalPreviewBajas').modal('show');
                    },
                    error: function(xhr) {
                        alert('Error al obtener la previsualización.');
                        console.error(xhr);
                    }
                });
            });

            // Ejecutar bajas (botón dentro del modal)
            $('#btnExecuteBajas').on('click', function() {
                if (!confirm("¿Confirmas ejecutar la baja masiva de las inscripciones mostradas?")) {
                    return;
                }

                // mostrar overlay
                $('#globalLoader').addClass('active');

                $(this).prop('disabled', true).text('Procesando...');

                $.ajax({
                    url: '{{ route('bajaMasivaInscripciones') }}',
                    type: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        $('#btnExecuteBajas').prop('disabled', false).text('Ejecutar bajas');
                        $('#modalPreviewBajas').modal('hide');
                        $('#globalLoader').removeClass('active');

                        // Mensaje y refrescar tablas
                        alert(res.message || 'Proceso finalizado.');
                        // Recarga tus DataTables:
                        $('#tblinscripcionesact').DataTable().ajax.reload();
                        $('#tblinscripcionesina').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        $('#btnExecuteBajas').prop('disabled', false).text('Ejecutar bajas');
                        $('#globalLoader').removeClass('active');
                        alert('Error al ejecutar la baja. Revisa logs.');
                        console.error(xhr);
                    }
                });
            });
        });
    </script>
@endsection
