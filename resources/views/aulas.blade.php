@extends('layouts.carcasa')

@section('title', 'Aulas')
@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4">
                {{-- Errores de validación (ej. aula duplicada en el edificio) --}}
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
                            <b style="text-transform: uppercase">Registrar Aulas</b>
                            <br>
                            Listado de aulas por edificio
                        </p>
                        <div style=" text-align: right;">
                            <button type="button" class="btn btn-md btn-success" data-bs-toggle="modal"
                                data-bs-target="#addAula" title="Agregar aula">
                                <i class="bi bi-door-open"></i>
                            </button>
                        </div>
                    </div>
                    {{-- Aulas Activas --}}
                    <div class="card-body">
                        <h2 class="text-center">Aulas activas</h2>
                        <table id="tblaulasact" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Edificio</th>
                                    <th>Aula</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    {{-- Aulas inactivas --}}
                    <div class="card-body">
                        <h2 class="text-center">Aulas inactivas</h2>
                        <table id="tblaulasina" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Edificio</th>
                                    <th>Aula</th>
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
    @include('modals.aulas.addaula')
    @include('modals.aulas.editaula')
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

        // Escapa texto para mostrarlo en una celda
        function escaparHtml(texto) {
            return $('<div>').text(texto).html();
        }

        const columnasAula = [{
                data: 'edificio',
                render: function(data, type, row) {
                    if (type !== 'display') return data;
                    // Avisa si el edificio está inactivo (solo pasa con aulas inactivas)
                    return escaparHtml(data) + (row.edificio_estatus != 1 ?
                        ' <span class="badge bg-secondary">Edificio inactivo</span>' : '');
                }
            },
            {
                data: 'nombre'
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

    {{-- TABLA DE AULAS ACTIVAS --}}
    <script>
        $('#tblaulasact').DataTable({
            scrollX: true,
            "lengthMenu": [
                [15, 20, 50, -1],
                [15, 20, 50, "Todos"]
            ],
            language: idiomaTabla,
            ajax: {
                url: '/ajaxConsulta/Aulas/act',
                dataSrc: 'data'
            },
            columns: columnasAula,
            columnDefs: [{
                targets: 3,
                orderable: false,
                render: function(data, type, row) {
                    return '<button onclick="buscarAula(' + row.idaula +
                        ');" type="button" class="btn btn-warning me-2" title="Editar"><i class="bi bi-pencil-square"></i></button>' +
                        '<a href="/upAula/' + row.idaula +
                        '/baja" class="btn btn-danger me-2" title="Dar de baja" onclick="return confirm(\'¿Estás seguro de dar de baja el aula: ' +
                        escaparTexto(row.nombre + ' (' + row.edificio + ')') + '?\')"><i class="bi bi-chevron-bar-down"></i></a>';
                }
            }, ],
        });

        function buscarAula(idaula) {
            $.ajax({
                url: "/ajaxConsulta/edit/" + idaula + "/aula",
                type: 'GET',
                success: function(data) {
                    document.getElementById("id_aula").value = data.idaula;
                    document.getElementById("idedificio_2").value = data.idedificio;
                    document.getElementById("nombre_2").value = data.nombre;

                    configurarValidacionesAula();
                    $('#editAula').modal('show');
                },
                error: function() {
                    alert('Error al cargar los datos del aula.');
                }
            });
        }

        // Habilita "Guardar" solo si cambió el edificio o el nombre
        function configurarValidacionesAula() {
            const select = document.getElementById('idedificio_2');
            const input = document.getElementById('nombre_2');
            const submitBtn = document.getElementById('submitEditAula');
            const edificioInicial = select.value;
            const nombreInicial = input.value.toUpperCase();

            const revisar = function() {
                submitBtn.disabled = select.value === edificioInicial &&
                    input.value.trim().toUpperCase() === nombreInicial;
            };

            submitBtn.disabled = true;
            select.onchange = revisar;
            input.oninput = revisar;
        }
    </script>

    {{-- TABLA DE AULAS INACTIVAS --}}
    <script>
        $('#tblaulasina').DataTable({
            scrollX: true,
            "lengthMenu": [
                [15, 20, 50, -1],
                [15, 20, 50, "Todos"]
            ],
            language: idiomaTabla,
            ajax: {
                url: '/ajaxConsulta/Aulas/ina',
                dataSrc: 'data'
            },
            columns: columnasAula,
            columnDefs: [{
                targets: 3,
                orderable: false,
                render: function(data, type, row) {
                    return '<a href="/upAula/' + row.idaula +
                        '/alta" class="btn btn-success me-2" title="Dar de alta" onclick="return confirm(\'¿Estás seguro de dar de alta el aula: ' +
                        escaparTexto(row.nombre + ' (' + row.edificio + ')') + '?\')"><i class="bi bi-chevron-bar-up"></i></a>';
                }
            }, ],
        });
    </script>
@endsection
