@extends('layouts.carcasa')

@section('title', 'Alumnos')
@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <p>
                            <b style="text-transform: uppercase">Registrar Conceptos</b>
                            <br>
                            Listado de conceptos
                        </p>
                        <div style=" text-align: right;">
                            <button type="button" class="btn btn-md btn-success" data-bs-toggle="modal"
                                data-bs-target="#addConcepto">
                                <i class="bi bi-file-earmark-plus"></i>
                            </button>
                        </div>
                    </div>
                    {{-- Conceptos activos --}}
                    <div class="card-body">
                        <h2 class="text-center">Conceptos activos</h2>
                        <table id="tblconceptosact" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
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
                        <table id="tblconceptosina" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
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
    @include('modals.conceptos.addconcepto')
    @include('modals.conceptos.editconcepto')
@endsection

@section('scripts')
    {{-- TABLA DE ALUMNOS ACTIVOS --}}
    <script>
        // $(document).ready(function() {
        var table = $('#tblconceptosact').DataTable({
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
                url: '/ajaxConsulta/Conceptos/act',
                dataSrc: 'data'
            },
            columns: [{
                    data: 'concepto'
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
                    return '<button onclick="buscarConcepto(' + row.idconcepto +
                        ');" type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editConcepto"><i class="bi bi-pencil-square"></i></button>' +
                        '<a href="/upConcepto/' + row.idconcepto +
                        '/baja" class="btn btn-danger me-2" onclick="return confirm(\'¿Estás seguro de dar de baja el concepto: ' +
                        row.concepto + '?\')" ><i class="bi bi-chevron-bar-down"></i></a>';
                }
            }, ],
        });
        // });

        function buscarConcepto(idconcepto) {
            // Hacer solicitud AJAX para obtener los datos personales
            $.ajax({
                url: "/ajaxConsulta/edit/" + idconcepto + "/concepto",
                // url: /datospersonales/${idDato}/edit,
                type: 'GET',
                success: function(data) {
                    // console.log(data);
                    document.getElementById("idconcepto_2").value = data.idconcepto;
                    document.getElementById("concepto_2").value = data.concepto;
                    document.getElementById("costo_2").value = data.costo;

                    // Habilitar el botón si hay cambios en los campos
                    configurarValidacionesConcepto();

                    // Mostrar el modal
                    $('#editConcepto').modal('show');
                },
                error: function() {
                    alert('Error al cargar los datos del concepto.');
                }
            });
        }

        function configurarValidacionesConcepto() {
            const submitBtn = document.getElementById('submitEditConcepto');
            const conceptoInput = document.getElementById('concepto_2');
            const costoInput = document.getElementById('costo_2');

            // Guardar el valor inicial del campo editable
            const valorInicialConcepto = conceptoInput.value;
            const valorInicialCosto = costoInput.value;

            // Habilitar el botón si el valor del input cambia
            function validarCambiosConcepto() {
                const huboCambio =
                    conceptoInput.value !== valorInicialConcepto ||
                    costoInput.value !== valorInicialCosto;

                const camposValidos =
                    conceptoInput.value.trim() !== '' &&
                    costoInput.value !== '';

                if (huboCambio && camposValidos) {
                    submitBtn.disabled = false;
                } else {
                    submitBtn.disabled = true;
                }
            }

            conceptoInput.oninput = validarCambiosConcepto;
            costoInput.oninput = validarCambiosConcepto;

            validarCambiosConcepto();
        }

        // Llama a la función después de que se abra el modal
        $('#editConcepto').on('shown.bs.modal', function() {
            configurarValidacionesConcepto();
        });

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
    </script>

    {{-- TABLA DE ALUMNOS INACTIVOS --}}
    <script>
        // $(document).ready(function() {
        var table = $('#tblconceptosina').DataTable({
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
                url: '/ajaxConsulta/Conceptos/ina',
                dataSrc: 'data'
            },
            columns: [{
                    data: 'concepto'
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
                    return '<a href="/upConcepto/' + row.idconcepto +
                        '/alta" class="btn btn-success me-2" onclick="return confirm(\'¿Estás seguro de dar de alta el concepto: ' +
                        row.concepto + '?\')" ><i class="bi bi-chevron-bar-up"></i></a>';
                }
            }, ],
        });
        // });
    </script>
@endsection
