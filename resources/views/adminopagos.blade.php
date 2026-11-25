@extends('layouts.carcasa')

@section('title', 'Alumnos')
@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <p>
                            <b style="text-transform: uppercase">Administación de Orden de Pago</b>
                            <br>
                            Área y Fundamento Legal
                        </p>
                        <div style=" text-align: right;">
                            <button type="button" class="btn btn-md btn-success" data-bs-toggle="modal"
                                data-bs-target="#addAdminOp">
                                <i class="fa-regular fa-file-lines"></i>
                            </button>
                        </div>
                    </div>
                    {{-- Administración activos --}}
                    <div class="card-body">
                        <table id="tbladminop" class="table table-striped dt-responsive nowrap border" style="width:100%;">
                            <thead>
                                <tr>
                                    {{-- <th>Nombre del Área</th> --}}
                                    <th>Fundamento Legal</th>
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
    @include('modals.adminopagos.addadminop')
    @include('modals.adminopagos.editadminop')
@endsection

@section('scripts')
    {{-- TABLA DE ALUMNOS ACTIVOS --}}
    <script>
        // $(document).ready(function() {
        var table = $('#tbladminop').DataTable({
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
                url: '/ajaxConsulta/AdminOpagos',
                dataSrc: 'data'
            },
            columns: [
                // {
                //     data: 'namearea'
                // },
                {
                    data: 'fundamentolegal'
                },
                {
                    data: null
                },
            ],
            columnDefs: [{
                targets: 1,
                orderable: false,
                render: function(data, type, row) {
                    return '<button onclick="buscarAdminOp(' + data.idgeneral +
                        ');" type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editAdminOp"><i class="bi bi-pencil-square"></i></button>'
                }
            }, ],
        });
        // });

        function buscarAdminOp(idgeneral) {
            // alert('Holis: ' + idgeneral);
            // Hacer solicitud AJAX para obtener los datos personales
            $.ajax({
                url: "/ajaxConsulta/edit/" + idgeneral + "/adminopago",
                type: 'GET',
                success: function(data) {
                    // console.log(data);
                    // Llenar los campos del modal con los datos recibidos
                    // $('#idgeneral_2').val(data.idgeneral);
                    // $('#namearea_2').val(data.namearea);
                    // $('#funlegal_2').val(data.fundamentolegal);
                    document.getElementById("idgeneral_2").value = data.idgeneral;
                    // document.getElementById("namearea_2").value = data.namearea;
                    document.getElementById("funlegal_2").value = data.fundamentolegal;

                    // Habilitar el botón si hay cambios en los campos
                    configurarValidaciones_2();

                    // Mostrar el modal
                    $('#editAdminOp').modal('show');
                },
                error: function() {
                    alert('Error al cargar los datos del registro.');
                }
            });
        }

        function configurarValidaciones_2() {
            const form = document.getElementById('editAdminOpForm');
            const submitBtn = document.getElementById('submitEditAdminOp');
            const inputs = form.querySelectorAll('input, textarea');

            // Guardar los valores iniciales
            const valoresIniciales = {};
            inputs.forEach(input => {
                valoresIniciales[input.id] = input.value;
            });

            // Habilitar el botón si se detecta un cambio
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    let hayCambios = false;
                    inputs.forEach(i => {
                        if (i.value !== valoresIniciales[i.id]) {
                            hayCambios = true;
                        }
                    });
                    submitBtn.disabled = !hayCambios; // Habilitar o deshabilitar el botón
                });
            });

            // Deshabilitar el botón inicialmente
            submitBtn.disabled = true;
        }
    </script>
@endsection
