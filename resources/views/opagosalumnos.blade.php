@extends('layouts.carcasa')

@section('title', 'Alumnos')
@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <p>
                            <b style="text-transform: uppercase">Ordenes de pago de Alumnos</b>
                            <br>
                            Listado de alumnos
                        </p>
                        {{-- <div style=" text-align: right;">
                            <button type="button" class="btn btn-md btn-success" data-bs-toggle="modal"
                                data-bs-target="#addAlumno">
                                <i class="bi bi-backpack"></i>
                            </button>
                        </div> --}}
                    </div>
                    {{-- Alumnos activos --}}
                    <div class="card-body">
                        <h2 class="text-center">Inscripciones</h2>
                        <table id="tblopinsc" class="table table-striped dt-responsive nowrap border" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Taller</th>
                                    <th>Inscripción</th>
                                    <th>Estatus del pago</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    {{-- Alumnos inactivos --}}
                    <div class="card-body">
                        <h2 class="text-center">Mensualidades</h2>
                        <table id="tblopmens" class="table table-striped dt-responsive nowrap border" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Taller</th>
                                    <th>Período</th>
                                    <th>Mensualidad</th>
                                    {{-- <th>Estatus del pago</th> --}}
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

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- Modales --}}
    @include('modals.opagos.alumnos.showopinscripcion')
    @include('modals.opagos.alumnos.showopmensualidades')
    {{-- @include('modals.alumnos.editalumno') --}}
@endsection

