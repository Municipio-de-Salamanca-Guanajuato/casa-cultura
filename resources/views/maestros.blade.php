@extends('layouts.carcasa')

@section('title', 'Maestros')
@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4">
                {{-- Errores de validación (ej. maestro duplicado) --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">
                        <p>
                            <b style="text-transform: uppercase">Registrar Maestros</b>
                            <br>
                            Listado de maestros
                        </p>
                        <div style=" text-align: right;">
                            <button type="button" class="btn btn-md btn-success" data-bs-toggle="modal"
                                data-bs-target="#addMaestro" title="Agregar maestro">
                                <i class="bi bi-person-plus"></i>
                            </button>
                        </div>
                    </div>
                    {{-- Maestros Activos --}}
                    <div class="card-body">
                        <h2 class="text-center">Maestros activos</h2>
                        <table id="tblmaestrosact" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Nombre completo</th>
                                    <th>Teléfono</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    {{-- Maestros inactivos --}}
                    <div class="card-body">
                        <h2 class="text-center">Maestros inactivos</h2>
                        <table id="tblmaestrosina" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Nombre completo</th>
                                    <th>Teléfono</th>
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
    @include('modals.maestros.addmaestro')
    @include('modals.maestros.editmaestro')
@endsection

@section('scripts')
    <script>
        const idiomaTabla = {
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
        };

        // Escapa texto para usarlo dentro del confirm('...') de un onclick="..."
        function escaparTexto(texto) {
            return String(texto)
                .replace(/\\/g, '\\\\')
                .replace(/'/g, "\\'")
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        // Teléfono opcional: si se captura, deben ser 10 dígitos
        function validarTelefono(input, errorMessageId) {
            input.value = input.value.replace(/\D/g, '').substring(0, 10);

            const errorMessage = document.getElementById(errorMessageId);
            const invalido = input.value.length > 0 && input.value.length !== 10;

            errorMessage.style.display = invalido ? 'block' : 'none';
            errorMessage.innerText = invalido ? 'El teléfono debe contener 10 dígitos.' : '';
            input.setCustomValidity(invalido ? 'El teléfono debe contener 10 dígitos.' : '');
        }

        const columnasMaestro = [{
                data: 'nombre_comp'
            },
            {
                data: 'telefono',
                render: function(data, type, row) {
                    return data ? data : '-';
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
        ];
    </script>

    {{-- TABLA DE MAESTROS ACTIVOS --}}
    <script>
        $('#tblmaestrosact').DataTable({
            scrollX: true,
            "lengthMenu": [
                [15, 20, 50, -1],
                [15, 20, 50, "Todos"]
            ],
            language: idiomaTabla,
            ajax: {
                url: '/ajaxConsulta/Maestros/act',
                dataSrc: 'data'
            },
            columns: columnasMaestro,
            columnDefs: [{
                targets: 3,
                orderable: false,
                render: function(data, type, row) {
                    return '<button onclick="buscarMaestro(' + row.idmaestro +
                        ');" type="button" class="btn btn-warning me-2" title="Editar"><i class="bi bi-pencil-square"></i></button>' +
                        '<a href="/upMaestro/' + row.idmaestro +
                        '/baja" class="btn btn-danger me-2" title="Dar de baja" onclick="return confirm(\'¿Estás seguro de dar de baja al maestro: ' +
                        escaparTexto(row.nombre_comp) + '?\')"><i class="bi bi-chevron-bar-down"></i></a>';
                }
            }, ],
        });

        function buscarMaestro(idmaestro) {
            $.ajax({
                url: "/ajaxConsulta/edit/" + idmaestro + "/maestro",
                type: 'GET',
                success: function(data) {
                    document.getElementById("id_maestro").value = data.idmaestro;
                    document.getElementById("nombre_comp_2").value = data.nombre_comp;
                    document.getElementById("telefono_2").value = data.telefono ?? '';

                    // Limpiar el aviso del teléfono de una edición anterior
                    validarTelefono(document.getElementById("telefono_2"), 'phoneErrorMessageEdit');

                    configurarValidacionesMaestro();
                    $('#editMaestro').modal('show');
                },
                error: function() {
                    alert('Error al cargar los datos del maestro.');
                }
            });
        }

        // Habilita "Guardar" solo si cambió el nombre o el teléfono
        function configurarValidacionesMaestro() {
            const nombre = document.getElementById('nombre_comp_2');
            const telefono = document.getElementById('telefono_2');
            const submitBtn = document.getElementById('submitEditMaestro');
            const nombreInicial = nombre.value.toUpperCase();
            const telefonoInicial = telefono.value;

            const revisar = function() {
                submitBtn.disabled = nombre.value.trim().toUpperCase() === nombreInicial &&
                    telefono.value === telefonoInicial;
            };

            submitBtn.disabled = true;
            nombre.oninput = revisar;
            telefono.oninput = function() {
                validarTelefono(telefono, 'phoneErrorMessageEdit');
                revisar();
            };
        }
    </script>

    {{-- TABLA DE MAESTROS INACTIVOS --}}
    <script>
        $('#tblmaestrosina').DataTable({
            scrollX: true,
            "lengthMenu": [
                [15, 20, 50, -1],
                [15, 20, 50, "Todos"]
            ],
            language: idiomaTabla,
            ajax: {
                url: '/ajaxConsulta/Maestros/ina',
                dataSrc: 'data'
            },
            columns: columnasMaestro,
            columnDefs: [{
                targets: 3,
                orderable: false,
                render: function(data, type, row) {
                    return '<a href="/upMaestro/' + row.idmaestro +
                        '/alta" class="btn btn-success me-2" title="Dar de alta" onclick="return confirm(\'¿Estás seguro de dar de alta al maestro: ' +
                        escaparTexto(row.nombre_comp) + '?\')"><i class="bi bi-chevron-bar-up"></i></a>';
                }
            }, ],
        });
    </script>
@endsection
