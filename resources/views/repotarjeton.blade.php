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
                            <b style="text-transform: uppercase">Reposición de Tarjetón</b>
                            <br>
                            Listado de solicitudes de reposición de tarjetón.
                        </p>
                        <div style=" text-align: right; mt-2">
                            <button type="button" onclick="cargarInscripciones()" class="btn btn-md btn-success me-2"
                                data-bs-toggle="modal" data-bs-target="#addRepoTarjeton"
                                title="Agregar solicitud de reposición">
                                <i class="bi bi-file-arrow-up-fill"></i>
                            </button>
                        </div>
                    </div>
                    {{-- Reposiciones activas --}}
                    <div class="card-body">
                        <h2 class="text-center">Solicitudes de Reposición activas</h2>
                        <table id="tblrepotarjetonact" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Alumno</th>
                                    <th>Taller</th>
                                    <th>Periodo</th>
                                    <th>Reposición</th>
                                    <th>Estatus del Pago</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    {{-- Reposiciones inactivas --}}
                    <div class="card-body">
                        <h2 class="text-center">Solicitudes de Reposición inactivas</h2>
                        <table id="tblrepotarjetonina" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Alumno</th>
                                    <th>Taller</th>
                                    <th>Periodo</th>
                                    <th>Reposición</th>
                                    <th>Estatus del Pago</th>
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
    @include('modals.repotarjeton.addrepotarjeton')
    @include('modals.opagos.alumnos.showoprepotarjeton')
    {{-- @include('modals.repotarjeton.editinscripcion')
    @include('modals.repotarjeton.bajainscripciones') --}}
@endsection

