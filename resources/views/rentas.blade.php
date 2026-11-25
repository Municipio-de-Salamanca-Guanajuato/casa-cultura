@extends('layouts.carcasa')

@section('title', 'Espacios')
@section('content')
    @if ($errors->any())
        {{ implode('', $errors->all('<div>:message</div>')) }}
    @endif
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <p>
                            <b style="text-transform: uppercase">Registrar Rentas</b>
                            <br>
                            Listado de rentas
                        </p>
                        <div style=" text-align: right;">
                            <button type="button" onclick="cargarEspaciosConceptos()" class="btn btn-md btn-success"
                                data-bs-toggle="modal" data-bs-target="#addRenta" title="Registrar renta">
                                <i class="fa-solid fa-address-book"></i>
                            </button>
                            {{-- <a href="/exportar-reporte-rentas" class="btn btn-warning" title="Repote rentas"
                                style="color: white;">
                                <i class="fa-solid fa-file-arrow-down"></i>
                            </a> --}}
                        </div>
                    </div>
                    {{-- Rentas activas --}}
                    <div class="card-body">
                        <h2 class="text-center">Rentas activas</h2>
                        <table id="tblrentasact" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
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
                    {{-- Rentas inactivas --}}
                    <div class="card-body">
                        <h2 class="text-center">Rentas inactivas</h2>
                        <table id="tblrentasina" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
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

    {{-- Modales --}}
    @include('modals.rentas.addrenta')
    @include('modals.rentas.editrenta')
    @include('modals.opagos.rentas.showoprenta')
@endsection

