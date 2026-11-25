@extends('layouts.carcasa')

@section('title', 'Ciudadanos')
@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <p>
                            <b style="text-transform: uppercase">Registrar Ciudadanos</b>
                            <br>
                            Listado de personas que rentan espacios
                        </p>
                        <div style=" text-align: right;">
                            <button type="button" class="btn btn-md btn-success" data-bs-toggle="modal"
                                data-bs-target="#addCiudadano">
                                <i class="fa-solid fa-users"></i>
                            </button>
                        </div>
                    </div>
                    {{-- Ciudadanos activos --}}
                    <div class="card-body">
                        <h2 class="text-center">Ciudadanos activos</h2>
                        <table id="tblciudadanosact" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Teléfono</th>
                                    <th>Direccion</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    {{-- Ciudadanos inactivos --}}
                    <div class="card-body">
                        <h2 class="text-center">Ciudadanos inactivos</h2>
                        <table id="tblciudadanosina" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Teléfono</th>
                                    <th>Direccion</th>
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
    @include('modals.ciudadanos.addciudadano')
    @include('modals.ciudadanos.editciudadano')
@endsection

@section('scripts')
    {{-- VALIDACION DE TELÉFONO --}}
    <script>
        function validarTelefono(input, errorMessageId) {
            let telefono = input.value;

            // Eliminar cualquier carácter que no sea número
            telefono = telefono.replace(/\D/g, '');
            input.value = telefono; // Actualiza el valor en el input

            let errorMessage = document.getElementById(errorMessageId);

            // Validar que tenga exactamente 10 dígitos
            if (telefono.length !== 10) {
                errorMessage.style.display = 'block';
                errorMessage.innerText = 'El teléfono debe contener 10 dígitos.';
            } else {
                errorMessage.style.display = 'none';
            }
        }

        $(document).ready(function() {
            $('form').on('submit', function(e) {
                let telefono = $('#telefono').val();
                let telefonoEdit = $('#telefono_2').val();

                // Verificar que ambos tengan exactamente 10 dígitos si están visibles
                if (
                    ($('#telefono').is(':visible') && telefonoAdd.length !== 10) ||
                    ($('#telefono_2').is(':visible') && telefonoEdit.length !== 10)
                ) {
                    alert('El teléfono debe tener 10 dígitos.');
                    e.preventDefault(); // Evita que el formulario se envíe
                }
                // Verificar nuevamente antes de enviar el formulario
                // if (telefono.length !== 10) {
                //     alert('El teléfono debe tener 10 dígitos.');
                //     e.preventDefault(); // Evita que el formulario se envíe
                // }
            });
        });
    </script>

    {{-- TABLA DE CIUDADANOS ACTIVOS --}}
    <script>
        // $(document).ready(function() {
        var table = $('#tblciudadanosact').DataTable({
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
                url: '/ajaxConsulta/Ciudadanos/act',
                dataSrc: 'data'
            },
            columns: [
                {
                    data: 'nombreCompleto'
                },
                {
                    data: 'telefono'
                },
                {
                    data: 'direccion'
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
                    return '<button onclick="buscarCiudadano(' + data.idciudadano +
                        ');" type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editCiudadano"><i class="bi bi-pencil-square"></i></button>' +
                        '<a href="/upCiudadano/' + row.idciudadano +
                        '/baja" class="btn btn-danger me-2" onclick="return confirm(\'¿Estás seguro de dar de baja el ciudadano: ' +
                        row.name + '?\')" ><i class="bi bi-chevron-bar-down"></i></a>';
                    // <a class = 'btn btn-success btn-sm ms-2' 
                    // href = '{{ url('/upFactor/ + row.id_factor + /baja') }}'
                    // onclick = "return confirm('¿Estás seguro de dar de alta el factor?')" >
                    //     Alta </a>
                }
            }, ],
        });
        // });

        function buscarCiudadano(idciudadano) {
            //alert('Holis: ' + id);
            // Hacer solicitud AJAX para obtener los datos personales
            $.ajax({
                url: "/ajaxConsulta/edit/" + idciudadano + "/ciudadano",
                // url: /datospersonales/${idDato}/edit,
                type: 'GET',
                success: function(data) {
                    console.log(data);
                    document.getElementById("id_ciudadano").value = data.idciudadano;
                    document.getElementById("name_2").value = data.name;
                    document.getElementById("apellidop_2").value = data.apellidop;
                    document.getElementById("apellidom_2").value = data.apellidom;
                    document.getElementById("direccion_2").value = data.direccion;
                    document.getElementById("telefono_2").value = data.telefono;

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

    {{-- TABLA DE CIUDADANOS INACTIVOS --}}
    <script>
        // $(document).ready(function() {
        var table = $('#tblciudadanosina').DataTable({
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
                url: '/ajaxConsulta/Ciudadanos/ina',
                dataSrc: 'data'
            },
            columns: [{
                    data: 'nombreCompleto'
                },
                {
                    data: 'telefono'
                },
                {
                    data: 'direccion'
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
                    return '<a href="/upCiudadano/' + row.idciudadano +
                        '/alta" class="btn btn-success me-2" onclick="return confirm(\'¿Estás seguro de dar de alta el ciudadano: ' +
                        row.name + '?\')" ><i class="bi bi-chevron-bar-up"></i></a>';
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
