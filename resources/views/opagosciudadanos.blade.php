@extends('layouts.carcasa')

@section('title', 'O. pagos rentas')
@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <p>
                            <b style="text-transform: uppercase">Ordenes de pago de Ciudadanos</b>
                            <br>
                            Listado de ciudadanos que rentaron un espacio
                        </p>
                        {{-- <div style=" text-align: right;">
                            <button type="button" class="btn btn-md btn-success" data-bs-toggle="modal"
                                data-bs-target="#addAlumno">
                                <i class="bi bi-backpack"></i>
                            </button>
                        </div> --}}
                    </div>
                    {{-- Ordenes de Pago activas --}}
                    <div class="card-body">
                        <h2 class="text-center">Rentas</h2>
                        <table id="tbloprentas" class="table table-striped dt-responsive nowrap border" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Teléfono</th>
                                    <th>Espacio</th>
                                    <th>Fecha de evento</th>
                                    <th>Hora de inicio</th>
                                    <th>Hora de fin</th>
                                    <th>Conceptos</th>
                                    <th>Cantidad</th>
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
    </div>

{{-- Modales --}}
@include('modals.opagos.rentas.showoprenta')
@endsection

@section('scripts')
    {{-- -------------- TABLA DE INSCRIPCIONES -------------- --}}
    <script>
        // $(document).ready(function() {
        //     $("#id_concepto").select2({
        //         dropdownParent: $('#addOpagoInsc'),
        //         width: '100%' // Ajusta el ancho al 100%
        //     });
        // });
        // $(document).ready(function() {
        //     $("#id_funlegal").select2({
        //         dropdownParent: $('#addOpagoInsc'),
        //         width: '100%' // Ajusta el ancho al 100%
        //     });
        // });

        // $(document).ready(function() {
        var table = $('#tbloprentas').DataTable({
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
                url: '/ajaxConsulta/OpagosRentas',
                dataSrc: 'data'
            },
            columns: [
                {
                    data: 'nombreCompleto'
                },
                {
                    data: 'nameroom'
                },
                {
                    data: 'fechaevento'
                },
                {
                    data: 'horainicio'
                },
                {
                    data: 'horafin'
                },
                {
                    data: 'conceptosxr'
                },
                {
                    data: 'cantidadxrenta'
                },
                {
                    data: 'montototal',
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
                targets: 9,
                orderable: false,
                render: function(data, type, row) {
                    return `<button onclick="visualizarOrdenRenta(${row.idopago});" type="button" class="btn btn-primary me-2" title="Visualizar Ordenes" data-bs-toggle="modal" data-bs-target="#showOpagoRenta">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                `;
                }
            }, ],
        });
        // });

        function visualizarOrdenRenta(idopago) {
            // alert('Holis: ' + idinscripcion);
            // Hacer solicitud AJAX para obtener los datos personales
            $.ajax({
                url: "/ajaxConsulta/" + idopago + "/opagociudadanorenta",
                type: 'GET',
                success: function(response) {
                    console.log(response);
                    // Verificar que haya datos en la respuesta
                    if (response.data) {
                        let data = response.data; // Acceder al primer elemento del array
                        let estatusTexto = data.estatuspago == "1" ? "Pagado" : "Pendiente de pago";

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
                        document.getElementById("espacio").value = data.nameroom;
                        document.getElementById("concepto").value = data.concepto;
                        document.getElementById("montototal").value = data.montototal;
                        // document.getElementById("fechalimite").value = data.fechalimite;
                        document.getElementById("estatuspago").value = estatusTexto;

                        // Rellenar los campos del formulario con los datos obtenidos
                        // $('#foliocc').val(data.foliocc);

                        // Habilitar el botón si hay cambios en los campos
                        // configurarValidaciones();

                        // Mostrar el modal
                        $('#showOpagoRenta').modal('show');
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
@endsection