@section('scripts')
    {{-- TABLA DE RENTAS ACTIVAS --}}
    <script>
        // SCRIPT DE SELECT2 PARA MOSTRAR ESPACIOS
        $(document).ready(function() {
            $("#id_ciudadano").select2({
                dropdownParent: $('#addRenta'),
                width: '100%' // Ajusta el ancho al 100%
            });
        });
        $(document).ready(function() {
            $("#id_espacio").select2({
                dropdownParent: $('#addRenta'),
                width: '100%' // Ajusta el ancho al 100%
            });
        });
        $(document).ready(function() {
            $("#id_cxrenta").select2({
                dropdownParent: $('#addRenta'),
                width: '100%' // Ajusta el ancho al 100%
            });
        });

        // $(document).ready(function() {
        var table = $('#tblrentasact').DataTable({
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
                url: '/ajaxConsulta/Rentas/act',
                dataSrc: 'data'
            },
            columns: [{
                    data: 'name'
                },
                {
                    data: 'telefono'
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
                targets: 9,
                orderable: false,
                render: function(data, type, row) {
                    return '<button onclick="visualizarOrdenRenta(' + data.idrenta +
                        ');" type="button" class="btn btn-primary me-2" data-bs-toggle="modal" title="Visualizar Ordenes" data-bs-target="#showOpagoRenta"><i class="fa-solid fa-eye"></i></button>' +
                        '<a href="/upRenta/' + row.idrenta +
                        '/baja" class="btn btn-danger me-2" onclick="return confirm(\'¿Estás seguro de dar de baja la renta de: ' +
                        row.name + '?\')" ><i class="bi bi-chevron-bar-down"></i></a>';
                    // <a class = 'btn btn-success btn-sm ms-2' 
                    // href = '{{ url('/upFactor/ + row.id_factor + /baja') }}'
                    // onclick = "return confirm('¿Estás seguro de dar de alta el factor?')" >
                    //     Alta </a>
                }
            }, ],
        });
        // });

        function visualizarOrdenRenta(idrenta) {
            // console.log('Holis: ' + idrenta);
            // Hacer solicitud AJAX para obtener los datos personales
            $.ajax({
                url: "/ajaxConsulta/" + idrenta + "/opagociudadanorenta",
                type: 'GET',
                success: function(response) {
                    console.log(response);
                    // Verificar que haya datos en la respuesta
                    if (response.data && response.data.length > 0) {
                        let data = response.data[0]; // Acceder al primer elemento del array
                        let estatusTexto = data.estatuspago == "1" ? "Pagado" : "Pendiente de pago";
                        let estatusClass = data.estatuspago == "1" ? "bg-success text-white" : "";

                        $('#id_renta').val(idrenta);
                        // Llenar los campos del modal con los datos recibidos
                        $('#id_renta').val(idrenta);
                        $('#foliocc').val(data.foliocc);
                        $('#foliocajas').val(data.foliocajas);
                        $('#concepto').val(data.concepto);
                        $('#montototal').val(data.montototal);
                        // Llenar el campo de estatus con clase condicional
                        $('#estatuspago')
                            .val(estatusTexto)
                            .removeClass('bg-success text-white') // Limpiar clases previas
                            .addClass(estatusClass); // Aplicar clase según estatus
                        $('#fechapago').val(data.fechapago);
                        // $('#foliocc').val(data.data.foliocc);
                        // $('#foliocajas').val(data.foliocajas);
                        // $('#name').val(data.nombreCompleto);
                        // $('#taller').val(data.nametaller);
                        // $('#concepto').val(data.idconcepto); // Ajusta si el campo "concepto" es diferente
                        // $('#montototal').val(data.montototal);
                        // $('#estatuspago').val(data.estatusTexto);

                        // Llenar los campos del modal con los datos recibidos
                        // document.getElementById("foliocc").value = data.foliocc;
                        // document.getElementById("foliocajas").value = data.foliocajas;
                        // document.getElementById("concepto").value = data.concepto;
                        // document.getElementById("montototal").value = data.montototal;
                        // document.getElementById("fechalimite").value = data.fechalimite;
                        // document.getElementById("estatuspago").value = estatusTexto;

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
    </script>

    {{-- TABLA DE RENTAS INACTIVAS --}}
    <script>
        // $(document).ready(function() {
        var table = $('#tblrentasina').DataTable({
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
                url: '/ajaxConsulta/Rentas/ina',
                dataSrc: 'data'
            },
            columns: [{
                    data: 'name'
                },
                {
                    data: 'telefono'
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
                targets: 9,
                orderable: false,
                render: function(data, type, row) {
                    return '<a href="/upRenta/' + row.idrenta +
                        '/alta" class="btn btn-success me-2" onclick="return confirm(\'¿Estás seguro de dar de alta la renta de: ' +
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
    {{-- SCRIPT PARA VALIDAR EL TELEFONO --}}
    {{-- VALIDACIÓN MEJORADA DE TELÉFONO --}}
    <script>
        function validarTelefono(input, errorMessageId) {
            // Obtener el valor actual eliminando cualquier carácter no numérico
            let telefono = input.value.replace(/\D/g, '');

            // Limitar a 10 dígitos
            telefono = telefono.substring(0, 10);

            // Actualizar el valor en el input (sin caracteres no numéricos)
            input.value = telefono;

            // Obtener el elemento del mensaje de error
            let errorMessage = document.getElementById(errorMessageId);

            // Validaciones
            if (telefono.length !== 10) {
                errorMessage.style.display = 'block';
                errorMessage.innerText = 'El teléfono debe contener exactamente 10 dígitos.';
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
            } else {
                errorMessage.style.display = 'none';
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            }
        }

        $(document).ready(function() {
            // Validación al enviar el formulario
            $('form').on('submit', function(e) {
                const telefonoInput = $('#telefono');
                const telefono = telefonoInput.val();

                if (telefonoInput.is(':visible') && telefono.length !== 10) {
                    // Mostrar mensaje de error
                    $('#phoneErrorMessageAdd').css('display', 'block').text(
                        'El teléfono debe contener 10 dígitos.');
                    telefonoInput.addClass('is-invalid');

                    // Prevenir el envío del formulario
                    e.preventDefault();

                    // Hacer scroll al campo con error
                    $('html, body').animate({
                        scrollTop: telefonoInput.offset().top - 100
                    }, 500);
                }
            });

            // Opcional: Validar también al perder el foco
            $('#telefono').on('blur', function() {
                validarTelefono(this, 'phoneErrorMessageAdd');
            });
        });
    </script>
    {{-- SCRIPT PARA MOSTRAR/OCULTAR INPUT DE RFC --}}
    {{-- <script>
        function toggleInput() {
            const checkbox = document.getElementById('flexCheckDefault');
            const rfcContainer = document.getElementById('rfcContainer');
            const rfcInput = document.getElementById('rfc');
            const submitButton = document.getElementById('submitAddRenta');

            // Mostrar el input si el checkbox está marcado, ocultarlo si no
            rfcContainer.style.display = checkbox.checked ? 'block' : 'none';

            // Opcional: Limpiar el contenido del RFC si se oculta
            // if (!checkbox.checked) {
            //     document.getElementById('rfc').value = '';
            // }

            // Cambiar el atributo required según el estado del checkbox
            if (checkbox.checked) {
                rfcInput.setAttribute('required', 'required');
            } else {
                rfcInput.removeAttribute('required');
                rfcInput.value = ''; // Limpiar el valor cuando se oculta
            }
        }
        // Validación antes de enviar el formulario
        document.getElementById('submitAddRenta').addEventListener('click', function(event) {
            const checkbox = document.getElementById('flexCheckDefault');
            const rfcInput = document.getElementById('rfc');

            if (checkbox.checked && !rfcInput.value.trim()) {
                event.preventDefault(); // Evitar envío del formulario
                // Mostrar mensaje de error
                document.getElementById('rfcErrorAdd').style.display = 'block';
                document.getElementById('rfcErrorAdd').textContent =
                    'El RFC es requerido.';
                // Enfocar el campo RFC
                rfcInput.focus();
            } else {
                // Ocultar mensaje de error si existe
                document.getElementById('rfcErrorAdd').style.display = 'none';
            }
        });
    </script> --}}
    {{-- SCRIPT PARA VALIDAR RFC --}}
    {{-- <script>
        function validarRFC(input) {
            const rfc = input.value.toUpperCase().trim(); // Convierte a mayúsculas y elimina espacios en blanco
            const errorMessage = document.getElementById('rfcErrorAdd');

            // Expresión regular para RFC: 12 o 13 caracteres (letras, números o &)
            const regex = /^[A-Z&Ñ]{3,4}\d{6}[A-Z0-9]{3}$/;

            // Verificar si el campo está vacío (opcionalidad)
            if (rfc === '') {
                errorMessage.style.display = 'none'; // No mostrar error si el campo está vacío
                return;
            }

            // Validar longitud: 12 o 13 caracteres
            if (rfc.length !== 12 && rfc.length !== 13) {
                errorMessage.style.display = 'block';
                errorMessage.innerText = 'El RFC debe tener 12 o 13 caracteres.';
                return;
            }

            // Validar formato con la expresión regular
            if (!regex.test(rfc)) {
                errorMessage.style.display = 'block';
                errorMessage.innerText = 'El RFC no tiene un formato válido.';
            } else {
                errorMessage.style.display = 'none'; // Ocultar el error si es válido
            }
        }

        // Validar antes de enviar el formulario
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                const rfcInput = $('#rfc').val().trim();

                // Si el RFC tiene valor, validarlo antes de enviar
                if (rfcInput !== '') {
                    const regex = /^[A-Z&Ñ]{3,4}\d{6}[A-Z0-9]{3}$/;
                    if (!regex.test(rfcInput) || (rfcInput.length !== 12 && rfcInput.length !== 13)) {
                        alert('El RFC no es válido. Verifica el formato.');
                        e.preventDefault(); // Evita que se envíe el formulario
                    }
                }
            });
        });
    </script> --}}
    <script>
        // Mostrar u ocultar input RFC según el checkbox
        function toggleInput() {
            const checkbox = document.getElementById('flexCheckDefault');
            const rfcContainer = document.getElementById('rfcContainer');
            const rfcInput = document.getElementById('rfc');
            const rfcError = document.getElementById('rfcErrorAdd');

            if (checkbox.checked) {
                rfcContainer.style.display = 'block';
                rfcInput.setAttribute('required', 'required');
            } else {
                rfcContainer.style.display = 'none';
                rfcInput.removeAttribute('required');
                rfcInput.value = '';
                rfcError.style.display = 'none'; // Limpia el mensaje si se oculta
            }
        }

        // Quitar espacios automáticamente mientras escribe
        document.addEventListener('DOMContentLoaded', function() {
            const rfcInput = document.getElementById('rfc');
            rfcInput.addEventListener('input', function() {
                this.value = this.value.replace(/\s/g, '').toUpperCase(); // sin espacios, y en mayúsculas
            });
        });

        // Validar formato RFC (desde oninput)
        function validarRFC(input) {
            const rfc = input.value.toUpperCase().trim();
            const errorMessage = document.getElementById('rfcErrorAdd');
            const regex = /^[A-Z&Ñ]{3,4}\d{6}[A-Z0-9]{3}$/;

            if (rfc === '') {
                errorMessage.style.display = 'none';
                return;
            }

            if (rfc.length !== 12 && rfc.length !== 13) {
                errorMessage.style.display = 'block';
                errorMessage.innerText = 'El RFC debe tener 12 o 13 caracteres.';
                return;
            }

            if (!regex.test(rfc)) {
                errorMessage.style.display = 'block';
                errorMessage.innerText = 'El RFC no tiene un formato válido.';
            } else {
                errorMessage.style.display = 'none';
            }
        }

        // Validación final antes de enviar el formulario
        document.getElementById('submitAddRenta').addEventListener('click', function(event) {
            const checkbox = document.getElementById('flexCheckDefault');
            const rfcInput = document.getElementById('rfc');
            const rfcError = document.getElementById('rfcErrorAdd');
            const rfc = rfcInput.value.toUpperCase().trim();
            const regex = /^[A-Z&Ñ]{3,4}\d{6}[A-Z0-9]{3}$/;

            // Solo validar si se requiere factura
            if (checkbox.checked) {
                if (rfc === '') {
                    event.preventDefault();
                    rfcError.style.display = 'block';
                    rfcError.textContent = 'El RFC es requerido.';
                    rfcInput.focus();
                    return;
                }

                if (rfc.length !== 12 && rfc.length !== 13) {
                    event.preventDefault();
                    rfcError.style.display = 'block';
                    rfcError.textContent = 'El RFC debe tener 12 o 13 caracteres.';
                    rfcInput.focus();
                    return;
                }

                if (!regex.test(rfc)) {
                    event.preventDefault();
                    rfcError.style.display = 'block';
                    rfcError.textContent = 'El RFC no tiene un formato válido.';
                    rfcInput.focus();
                    return;
                }

                // Si es válido, oculta el error
                rfcError.style.display = 'none';
            }
        });
    </script>

    {{-- SCRIPT PARA CARGAR ESPACIOS Y CONCEPTOS POR RENTA Y FUNCIONALIDAD DE CARGAR LA TABLA EN EL MODAL DINAMICAMENTE --}}
    <script>
        // Variables globales
        let conceptosAgregados = [];
        let conceptosDisponibles = [];
        let costoEspacio = 0;
        let nombresConceptos = [];
        let espaciosCargados = []; // Para controlar espacios duplicados

        $(document).ready(function() {
            $('#addRenta').on('show.bs.modal', cargarEspaciosConceptos);
            $('#btnAgregarConcepto').click(agregarConcepto);
        });

        function cargarEspaciosConceptos() {
            $('#id_espacio').empty().append('<option value="" selected>Seleccione un espacio</option>');
            $('#id_cxrenta').empty().append('<option value="" selected>Seleccione un concepto</option>');
            espaciosCargados = []; // Reiniciar control de espacios

            $.ajax({
                url: "/ajaxConsulta/Rentas/mostrar",
                type: 'GET',
                success: function(data) {
                    // Llenar espacios sin duplicados
                    data.espacios.forEach(function(espacio) {
                        if (!espaciosCargados.includes(espacio.idespacio)) {
                            $('#id_espacio').append(
                                `<option value="${espacio.idespacio}" data-costo="${espacio.costo}">${espacio.nameroom} - $${espacio.costo}</option>`
                            );
                            espaciosCargados.push(espacio.idespacio);
                        }
                    });

                    conceptosDisponibles = data.conceptosxrenta;
                    actualizarSelectConceptos();

                    $('#id_espacio').change(function() {
                        const selected = $(this).find('option:selected');
                        costoEspacio = parseFloat(selected.data('costo')) || 0;
                        actualizarTotalGeneral();
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Error al cargar datos');
                }
            });
        }

        function actualizarSelectConceptos() {
            $('#id_cxrenta').empty().append('<option value="" selected>Seleccione un concepto</option>');

            conceptosDisponibles.forEach(function(concepto) {
                if (!conceptosAgregados.includes(concepto.idcxrenta.toString())) {
                    $('#id_cxrenta').append(
                        `<option value="${concepto.idcxrenta}" data-costo="${concepto.costo}">${concepto.namecxrenta} - $${concepto.costo}</option>`
                    );
                }
            });
        }

        function agregarConcepto() {
            const select = $('#id_cxrenta');
            const conceptoId = select.val();

            if (!conceptoId) {
                alert('Seleccione un concepto');
                return;
            }

            if (conceptosAgregados.includes(conceptoId)) {
                alert('Este concepto ya fue agregado');
                return;
            }

            const option = select.find('option:selected');
            const nombre = option.text().split(' - ')[0];
            const costo = parseFloat(option.data('costo'));

            conceptosAgregados.push(conceptoId);
            nombresConceptos.push(nombre);

            const nuevaFila = `
            <tr id="concepto-${conceptoId}">
                <td>
                    ${nombre}
                    <input type="hidden" name="id_cxrenta[]" value="${conceptoId}">
                </td>
                <td>
                    <input type="number" min="1" max="99" value="1" name="cantidad[]"
                        class="form-control form-control-sm horas"
                        onchange="calcularConcepto('${conceptoId}')">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm costo" name="costo[]"
                        value="${costo.toFixed(2)}" readonly>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm total" name="total_cxr[]"
                        value="${costo.toFixed(2)}" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" 
                        onclick="eliminarConcepto('${conceptoId}', '${nombre.replace(/'/g, "\\'")}')">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;

            $('#tablaConceptos tbody').append(nuevaFila);
            actualizarSelectConceptos();
            select.val('');
            actualizarTotalGeneral();
        }

        function calcularConcepto(conceptoId) {
            const fila = $(`#concepto-${conceptoId}`);
            const horas = parseFloat(fila.find('.horas').val()) || 0;
            const costo = parseFloat(fila.find('.costo').val()) || 0;
            const total = horas * costo;

            fila.find('.total').val(total.toFixed(2));
            actualizarTotalGeneral();
        }

        function eliminarConcepto(conceptoId, nombre) {
            $(`#concepto-${conceptoId}`).remove();
            conceptosAgregados = conceptosAgregados.filter(id => id !== conceptoId);
            nombresConceptos = nombresConceptos.filter(n => n !== nombre);
            actualizarSelectConceptos();
            actualizarTotalGeneral();
        }

        function actualizarTotalGeneral() {
            let totalConceptos = 0;

            // Calcular suma de conceptos
            $('#tablaConceptos tbody tr').each(function() {
                totalConceptos += parseFloat($(this).find('.total').val()) || 0;
            });

            // Calcular total general
            const totalGeneral = costoEspacio + totalConceptos;

            // Construir el texto del título
            let textoTotal = 'TOTAL RENTA DEL ESPACIO';
            if (nombresConceptos.length > 0) {
                textoTotal += ' + ' + nombresConceptos.join(', ');
            }

            // Actualizar elementos separadamente
            $('#tituloTotal').text(textoTotal);
            $('#montoTotal').text('$' + totalGeneral.toFixed(2));

            // Actualizar campo hidden
            $('#totalGeneralHidden').val(totalGeneral.toFixed(2));
            // textoTotal += ': $' + totalGeneral.toFixed(2);
            // $('#totalGeneral').html(textoTotal);
            // $('#totalGeneralHidden').val(totalGeneral.toFixed(2));
        }

        $('#addRentaForm').submit(function(e) {
            e.preventDefault();

            const conceptos = [];
            $('#tablaConceptos tbody tr').each(function() {
                const id = $(this).attr('id').replace('concepto-', '');
                conceptos.push({
                    id_cxrenta: id,
                    horas_extra: $(this).find('.horas').val(),
                    costo: $(this).find('.costo').val(),
                    total: $(this).find('.total').val()
                });
            });

            $('<input>').attr({
                type: 'hidden',
                name: 'conceptos',
                value: JSON.stringify(conceptos)
            }).appendTo('#addRentaForm');

            this.submit();
        });
    </script>
    {{-- <script>
        // Variables globales
        let conceptosAgregados = [];
        let conceptosDisponibles = [];
        let costoEspacio = 0;
        let nombresConceptos = []; // Nuevo array para almacenar nombres de conceptos

        $(document).ready(function() {
            // Cargar conceptos cuando se muestre el modal
            $('#addRenta').on('show.bs.modal', function() {
                cargarEspaciosConceptos();
            });

            // Manejar el clic en el botón Agregar Concepto
            $('#btnAgregarConcepto').click(agregarConcepto);
        });

        function cargarEspaciosConceptos() {
            // Limpiar selects
            $('#id_espacio').empty().append('<option value="" selected>Seleccione un espacio</option>');
            $('#id_cxrenta').empty().append('<option value="" selected>Seleccione un concepto</option>');

            $.ajax({
                url: "/ajaxConsulta/Rentas/mostrar",
                type: 'GET',
                success: function(data) {
                    // Llenar espacios
                    data.espacios.forEach(function(espacio) {
                        $('#id_espacio').append(
                            `<option value="${espacio.idespacio}" data-costo="${espacio.costo}">${espacio.nameroom} - $${espacio.costo}</option>`
                        );
                    });

                    // Guardar conceptos disponibles
                    conceptosDisponibles = data.conceptosxrenta;
                    actualizarSelectConceptos();

                    // Evento para capturar costo del espacio
                    $('#id_espacio').change(function() {
                        const selected = $(this).find('option:selected');
                        costoEspacio = parseFloat(selected.data('costo')) || 0;
                        actualizarTotalGeneral();
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Error al cargar datos');
                }
            });
        }

        function actualizarSelectConceptos() {
            $('#id_cxrenta').empty().append('<option value="" selected>Seleccione un concepto</option>');

            conceptosDisponibles.forEach(function(concepto) {
                if (!conceptosAgregados.includes(concepto.idcxrenta.toString())) {
                    $('#id_cxrenta').append(
                        `<option value="${concepto.idcxrenta}" data-costo="${concepto.costo}">${concepto.namecxrenta} - $${concepto.costo}</option>`
                    );
                }
            });
        }

        function agregarConcepto() {
            const select = $('#id_cxrenta');
            const conceptoId = select.val();

            if (!conceptoId) {
                alert('Seleccione un concepto');
                return;
            }

            if (conceptosAgregados.includes(conceptoId)) {
                alert('Este concepto ya fue agregado');
                return;
            }

            const option = select.find('option:selected');
            const nombre = option.text().split(' - ')[0];
            const costo = parseFloat(option.data('costo'));

            // Agregar a arrays de control
            conceptosAgregados.push(conceptoId);
            nombresConceptos.push(nombre); // Almacenar nombre del concepto

            // Crear fila de tabla
            const nuevaFila = `
            <tr id="concepto-${conceptoId}">
                <td>${nombre}</td>
                <td>
                    <input type="number" min="0" max="99" value="1" 
                        class="form-control form-control-sm horas" 
                        onchange="calcularConcepto('${conceptoId}')">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm costo" 
                        value="${costo.toFixed(2)}" readonly>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm total" 
                        value="${costo.toFixed(2)}" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" 
                        onclick="eliminarConcepto('${conceptoId}', '${nombre.replace(/'/g, "\\'")}')">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;

            $('#tablaConceptos tbody').append(nuevaFila);
            actualizarSelectConceptos();
            select.val('');
            actualizarTotalGeneral();
        }

        function calcularConcepto(conceptoId) {
            const fila = $(`#concepto-${conceptoId}`);
            const horas = parseFloat(fila.find('.horas').val()) || 0;
            const costo = parseFloat(fila.find('.costo').val()) || 0;
            const total = horas * costo;

            fila.find('.total').val(total.toFixed(2));
            actualizarTotalGeneral();
        }

        function eliminarConcepto(conceptoId, nombre) {
            $(`#concepto-${conceptoId}`).remove();
            conceptosAgregados = conceptosAgregados.filter(id => id !== conceptoId);
            nombresConceptos = nombresConceptos.filter(n => n !== nombre); // Eliminar nombre del concepto
            actualizarSelectConceptos();
            actualizarTotalGeneral();
        }

        function actualizarTotalGeneral() {
            let totalConceptos = 0;

            // Calcular suma de todos los conceptos
            $('#tablaConceptos tbody tr').each(function() {
                totalConceptos += parseFloat($(this).find('.total').val()) || 0;
            });

            // Calcular total general (espacio + conceptos)
            const totalGeneral = costoEspacio + totalConceptos;

            // Crear texto descriptivo
            let textoTotal = 'Total de renta de espacio';
            if (nombresConceptos.length > 0) {
                textoTotal += ' más ' + nombresConceptos.join(', ');
            }
            textoTotal += ': $' + totalGeneral.toFixed(2);

            // Actualizar el footer de la tabla
            $('#totalGeneral').html('<span class="fw-bold">' + textoTotal + '</span>');

            // Actualizar campo hidden para el formulario
            $('#totalGeneralHidden').val(totalGeneral.toFixed(2));
        }

        // Manejar envío del formulario
        $('#addRentaForm').submit(function(e) {
            e.preventDefault();

            // Recopilar conceptos
            const conceptos = [];
            $('#tablaConceptos tbody tr').each(function() {
                const id = $(this).attr('id').replace('concepto-', '');
                conceptos.push({
                    id_cxrenta: id,
                    horas_extra: $(this).find('.horas').val(),
                    costo: $(this).find('.costo').val(),
                    total: $(this).find('.total').val()
                });
            });

            // Agregar campo hidden con los conceptos
            $('<input>').attr({
                type: 'hidden',
                name: 'conceptos',
                value: JSON.stringify(conceptos)
            }).appendTo('#addRentaForm');

            // Enviar formulario
            this.submit();
        });
    </script> --}}
    {{-- <script>
        // Variables globales
        let conceptosAgregados = [];
        let conceptosDisponibles = [];
        // Variable para almacenar el costo del espacio
        let costoEspacio = 0;
        let totalConceptos = 0;

        $(document).ready(function() {
            // Cargar conceptos cuando se muestre el modal
            $('#addRenta').on('show.bs.modal', function() {
                cargarEspaciosConceptos();
            });

            // Manejar el clic en el botón Agregar Concepto
            $('#btnAgregarConcepto').click(agregarConcepto);
        });

        function cargarEspaciosConceptos() {
            // Limpiar los selects antes de llenarlos
            // $('#id_ciudadano').empty().append('<option value="" selected>Seleccione un ciudadano</option>');
            $('#id_espacio').empty().append('<option value="" selected>Seleccione un espacio</option>');
            $('#id_cxrenta').empty().append('<option value="" selected>Seleccione un concepto</option>');

            // Llamada Ajax
            $.ajax({
                url: "/ajaxConsulta/Rentas/mostrar", // Ruta definida
                type: 'GET',
                success: function(data) {
                    // Verificar si llegan los datos
                    console.log(data);

                    // Llenar el select de ciudadanos
                    // data.ciudadanos.forEach(function(ciudadano) {
                    //     $('#id_ciudadano').append(
                    //         `<option value="${ciudadano.idciudadano}">${ciudadano.nombreCompleto}</option>`
                    //     );
                    // });
                    // Guardar conceptos disponibles y llenar select
                    conceptosDisponibles = data.conceptosxrenta;

                    // Llenar el select de espacios
                    data.espacios.forEach(function(espacio) {
                        $('#id_espacio').append(
                            `<option value="${espacio.idespacio}" data-costo="${espacio.costo}>${espacio.nameroom} - Cupo: ${espacio.cupo} - $${espacio.costo}</option>`
                        );
                    });

                    // Evento para capturar el costo del espacio seleccionado
                    $('#id_espacio').change(function() {
                        const espacioSeleccionado = $(this).find('option:selected');
                        costoEspacio = parseFloat(espacioSeleccionado.data('costo')) || 0;
                        calcularTotalGeneral(); // Actualizar el total cuando cambia el espacio
                    });

                    // Llenar el select de conceptos por renta

                    // data.conceptosxrenta.forEach(function(concepto) {
                    //     $('#id_cxrenta').append(
                    //         `<option value="${concepto.idcxrenta}">${concepto.namecxrenta} - $${concepto.costo}</option>`
                    //     );
                    // });

                    // Llenar select de conceptos con los no agregados
                    actualizarSelectConceptos();

                    // Mostrar el modal
                    // $('#addRenta').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Ocurrió un error al cargar los datos.');
                }
            });
        }

        function actualizarSelectConceptos() {
            // Limpiar y agregar opción por defecto
            $('#id_cxrenta').empty().append('<option value="" selected>Seleccione un concepto</option>');

            // Agregar solo los conceptos no seleccionados
            conceptosDisponibles.forEach(function(concepto) {
                if (!conceptosAgregados.includes(concepto.idcxrenta.toString())) {
                    $('#id_cxrenta').append(
                        `<option value="${concepto.idcxrenta}" data-costo="${concepto.costo}">${concepto.namecxrenta} - $${concepto.costo}</option>`
                    );
                }
            });
        }

        function agregarConcepto() {
            const select = $('#id_cxrenta');
            const conceptoId = select.val();

            if (!conceptoId) {
                alert('Por favor seleccione un concepto');
                return;
            }

            // Verificar si el concepto ya fue agregado
            if (conceptosAgregados.includes(conceptoId)) {
                alert('Este concepto ya fue agregado');
                return;
            }

            // Obtener datos del concepto seleccionado
            const conceptoOption = select.find('option:selected');
            const conceptoNombre = conceptoOption.text().split(' - ')[0];
            const conceptoCosto = parseFloat(conceptoOption.data('costo'));

            // Validar que el costo sea un número válido
            if (isNaN(conceptoCosto)) {
                console.error('Costo no es un número válido:', conceptoOption.data('costo'));
                alert('Error: El costo del concepto no es válido');
                return;
            }

            // Agregar a la lista de conceptos agregados
            conceptosAgregados.push(conceptoId);

            // Crear fila en la tabla
            const nuevaFila = `
                <tr id="concepto-${conceptoId}">
                    <td>${conceptoNombre}</td>
                    <td>
                        <input type="number" min="0" max="99" value="1" 
                            class="form-control horas" 
                            data-concepto-id="${conceptoId}"
                            onchange="calcularConcepto('${conceptoId}')">
                    </td>
                    <td>
                        <input type="text" value="${conceptoCosto.toFixed(2)}" 
                            class="form-control costo" readonly>
                    </td>
                    <td>
                        <input type="text" value="${conceptoCosto.toFixed(2)}" 
                            class="form-control total" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" 
                                onclick="eliminarConcepto('${conceptoId}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
    `;

            $('#tablaConceptos tbody').append(nuevaFila);

            // Actualizar total general
            calcularTotalGeneral();

            // Ocultar opción en el select
            // conceptoOption.hide();

            // Actualizar el select de conceptos (sin ocultar opciones)
            actualizarSelectConceptos();

            // Resetear select
            select.val('');
        }

        function calcularConcepto(conceptoId) {
            const fila = $(`#concepto-${conceptoId}`);
            const horas = parseFloat(fila.find('.horas').val()) || 0;
            const costo = parseFloat(fila.find('.costo').val()) || 0;
            // const total = horas * costo;

            if (isNaN(horas) || isNaN(costo)) {
                console.error('Error en cálculo: horas o costo no son números válidos');
                return;
            }

            // fila.find('.total').val(total.toFixed(2));
            const total = horas * costo;
            fila.find('.total').val(total.toFixed(2));

            // Actualizar total general
            calcularTotalGeneral();
        }

        function calcularTotalGeneral() {
            let totalGeneral = 0;

            // Sumar todos los conceptos con sus horas extras
            $('#tablaConceptos tbody tr').each(function() {
                const total = parseFloat($(this).find('.total').val()) || 0;
                // totalGeneral += total;
                if (!isNaN(total)) {
                    totalGeneral += total;
                }
            });

            // Actualizar los displays
            $('#costoEspacioDisplay').text('$' + costoEspacio.toFixed(2));
            $('#totalConceptosDisplay').text('$' + totalConceptos.toFixed(2));

            // Sumar el costo del espacio
            const totalGeneral = costoEspacio + totalConceptos;
            $('#totalGeneral').text('$' + totalGeneral.toFixed(2));

            // Opcional: Guardar el total en un campo hidden para el formulario
            $('#totalGeneralHidden').val(totalGeneral.toFixed(2));
        }

        function eliminarConcepto(conceptoId) {
            // Eliminar fila de la tabla
            $(`#concepto-${conceptoId}`).remove();

            // Quitar de la lista de conceptos agregados
            conceptosAgregados = conceptosAgregados.filter(id => id !== conceptoId);

            // Mostrar nuevamente la opción en el select
            // $(`#id_cxrenta option[value="${conceptoId}"]`).show();

            // Actualizar el select de conceptos
            actualizarSelectConceptos();

            // Actualizar total general
            calcularTotalGeneral();
        }

        // Modificar el submit del formulario para incluir los conceptos
        $('#addRentaForm').submit(function(e) {
            e.preventDefault();

            // Recopilar datos de los conceptos
            const conceptos = [];
            $('#tablaConceptos tbody tr').each(function() {
                const conceptoId = $(this).attr('id').replace('concepto-', '');
                // const horas = $(this).find('.horas').val();
                // const costo = $(this).find('.costo').val();
                // const total = $(this).find('.total').val();

                conceptos.push({
                    id_cxrenta: conceptoId,
                    horas_extra: $(this).find('.horas').val(),
                    costo: $(this).find('.costo').val(),
                    total: $(this).find('.total').val()
                });
            });

            // Agregar los conceptos al formulario
            $('<input>').attr({
                type: 'hidden',
                name: 'conceptos',
                value: JSON.stringify(conceptos)
            }).appendTo('#addRentaForm');

            // Enviar formulario
            this.submit();
        });
    </script> --}}
@endsection