@section('scripts')
    {{-- -------------- TABLA DE INSCRIPCIONES -------------- --}}
    <script>
        $(document).ready(function() {
            $("#id_concepto").select2({
                dropdownParent: $('#addOpagoInsc'),
                width: '100%' // Ajusta el ancho al 100%
            });
        });
        $(document).ready(function() {
            $("#id_funlegal").select2({
                dropdownParent: $('#addOpagoInsc'),
                width: '100%' // Ajusta el ancho al 100%
            });
        });

        // $(document).ready(function() {
        var table = $('#tblopinsc').DataTable({
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
                url: '/ajaxConsulta/OpagosInscripciones',
                dataSrc: 'data'
            },
            columns: [{
                    data: 'nombreCompleto'
                },
                {
                    data: 'nametaller'
                },
                {
                    data: 'inscripcion',
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
                targets: 4,
                orderable: false,
                render: function(data, type, row) {
                    // '<button onclick="buscarAdminOp(' + data.idgeneral +
                    //     ');" type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#editAdminOp"><i class="fa-solid fa-file-invoice-dollar"></i></button>'
                    let buttons = '';
                    switch (row.estatuspago) {
                        case "0": // Pendiente
                            buttons = `
                                <button onclick="visualizarOrdenInsc(${row.idopago});" type="button" class="btn btn-primary me-2" title="Visualizar Orden" data-bs-toggle="modal" data-bs-target="#showOpagoInsc">
                                    <i class="fa-solid fa-eye"></i>
                                </button>`;
                            break;

                        case "1": // En proceso
                            buttons = `
                                <button onclick="visualizarOrdenInsc(${row.idopago});" type="button" class="btn btn-primary me-2" title="Visualizar Orden" data-bs-toggle="modal" data-bs-target="#showOpagoInsc">
                                    <i class="fa-solid fa-eye"></i>
                                </button>`;
                            break;

                        default: // Valor inesperado, tratamos como pendiente
                            buttons = `
                                <span class="badge bg-danger">Orden no generada correctamente.</span>`;
                            // <button onclick="generarOrden(${row.idinscripcion});" type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addOpagoInsc" title="Generar Orden">
                            //     <i class="fa-solid fa-file-invoice-dollar"></i>
                            // </button>`;
                            break;
                    }

                    return buttons;
                }
            }, ],
        });
        // });

        function visualizarOrdenInsc(idopago) {
            // alert('Holis: ' + idinscripcion);
            // Hacer solicitud AJAX para obtener los datos personales
            $.ajax({
                url: "/ajaxConsulta/" + idopago + "/opagoalumnoinsc",
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
        //     const form = document.getElementById('editAdminOpForm');
        //     const submitBtn = document.getElementById('submitEditAdminOp');
        //     const inputs = form.querySelectorAll('input, textarea');

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
        //             submitBtn.disabled = !hayCambios; // Habilitar o deshabilitar el botón
        //         });
        //     });

        //     // Deshabilitar el botón inicialmente
        //     submitBtn.disabled = true;
        // }
    </script>

    {{-- -------------- TABLA DE MENSUALIDADES -------------- --}}
    <script>
        // $(document).ready(function() {
        var table = $('#tblopmens').DataTable({
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
                url: '/ajaxConsulta/OpagosMensualidades',
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
                    data: 'mensualidad',
                    render: function(data, type, row) {
                        return '$' + parseFloat(data).toFixed(2); // Muestra el valor con dos decimales
                    }
                },
                // {
                //     data: 'estatuspago',
                //     render: function(data, type, row) {
                //         // Cambia el texto según el valor del estatuspago
                //         switch (data) {
                //             case "0":
                //                 return '<span class="badge text-bg-secondary fs-6 p-2">Pendiente</span>';
                //             case "1":
                //                 return '<span class="badge text-bg-warning fs-6 p-2">En proceso</span>';
                //             case "2":
                //                 return '<span class="badge text-bg-success fs-6 p-2">Pagado</span>';
                //             default:
                //                 return '<span class="badge text-bg-secondary fs-6 p-2">Pendiente</span>'; // En caso de un valor inesperado
                //         }
                //     }
                // },
                {
                    data: null
                },
            ],
            columnDefs: [{
                targets: 4,
                orderable: false,
                render: function(data, type, row) {
                    // return '<button onclick="buscarAdminOp(' + data.idgeneral +
                    //     ');" type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#editAdminOp"><i class="fa-solid fa-file-invoice-dollar"></i></button>'
                    return `<button onclick="visualizarOrdenMens(${row.idinscripcion});" type="button" class="btn btn-primary me-2" title="Visualizar Ordenes" data-bs-toggle="modal" data-bs-target="#showOpagoMens">
                                    <i class="fa-solid fa-list-check"></i>
                                </button>
                                `;
                    // let buttons = '';

                    // switch (row.estatuspago) {
                    //     case "0": // En proceso de pago
                    //         buttons = `
                //             <button onclick="visualizarOrdenMens(${row.idinscripcion});" type="button" class="btn btn-primary me-2" title="Visualizar Ordenes" data-bs-toggle="modal" data-bs-target="#showOpagoMens">
                //                 <i class="fa-solid fa-list-check"></i>
                //             </button>`;
                    //         break;

                    //     case "1": // Pagado
                    //         buttons = `
                //             <button onclick="visualizarOrdenMens(${row.idinscripcion});" type="button" class="btn btn-primary me-2" title="Visualizar Ordenes" data-bs-toggle="modal" data-bs-target="#showOpagoMens">
                //                 <i class="fa-solid fa-list-check"></i>
                //             </button>`;
                    //         break;

                    //     default: // Valor inesperado, tratamos como pendiente
                    //         buttons = `
                //             <span class="badge bg-danger">Ordenes no generadas correctamente.</span>`;
                    //         break;
                    // }

                    // return buttons;
                }
            }, ],
        });
        // });

        function visualizarOrdenMens(idinscripcion) {
            // alert('Holis: ' + idgeneral);
            // Hacer solicitud AJAX para obtener los datos personales
            $.ajax({
                url: "/ajaxConsulta/" + idinscripcion + "/opagoalumnomens",
                type: 'GET',
                success: function(data) {
                    console.log(data);
                    // console.log(data);
                    // Limpiar el contenido anterior del modal
                    // $('#modalOrdenesContent').empty();
                    // mostrar nombre (seguro y dentro del modal)
                    // Mostrar nombre del alumno (tomamos el primero)
                    $('#showOpagoMens #alumnoMens').text(data.nombre ?? 'Sin alumno');

                    let modalContent = $('#modalOrdenesContent');
                    modalContent.html(''); // Limpiar contenido anterior
                    let ordenHTML = `
                    <table class="table table-hover">
                        <thead>
                            <tr>
                            <th scope="col">Folio</th>
                            <th scope="col">Folio Cajas</th>
                            <th scope="col">Concepto</th>
                            <th scope="col">Monto Total</th>
                            <th scope="col">Mes Pago</th>
                            <th scope="col">Estatus del Pago</th>
                            <th scope="col">Fecha del Pago</th>
                            </tr>
                        </thead>
                        <tbody>
                    `;
                    if (data.data.length > 0) {
                        data.data.forEach((orden, index) => {
                            let foliocajasTexto = orden.foliocajas ? orden.foliocajas :
                                "En proceso de pago";
                            let estatusTexto = orden.estatuspago == "1" ? "Pagado" :
                                "Pendiente de pago";

                            // Agregar la clase bg-success-subtle al input si estatuspago = 1
                            let inputClass = orden.estatuspago == "1" ?
                                "form-control bg-success-subtle" : "form-control bg-danger-subtle";

                            let fechapagoTexto = orden.fechapago ? orden.fechapago :
                                "Sin fecha de pago";

                            ordenHTML += `
                                    <tr>
                                        <th>
                                            <input type="text" class="form-control" name="foliocc_2[]" value="${orden.foliocc}" readonly />
                                        </th>
                                        <td>
                                            <input type="text" class="form-control" name="foliocajas_2[]" value="${foliocajasTexto}" readonly />
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="concepto_2[]" value="${orden.concepto}" readonly />
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="montototal_2[]" value="${orden.montototal}" readonly />
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="mespago_2[]" value="${orden.mespago}" readonly />
                                        </td>
                                        <td>
                                            <input type="text" class="${inputClass}" name="estatuspago_2[]" value="${estatusTexto}" readonly/>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="fechapago_2[]" value="${fechapagoTexto}" readonly />
                                        </td>
                                    </tr>
                            `;
                        });

                        ordenHTML += `
                                </tbody>
                            </table>
                            `;
                        modalContent.append(ordenHTML);

                    } else {
                        modalContent.html('<p class="text-center">No hay órdenes de pago disponibles.</p>');
                    }


                    // Mostrar el modal
                    $('#showOpagoMens').modal('show');
                },
                error: function() {
                    alert('Error al cargar los datos del registro.');
                }
            });
        }
    </script>
@endsection
