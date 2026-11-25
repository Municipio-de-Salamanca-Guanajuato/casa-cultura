@extends('layouts.carcasa')

@section('title', 'Conceptos por renta')
@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <p>
                            <b style="text-transform: uppercase">Registrar Conceptos por renta</b>
                            <br>
                            Listado conceptos por renta
                        </p>
                        <div style=" text-align: right;">
                            <button type="button" class="btn btn-md btn-success" data-bs-toggle="modal"
                                data-bs-target="#addCxrenta">
                                <i class="fa-solid fa-list-check"></i>
                            </button>
                        </div>
                    </div>
                    {{-- Conceptosxr activos --}}
                    <div class="card-body">
                        <h2 class="text-center">Conceptos activos</h2>
                        <table id="tblcxract" class="table table-striped dt-responsive nowrap border" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Concepto</th>
                                    <th>Costo</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    {{-- Conceptos inactivos --}}
                    <div class="card-body">
                        <h2 class="text-center">Conceptos inactivos</h2>
                        <table id="tblcxrina" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Costo</th>
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

    {{-- Modales --}}
    @include('modals.conceptosxrenta.addcxrenta')
    @include('modals.conceptosxrenta.editcxrenta')
@endsection

@section('scripts')
    {{-- TABLA DE CONCEPTOS ACTIVOS --}}
    <script>
        // $(document).ready(function() {
        var table = $('#tblcxract').DataTable({
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
                url: '/ajaxConsulta/ConceptosxRenta/act',
                dataSrc: 'data'
            },
            columns: [
                {
                    data: 'namecxrenta'
                },
                {
                    data: 'costo',
                    render: function(data, type, row) {
                        return '$' + parseFloat(data).toFixed(
                            2); // Esto muestra el precio con dos decimales
                    }
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
                targets: 3,
                orderable: false,
                render: function(data, type, row) {
                    return '<button onclick="buscarConceptoxr(' + data.idcxrenta +
                        ');" type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editCxrenta"><i class="bi bi-pencil-square"></i></button>' +
                        '<a href="/upConceptoxrenta/' + row.idcxrenta +
                        '/baja" class="btn btn-danger me-2" onclick="return confirm(\'¿Estás seguro de dar de baja el concepto: ' +
                        row.namecxrenta + '?\')" ><i class="bi bi-chevron-bar-down"></i></a>';
                    // <a class = 'btn btn-success btn-sm ms-2' 
                    // href = '{{ url('/upFactor/ + row.id_factor + /baja') }}'
                    // onclick = "return confirm('¿Estás seguro de dar de alta el factor?')" >
                    //     Alta </a>
                }
            }, ],
        });
        // });

        function buscarConceptoxr(idcxrenta) {
            //alert('Holis: ' + id);
            // Hacer solicitud AJAX para obtener los datos personales
            $.ajax({
                url: "/ajaxConsulta/edit/" + idcxrenta + "/conceptoxrenta",
                // url: /datospersonales/${idDato}/edit,
                type: 'GET',
                success: function(data) {
                    console.log(data);
                    document.getElementById("id_cxrenta").value = data.idcxrenta;
                    document.getElementById("cxrenta_2").value = data.namecxrenta;
                    document.getElementById("costo_2").value = data.costo;

                    // Rellenar los campos del formulario con los datos obtenidos
                    // $('#id_cxrenta').val(data.idcxrenta);
                    // document.getElementById("codigoConceptoEditar").value = data.codigoConcepto;
                    // document.getElementById("importeConceptoEditar").value = data.importeConcepto;

                    // Mostrar siempre los campos de tutor
                    // document.getElementById("tutorFieldsEditar").style.display = 'block';

                    // Habilitar el botón si hay cambios en los campos
                    // configurarValidaciones();
                    // Mostrar el modal
                    $('#editCxrenta').modal('show');
                },
                error: function() {
                    alert('Error al cargar los datos del concepto.');
                }
            });
        }

    </script>

    {{-- TABLA DE CONCEPTOS INACTIVOS --}}
    <script>
        // $(document).ready(function() {
        var table = $('#tblcxrina').DataTable({
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
                url: '/ajaxConsulta/ConceptosxRenta/ina',
                dataSrc: 'data'
            },
            columns: [
                {
                    data: 'namecxrenta'
                },
                {
                    data: 'costo',
                    render: function(data, type, row) {
                        return '$' + parseFloat(data).toFixed(
                            2); // Esto muestra el precio con dos decimales
                    }
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
                targets: 3,
                orderable: false,
                render: function(data, type, row) {
                    return '<a href="/upConceptoxrenta/' + row.idcxrenta +
                        '/alta" class="btn btn-success me-2" onclick="return confirm(\'¿Estás seguro de dar de alta el concepto: ' +
                        row.namecxrenta + '?\')" ><i class="bi bi-chevron-bar-up"></i></a>';
                    // <a class = 'btn btn-success btn-sm ms-2' 
                    // href = '{{ url('/upFactor/ + row.id_factor + /baja') }}'
                    // onclick = "return confirm('¿Estás seguro de dar de alta el factor?')" >
                    //     Alta </a>
                }
            }, ],
        });
        // });
    </script>
@endsection
