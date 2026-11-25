@extends('layouts.carcasa')

@section('title', 'Espacios')
@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <p>
                            <b style="text-transform: uppercase">Registrar Espacios</b>
                            <br>
                            Listado de espacios
                        </p>
                        <div style=" text-align: right;">
                            <button type="button" class="btn btn-md btn-success" data-bs-toggle="modal"
                                data-bs-target="#addEspacio">
                                <i class="bi bi-geo-fill"></i>
                            </button>
                        </div>
                    </div>
                    {{-- Espacios activos --}}
                    <div class="card-body">
                        <h2 class="text-center">Espacios activos</h2>
                        <table id="tblespaciosact" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Nombre de la Sala</th>
                                    <th>Cupo</th>
                                    <th>Costo</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    {{-- Espacios inactivos --}}
                    <div class="card-body">
                        <h2 class="text-center">Espacios inactivos</h2>
                        <table id="tblespaciosina" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Nombre de la Sala</th>
                                    <th>Cupo</th>
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
    @include('modals.espacios.addespacio')
    @include('modals.espacios.editespacio')
@endsection

@section('scripts')
    {{-- TABLA DE ESPACIOS ACTIVOS --}}
    <script>
        // $(document).ready(function() {
        var table = $('#tblespaciosact').DataTable({
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
                url: '/ajaxConsulta/Espacios/act',
                dataSrc: 'data'
            },
            columns: [
                {
                    data: 'nameroom'
                },
                {
                    data: 'cupo'
                },
                {
                    data: 'costo'
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
                targets: 4,
                orderable: false,
                render: function(data, type, row) {
                    return '<button onclick="buscarEspacio(' + data.idespacio +
                        ');" type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editEspacio"><i class="bi bi-pencil-square"></i></button>' +
                        '<a href="/upEspacio/' + row.idespacio +
                        '/baja" class="btn btn-danger me-2" onclick="return confirm(\'¿Estás seguro de dar de baja el espacio: ' +
                        row.nameroom + '?\')" ><i class="bi bi-chevron-bar-down"></i></a>';
                    // <a class = 'btn btn-success btn-sm ms-2' 
                    // href = '{{ url('/upFactor/ + row.id_factor + /baja') }}'
                    // onclick = "return confirm('¿Estás seguro de dar de alta el factor?')" >
                    //     Alta </a>
                }
            }, ],
        });
        // });

        function buscarEspacio(idespacio) {
            //alert('Holis: ' + id);
            // Hacer solicitud AJAX para obtener los datos personales
            $.ajax({
                url: "/ajaxConsulta/edit/" + idespacio + "/espacio",
                // url: /datospersonales/${idDato}/edit,
                type: 'GET',
                success: function(data) {
                    // console.log(data);
                    document.getElementById("id_espacio").value = data.idespacio;
                    document.getElementById("nameroom_2").value = data.nameroom;
                    // document.getElementById("descripcion_2").value = data.descripcion;
                    document.getElementById("cupo_2").value = data.cupo;
                    document.getElementById("costo_2").value = data.costo;
                    // document.getElementById("horaextra_2").value = data.horaextra;
                    // document.getElementById("telefono_2").value = data.telefono;

                    // Rellenar los campos del formulario con los datos obtenidos
                    // $('#name_2').val(data.name);
                    // document.getElementById("codigoConceptoEditar").value = data.codigoConcepto;
                    // document.getElementById("importeConceptoEditar").value = data.importeConcepto;

                    // Mostrar siempre los campos de tutor
                    // document.getElementById("tutorFieldsEditar").style.display = 'block';

                    // Habilitar el botón si hay cambios en los campos
                    // configurarValidaciones();
                    // Mostrar el modal
                    $('#editCiudadano').modal('show');
                },
                error: function() {
                    alert('Error al cargar los datos del ciudadano.');
                }
            });
        }
    </script>

    {{-- TABLA DE ESPACIOS INACTIVOS --}}
    <script>
        // $(document).ready(function() {
        var table = $('#tblespaciosina').DataTable({
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
                url: '/ajaxConsulta/Espacios/ina',
                dataSrc: 'data'
            },
            columns: [
                {
                    data: 'nameroom'
                },
                {
                    data: 'cupo'
                },
                {
                    data: 'costo'
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
                targets: 4,
                orderable: false,
                render: function(data, type, row) {
                    return '<a href="/upEspacio/' + row.idespacio +
                        '/alta" class="btn btn-success me-2" onclick="return confirm(\'¿Estás seguro de dar de alta el espacio: ' +
                        row.nameroom + '?\')" ><i class="bi bi-chevron-bar-up"></i></a>';
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
