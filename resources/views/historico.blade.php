@extends('layouts.carcasa')

@section('title', 'Alumnos')
@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <p>
                            <b style="text-transform: uppercase">Historico de Inscripciones</b>
                            <br>
                            Listado de alumnos activos e inactivos, con sus respectivas inscripciones y mensualidades. Desde
                            esta sección se pueden visualizar las órdenes de pago generadas para cada alumno, tanto para
                            inscripciones como para mensualidades, así como el estatus de cada una de ellas.
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
                                    <th>Perido</th>
                                    <th>Estatus del alumno</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    {{-- Alumnos inactivos --}}
                    {{-- <div class="card-body">
                        <h2 class="text-center">Mensualidades</h2>
                        <table id="tblopmens" class="table table-striped dt-responsive nowrap border" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Taller</th>
                                    <th>Período</th>
                                    <th>Mensualidad</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div> --}}
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
                url: '/ajaxConsulta/Alumnos-historico',
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
                    data: 'estatus',
                    render: function(data, type, row) {
                        // Cambia el texto según el valor del estatus
                        switch (data) {
                            case "0":
                                return '<span class="badge text-bg-secondary fs-6 p-2">Inactivo</span>';
                            case "1":
                                return '<span class="badge text-bg-success fs-6 p-2">Activo</span>';
                            default:
                                return '<span class="badge text-bg-secondary fs-6 p-2">Sin estatus</span>'; // En caso de un valor inesperado
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
                    return `<button onclick="visualizarOrdenInsc(${row.idinscripcion});" type="button" class="btn btn-primary me-2" title="Visualizar Orden Inscripción" data-bs-toggle="modal" data-bs-target="#showOpagoInsc">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <button onclick="visualizarOrdenMens(${row.idinscripcion});" type="button" class="btn btn-primary me-2" title="Visualizar Ordenes Mensualidades" data-bs-toggle="modal" data-bs-target="#showOpagoMens">
                                    <i class="fa-solid fa-list-check"></i>
                            </button>`;
                }
            }, ],
        });
        // });

        function visualizarOrdenInsc(idinscripcion) {
            // alert('Holis: ' + idinscripcion);
            // Hacer solicitud AJAX para obtener los datos personales
            $.ajax({
                url: "/ajaxConsulta/" + idinscripcion + "/historico-inscripcion",
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

        function visualizarOrdenMens(idinscripcion) {
            $.ajax({
                url: "/ajaxConsulta/" + idinscripcion + "/historico-mensualidades",
                type: 'GET',
                success: function(response) {
                    console.log(response);

                    // Mostrar nombre del alumno (tomando el primer registro)
                    if (response.nombre) {
                        $('#alumnoMens').text(response.nombre);
                    } else {
                        $('#alumnoMens').text("Sin alumno");
                    }

                    // Construir tabla
                    let modalContent = $('#modalOrdenesContent');
                    modalContent.html(''); // limpiar
                    let ordenHTML = `
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Folio Cajas</th>
                            <th>Concepto</th>
                            <th>Monto Total</th>
                            <th>Mes Pago</th>
                            <th>Estatus del Pago</th>
                            <th>Fecha del Pago</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

                    if (response.data && response.data.length > 0) {
                        response.data.forEach((orden) => {
                            let foliocajasTexto = orden.foliocajas ? orden.foliocajas :
                                "En proceso de pago";
                            let estatusTexto = orden.estatuspago == "1" ? "Pagado" :
                                "Pendiente de pago";
                            let inputClass = orden.estatuspago == "1" ?
                                "form-control bg-success-subtle" : "form-control bg-danger-subtle";
                            let fechapagoTexto = orden.fechapago ? orden.fechapago :
                                "Sin fecha de pago";

                            ordenHTML += `
                        <tr>
                            <th><input type="text" class="form-control" value="${orden.foliocc}" readonly /></th>
                            <td><input type="text" class="form-control" value="${foliocajasTexto}" readonly /></td>
                            <td><input type="text" class="form-control" value="${orden.concepto}" readonly /></td>
                            <td><input type="text" class="form-control" value="${orden.montototal}" readonly /></td>
                            <td><input type="text" class="form-control" value="${orden.mespago}" readonly /></td>
                            <td><input type="text" class="${inputClass}" value="${estatusTexto}" readonly /></td>
                            <td><input type="text" class="form-control" value="${fechapagoTexto}" readonly /></td>
                        </tr>
                    `;
                        });

                        ordenHTML += `</tbody></table>`;
                        modalContent.append(ordenHTML);
                    } else {
                        modalContent.html('<p class="text-center">No hay órdenes de pago disponibles.</p>');
                    }

                    // Mostrar modal
                    // Si usas Bootstrap 4 / jQuery:
                    $('#showOpagoMens').modal('show');

                    // O si usas Bootstrap 5 (sin jQuery):
                    // const myModal = new bootstrap.Modal(document.getElementById('showOpagoMens'));
                    // myModal.show();
                },
                error: function() {
                    alert('Error al cargar los datos del registro.');
                }
            });
        }
    </script>
@endsection
