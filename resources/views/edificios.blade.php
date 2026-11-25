@extends('layouts.carcasa')

@section('title', 'Edificios')
@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4">
                {{-- Errores de validación (ej. edificio duplicado) --}}
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
                            <b style="text-transform: uppercase">Registrar Edificios</b>
                            <br>
                            Listado de edificios
                        </p>
                        <div style=" text-align: right;">
                            <button type="button" class="btn btn-md btn-success" data-bs-toggle="modal"
                                data-bs-target="#addEdificio" title="Agregar edificio">
                                <i class="bi bi-building-add"></i>
                            </button>
                        </div>
                    </div>
                    {{-- Edificios Activos --}}
                    <div class="card-body">
                        <h2 class="text-center">Edificios activos</h2>
                        <table id="tbledificiosact" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Aulas activas</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    {{-- Edificios inactivos --}}
                    <div class="card-body">
                        <h2 class="text-center">Edificios inactivos</h2>
                        <table id="tbledificiosina" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Aulas activas</th>
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
    @include('modals.edificios.addedificio')
    @include('modals.edificios.editedificio')
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

        const columnasEdificio = [{
                data: 'nombre'
            },
            {
                data: 'aulas_activas'
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

    {{-- TABLA DE EDIFICIOS ACTIVOS --}}
    <script>
        $('#tbledificiosact').DataTable({
            scrollX: true,
            "lengthMenu": [
                [15, 20, 50, -1],
                [15, 20, 50, "Todos"]
            ],
            language: idiomaTabla,
            ajax: {
                url: '/ajaxConsulta/Edificios/act',
                dataSrc: 'data'
            },
            columns: columnasEdificio,
            columnDefs: [{
                targets: 3,
                orderable: false,
                render: function(data, type, row) {
                    return '<button onclick="buscarEdificio(' + row.idedificio +
                        ');" type="button" class="btn btn-warning me-2" title="Editar"><i class="bi bi-pencil-square"></i></button>' +
                        '<a href="/upEdificio/' + row.idedificio +
                        '/baja" class="btn btn-danger me-2" title="Dar de baja" onclick="return confirm(\'¿Estás seguro de dar de baja el edificio: ' +
                        escaparTexto(row.nombre) + '?\')"><i class="bi bi-chevron-bar-down"></i></a>';
                }
            }, ],
        });

        function buscarEdificio(idedificio) {
            $.ajax({
                url: "/ajaxConsulta/edit/" + idedificio + "/edificio",
                type: 'GET',
                success: function(data) {
                    document.getElementById("id_edificio").value = data.idedificio;
                    document.getElementById("nombre_2").value = data.nombre;

                    configurarValidacionesEdificio();
                    $('#editEdificio').modal('show');
                },
                error: function() {
                    alert('Error al cargar los datos del edificio.');
                }
            });
        }

        // Habilita "Guardar" solo si cambió algo
        function configurarValidacionesEdificio() {
            const input = document.getElementById('nombre_2');
            const submitBtn = document.getElementById('submitEditEdificio');
            const valorInicial = input.value;

            submitBtn.disabled = true;
            input.oninput = function() {
                submitBtn.disabled = input.value.trim().toUpperCase() === valorInicial.toUpperCase();
            };
        }
    </script>

    {{-- TABLA DE EDIFICIOS INACTIVOS --}}
    <script>
        $('#tbledificiosina').DataTable({
            scrollX: true,
            "lengthMenu": [
                [15, 20, 50, -1],
                [15, 20, 50, "Todos"]
            ],
            language: idiomaTabla,
            ajax: {
                url: '/ajaxConsulta/Edificios/ina',
                dataSrc: 'data'
            },
            columns: columnasEdificio,
            columnDefs: [{
                targets: 3,
                orderable: false,
                render: function(data, type, row) {
                    return '<a href="/upEdificio/' + row.idedificio +
                        '/alta" class="btn btn-success me-2" title="Dar de alta" onclick="return confirm(\'¿Estás seguro de dar de alta el edificio: ' +
                        escaparTexto(row.nombre) + '?\')"><i class="bi bi-chevron-bar-up"></i></a>';
                }
            }, ],
        });
    </script>
@endsection