@section('scripts')
    {{-- TABLA DE REPOSICIONES ACTIVAS --}}
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
            $("#id_inscripcion").select2({
                dropdownParent: $('#addRepoTarjeton'),
                width: '100%' // Ajusta el ancho al 100%
            });
        });

        // $(document).ready(function() {
        //     $("#id_alumno_2").select2({
        //         dropdownParent: $('#editInscripcion'),
        //         width: '100%' // Ajusta el ancho al 100%
        //     });
        // });

        var table = $('#tblrepotarjetonact').DataTable({
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
                url: '/ajaxConsulta/OpagosRepoTarjeton/act',
                dataSrc: 'data'
            },
            columns: [{
                    data: 'nombreCompleto'
                },
                {
                    data: 'nametaller'
                },
                {
                    data: 'periodo'
                },
                {
                    data: 'montototal',
                    render: function(data, type, row) {
                        return '$' + parseFloat(data).toFixed(
                            2); // Esto muestra el precio con dos decimales
                    }
                },
                {
                    data: 'estatuspago',
                    render: function(data, type, row) {
                        // Cambia el texto según el valor del estatuspago
                        switch (data) {
                            case "0":
                                return '<span class="badge text-bg-secondary fs-6 p-2">Pendiente</span>';
                            case "1":
                                return '<span class="badge text-bg-success fs-6 p-2">Pagado</span>';
                            default:
                                return '<span class="badge text-bg-secondary fs-6 p-2">Pendiente</span>'; // En caso de un valor inesperado
                        }
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
                    let botones = `
                        <button onclick="visualizarOrdenRepoTarjeton(${row.idopago});" type="button" class="btn btn-primary me-2" title="Visualizar Orden" data-bs-toggle="modal" data-bs-target="#showOpagoRepoTarjeton">
                            <i class="fa-solid fa-eye"></i>
                        </button>`;

                    if (String(row.estatuspago) !== '1') {
                        botones +=
                            '<button type="button" class="btn btn-secondary me-2" title="Pago pendiente de reposicion" disabled><i class="bi bi-file-earmark-spreadsheet-fill"></i></button>';
                    } else {
                        botones += '<a href="/tarjeton/' + row.idinscripcion +
                            '" class="btn btn-primary me-2" title="Ver tarjetón"><i class="bi bi-file-earmark-spreadsheet-fill"></i></a>';
                    }

                    botones += '<a href="/upTarjeton/' + row.idtarjeton +
                        '/baja" class="btn btn-danger me-2" onclick="return confirm(\'¿Estás seguro de dar de baja la reposición?\')" ><i class="bi bi-chevron-bar-down"></i></a>';

                    return botones;
                }
            }, ],
        });
        // });

        function visualizarOrdenRepoTarjeton(idopago) {
            // alert('Holis: ' + idinscripcion);
            // Hacer solicitud AJAX para obtener los datos personales
            $.ajax({
                url: "/ajaxConsulta/" + idopago + "/opagoalumnorepotarjeton",
                type: 'GET',
                success: function(response) {
                    console.log(response);
                    // Verificar que haya datos en la respuesta
                    if (response.data) {
                        let data = response.data; // Acceder al primer elemento del array
                        let estatusTexto = data.estatuspago == "1" ? "Pagado" : "Pendiente de pago";

                        if (data.estatuspago == "1") {
                            $('#estatuspago').removeClass('bg-danger-subtle').addClass('bg-success-subtle');
                        } else {
                            $('#estatuspago').removeClass('bg-success-subtle').addClass('bg-danger-subtle');
                        }

                        $('#idopago').val(idopago);
                        // Llenar los campos del modal con los datos recibidos
                        // $('#foliocc').val(data.data.foliocc);
                        // $('#foliocajas').val(data.foliocajas);
                        // $('#name').val(data.nombreCompleto);
                        // $('#taller').val(data.nametaller);
                        // $('#concepto').val(data.idconcepto); // Ajusta si el campo "concepto" es diferente
                        // $('#montototal').val(data.montototal);
                        // $('#estatuspago').val(data.estatuspago);

                        // Llenar los campos del modal con los datos recibidos
                        document.getElementById("foliocc").value = data.foliocc;
                        document.getElementById("foliocajas").value = data.foliocajas;
                        document.getElementById("name").value = data.nombreCompleto;
                        document.getElementById("taller").value = data.nametaller;
                        document.getElementById("concepto").value = data.concepto;
                        document.getElementById("montototal").value = data.montototal;
                        // document.getElementById("fechalimite").value = data.fechalimite;
                        document.getElementById("estatuspago").value = estatusTexto;
                        document.getElementById("fechapago").value = data.fechapago;

                        // Rellenar los campos del formulario con los datos obtenidos
                        // $('#foliocc').val(data.foliocc);

                        // Habilitar el botón si hay cambios en los campos
                        // configurarValidaciones();

                        // Mostrar el modal
                        $('#showOpagoInsc').modal('show');
                    } else {
                        alert('No se encontraron datos para esta orden de pago.');
                    }
                },
                error: function() {
                    alert('Error al cargar los datos del registro.');
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
    {{-- TABLA DE REPOSICIONES INACTIVAS --}}
    <script>
        var table = $('#tblrepotarjetonina').DataTable({
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
                url: '/ajaxConsulta/OpagosRepoTarjeton/ina',
                dataSrc: 'data'
            },
            columns: [{
                    data: 'nombreCompleto'
                },
                {
                    data: 'nametaller'
                },
                {
                    data: 'periodo'
                },
                {
                    data: 'montototal',
                    render: function(data, type, row) {
                        return '$' + parseFloat(data).toFixed(
                            2); // Esto muestra el precio con dos decimales
                    }
                },
                {
                    data: 'estatuspago',
                    render: function(data, type, row) {
                        // Cambia el texto según el valor del estatuspago
                        switch (data) {
                            case "0":
                                return '<span class="badge text-bg-secondary fs-6 p-2">Pendiente</span>';
                            case "1":
                                return '<span class="badge text-bg-success fs-6 p-2">Pagado</span>';
                            default:
                                return '<span class="badge text-bg-secondary fs-6 p-2">Pendiente</span>'; // En caso de un valor inesperado
                        }
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
                    return '<a href="/upTarjeton/' + row.idtarjeton +
                        '/alta" class="btn btn-success me-2" onclick="return confirm(\'¿Estás seguro de dar de alta la reposición: ' +
                        row.nombreCompleto + '?\')" ><i class="bi bi-chevron-bar-up"></i></a>';
                }
            }, ],
        });
    </script>
    <script>
        function cargarInscripciones() {
            // alert('Holis');
            // console.log('Dentro del modal')
            // Limpiar los select antes de llenarlos
            $('#id_inscripcion').empty().append('<option value="" selected>Seleccione una inscripción</option>');
            // $('#id_taller').empty().append('<option value="" selected>Seleccione un taller</option>');

            // Llamada Ajax
            $.ajax({
                url: "/ajaxConsulta/Tarjeton/mostrar", // URL definida en la ruta
                type: 'GET',
                success: function(data) {
                    // Verifica si llegan datos
                    // console.log(data);

                    // Llenar el select de alumnos
                    data.data.forEach(function(tarjeton) {
                        $('#id_inscripcion').append(
                            `<option value="${tarjeton.idinscripcion}">${tarjeton.nombreCompleto} - ${tarjeton.nametaller} (${tarjeton.periodo})</option>`
                            );
                    });

                    // Mostrar el modal
                    $('#addRepoTarjeton').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Ocurrió un error al cargar los datos.');
                }
            });
        }
    </script>
    {{-- SCRIPT BTN BAJAS MASIVAS --}}
    {{-- <script>
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
    </script> --}}
@endsection
