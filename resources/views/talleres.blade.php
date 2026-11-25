@extends('layouts.carcasa')

@section('title', 'Talleres')
@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4">
                {{-- Errores de validación (ej. edad máxima menor que la mínima) --}}
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
                            <b style="text-transform: uppercase">Registrar Talleres</b>
                            <br>
                            Listado de talleres
                        </p>
                        <div style=" text-align: right;">
                            <button type="button" class="btn btn-md btn-success me-2" data-bs-toggle="modal"
                                data-bs-target="#addTaller" title="Agregar taller">
                                <i class="bi bi-backpack"></i>
                            </button>
                            <button type="button" class="btn btn-md btn-warning me-2" style="color: white;"
                                data-bs-toggle="modal" data-bs-target="#modalExportarHorarios" title="Exportar horarios a Excel">
                                <i class="fa-solid fa-file-arrow-down"></i>
                            </button>
                            <button id="btnPreviewBajasTalleres" type="button" class="btn btn-md btn-danger"
                                title="Dar de baja talleres terminados">
                                <i class="bi bi-calendar-x"></i>
                            </button>
                        </div>
                    </div>
                    {{-- Talleres Activos --}}
                    <div class="card-body">
                        <h2 class="text-center">Talleres activos</h2>
                        <table id="tbltalleresact" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Maestro</th>
                                    <th>Edad</th>
                                    <th>Fecha de Inicio</th>
                                    <th>Fecha de Fin</th>
                                    <th>Cupo</th>
                                    <th>Cupo disponible</th>
                                    <th>Inscripción</th>
                                    <th>Mensualidad</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    {{-- Talleres inactivos --}}
                    <div class="card-body">
                        <h2 class="text-center">Talleres inactivos</h2>
                        <table id="tbltalleresina" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Maestro</th>
                                    <th>Edad</th>
                                    <th>Fecha de Inicio</th>
                                    <th>Fecha de Fin</th>
                                    <th>Cupo</th>
                                    <th>Cupo disponible</th>
                                    <th>Inscripción</th>
                                    <th>Mensualidad</th>
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

    {{-- @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif --}}


    {{-- Modales --}}
    @include('modals.talleres.addtaller')
    @include('modals.talleres.edittaller')
    @include('modals.talleres.bajatalleres')
    @include('modals.talleres.exportarhorarios')
@endsection

@section('styles')
    <style>
        /* Indicador de pasos del modal de talleres */
        .wizard-indicador .nav-link {
            color: var(--bs-secondary-color);
            border: 1px solid var(--bs-border-color);
            margin: 0 .25rem;
        }

        .wizard-indicador .nav-link.completado {
            color: var(--bs-success);
            border-color: var(--bs-success);
        }

        .wizard-indicador .wizard-numero {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.6rem;
            height: 1.6rem;
            border-radius: 50%;
            margin-right: .35rem;
            font-weight: 600;
            background: var(--bs-secondary-bg);
            color: var(--bs-body-color);
        }

        .wizard-indicador .nav-link.active .wizard-numero {
            background: #fff;
            color: var(--bs-primary);
        }

        .wizard-indicador .nav-link.completado .wizard-numero {
            background: var(--bs-success);
            color: #fff;
        }

        /* Select2 sin valor cuando es obligatorio */
        .select2-container .select2-selection.select2-invalido {
            border-color: var(--bs-danger) !important;
        }
    </style>
@endsection

@section('scripts')
    {{-- SCRIPTS MODAL POR PASOS --}}
    <script>
        /**
         * Convierte un modal con .wizard-paso (data-paso = 1..N) en un asistente por pasos.
         * Valida los campos del paso actual antes de avanzar y todos al guardar.
         */
        function crearWizard(modalSelector) {
            const $modal = $(modalSelector);
            const $form = $modal.find('form');
            const total = $modal.find('.wizard-paso').length;
            let actual = 1;

            function mostrar(paso) {
                actual = paso;
                $modal.find('.wizard-paso').each(function() {
                    $(this).toggleClass('d-none', Number($(this).data('paso')) !== paso);
                });
                $modal.find('.wizard-indicador .nav-link').each(function() {
                    const p = Number($(this).data('paso'));
                    $(this).toggleClass('active', p === paso).toggleClass('completado', p < paso);
                });
                $modal.find('.btn-wizard-anterior').toggleClass('invisible', paso === 1);
                $modal.find('.btn-wizard-siguiente').toggleClass('d-none', paso === total);
                $modal.find('.btn-wizard-guardar').toggleClass('d-none', paso !== total);
            }

            function camposDelPaso(paso) {
                return $modal.find('.wizard-paso[data-paso="' + paso + '"]').find('input, select, textarea')
                    .filter(function() {
                        return !this.disabled && this.type !== 'hidden';
                    });
            }

            function pasoValido(paso) {
                return camposDelPaso(paso).toArray().every(el => el.checkValidity());
            }

            // Valida el paso visible y marca el primer error
            function validarPaso(paso) {
                let valido = true;
                camposDelPaso(paso).each(function() {
                    const $el = $(this);
                    const ok = this.checkValidity();

                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.next('.select2-container').find('.select2-selection').toggleClass('select2-invalido', !ok);
                        $el.closest('div').find('.select-error').toggle(!ok);
                        if (!ok && valido) $el.select2('open');
                    } else if (!ok && valido) {
                        this.reportValidity();
                    }

                    if (!ok) valido = false;
                });
                return valido;
            }

            function irA(paso) {
                // Hacia atrás siempre se puede; hacia adelante, solo con los pasos previos completos
                for (let p = actual; p < paso; p++) {
                    if (!validarPaso(p)) return;
                    if (p + 1 < paso) mostrar(p + 1);
                }
                mostrar(paso);
            }

            $modal.find('.btn-wizard-siguiente').on('click', () => irA(actual + 1));
            $modal.find('.btn-wizard-anterior').on('click', () => mostrar(Math.max(1, actual - 1)));
            $modal.find('.wizard-indicador .nav-link').on('click', function() {
                const paso = Number($(this).data('paso'));
                paso < actual ? mostrar(paso) : irA(paso);
            });

            $form.on('submit', function(e) {
                // Enter en un paso intermedio: avanzar en lugar de guardar
                if (actual < total) {
                    e.preventDefault();
                    irA(actual + 1);
                    return;
                }
                // Al guardar, llevar al primer paso con errores
                for (let p = 1; p <= total; p++) {
                    if (!pasoValido(p)) {
                        e.preventDefault();
                        mostrar(p);
                        validarPaso(p);
                        return;
                    }
                }
            });

            // Quitar la marca de error del Select2 al elegir un valor
            $modal.find('select').on('change', function() {
                if (this.checkValidity()) {
                    $(this).next('.select2-container').find('.select2-selection').removeClass('select2-invalido');
                    $(this).closest('div').find('.select-error').hide();
                }
            });

            // Cada vez que se abre, empezar en el paso 1
            $modal.on('show.bs.modal', () => mostrar(1));
            mostrar(1);
        }

        // Edificios activos con sus aulas activas (para el paso de horarios)
        const catalogoEdificios = @json($edificios);
        const nombresDias = @json(\App\Models\TallerHorario::DIAS);

        function escaparHtml(texto) {
            return $('<div>').text(texto ?? '').html();
        }

        // "16:00:00" -> "16:00"
        function horaCorta(hora) {
            return String(hora ?? '').substring(0, 5);
        }

        function aMinutos(hora) {
            const [h, m] = horaCorta(hora).split(':').map(Number);
            return h * 60 + m;
        }

        // Opciones de 30 en 30 minutos entre dos horas (inclusive)
        function opcionesHora(desde, hasta) {
            const lista = [];
            for (let m = aMinutos(desde); m <= aMinutos(hasta); m += 30) {
                lista.push(String(Math.floor(m / 60)).padStart(2, '0') + ':' + String(m % 60).padStart(2, '0'));
            }
            return lista;
        }

        function formatoHoras(minutos) {
            const horas = minutos / 60;
            return (Number.isInteger(horas) ? horas : horas.toFixed(1)) + ' hrs';
        }

        /**
         * Editor de bloques de horario dentro de un modal.
         * Cada bloque: { dias: [1,3,5], idaula, inicio: '16:00', fin: '18:00', aula: 'AULA 1', edificio: 'CENTRO',
         *                diasGuardados: [1,3] }  <- días que ya existen en la BD (al quitarlos pasan a "inactivos")
         *
         * obtenerContexto() debe regresar { idtaller, idmaestro, fechaini, fechafin } para revisar choques.
         */
        function crearEditorHorarios(modalSelector, obtenerContexto) {
            const $modal = $(modalSelector);
            const $form = $modal.find('form');
            const $ed = $modal.find('.horarios-editor');
            const $edificio = $ed.find('.hz-edificio');
            const $aula = $ed.find('.hz-aula');
            const $inicio = $ed.find('.hz-inicio');
            const $fin = $ed.find('.hz-fin');
            const $error = $ed.find('.hz-error');
            const $btnAgregar = $ed.find('.hz-agregar');
            let bloques = [];
            let inactivos = [];

            const claveBloque = b => b.idaula + '|' + b.inicio + '|' + b.fin;

            // Agrega un bloque a una lista; si ya hay uno con la misma aula y horas, junta los días
            function juntarEn(lista, bloque) {
                const existente = lista.find(b => claveBloque(b) === claveBloque(bloque));
                if (existente) {
                    existente.dias = [...new Set([...existente.dias, ...bloque.dias])].sort((x, y) => x - y);
                    existente.diasGuardados = [...new Set([...(existente.diasGuardados || []), ...(bloque.diasGuardados || [])])];
                } else {
                    lista.push(bloque);
                }
            }

            // Revisa en el servidor si el bloque choca con otros talleres (aula o maestro)
            // y, al editar, con otros talleres de los alumnos ya inscritos
            function revisarChoques(bloque) {
                const ctx = obtenerContexto();
                return $.ajax({
                    url: '/ajaxConsulta/horarios/choques',
                    type: 'GET',
                    data: {
                        dias: bloque.dias,
                        idaula: bloque.idaula,
                        hora_inicio: bloque.inicio,
                        hora_fin: bloque.fin,
                        fechaini: ctx.fechaini,
                        fechafin: ctx.fechafin,
                        idmaestro: ctx.idmaestro || '',
                        idtaller: ctx.idtaller || ''
                    }
                }).then(r => r.choques || []);
            }

            function mostrarChoques(choques) {
                $error.html('<i class="bi bi-exclamation-triangle"></i> ' + choques.map(escaparHtml).join('<br>')).show();
            }

            function cargando(si) {
                $btnAgregar.prop('disabled', si);
                $btnAgregar.find('.hz-agregar-texto').toggleClass('d-none', si);
                $btnAgregar.find('.hz-agregar-cargando').toggleClass('d-none', !si);
            }

            // Horas: inicio de 09:00 a 20:30, fin de 09:30 a 21:00
            opcionesHora('09:00', '20:30').forEach(h => $inicio.append(new Option(h, h)));
            opcionesHora('09:30', '21:00').forEach(h => $fin.append(new Option(h, h)));

            function ajustarFin() {
                const inicio = aMinutos($inicio.val());
                $fin.find('option').each(function() {
                    this.disabled = aMinutos(this.value) <= inicio;
                });
                if (aMinutos($fin.val()) <= inicio) {
                    // Por defecto, una hora después (o lo más cercano posible)
                    const sugerida = $fin.find('option:not(:disabled)').eq(1).val() ?? $fin.find('option:not(:disabled)').first().val();
                    $fin.val(sugerida);
                }
            }
            $inicio.on('change', ajustarFin);

            $edificio.on('change', function() {
                const edificio = catalogoEdificios.find(e => String(e.idedificio) === this.value);
                $aula.empty();
                if (!edificio) {
                    $aula.append(new Option('Primero el edificio', '')).prop('disabled', true);
                    return;
                }
                $aula.append(new Option(edificio.aulas.length ? 'Aula:' : 'Sin aulas activas', ''));
                edificio.aulas.forEach(a => $aula.append(new Option(a.nombre, a.idaula)));
                $aula.prop('disabled', edificio.aulas.length === 0);
            });

            function mostrarError(mensaje) {
                $error.text(mensaje).toggle(!!mensaje);
            }

            function limpiarFormulario() {
                $ed.find('.hz-dia').prop('checked', false);
                mostrarError('');
            }

            function diasSeleccionados() {
                return $ed.find('.hz-dia:checked').map((i, el) => Number(el.value)).get();
            }

            // ¿Este bloque se encima con otro del mismo taller en algún día?
            function buscarEncimado(nuevo) {
                for (const b of bloques) {
                    const diasComunes = b.dias.filter(d => nuevo.dias.includes(d));
                    if (diasComunes.length && aMinutos(nuevo.inicio) < aMinutos(b.fin) && aMinutos(b.inicio) < aMinutos(nuevo.fin)) {
                        return 'Se encima con el horario del ' + diasComunes.map(d => nombresDias[d]).join(', ') +
                            ' de ' + b.inicio + ' a ' + b.fin + '.';
                    }
                }
                return null;
            }

            $ed.find('.hz-agregar').on('click', function() {
                const dias = diasSeleccionados();
                const edificio = catalogoEdificios.find(e => String(e.idedificio) === $edificio.val());
                const aula = edificio ? edificio.aulas.find(a => String(a.idaula) === $aula.val()) : null;

                if (!dias.length) return mostrarError('Selecciona al menos un día.');
                if (!aula) return mostrarError('Selecciona el edificio y el aula.');
                if (aMinutos($fin.val()) <= aMinutos($inicio.val())) return mostrarError('La hora de fin debe ser mayor que la de inicio.');

                const nuevo = {
                    dias: dias,
                    idaula: aula.idaula,
                    inicio: $inicio.val(),
                    fin: $fin.val(),
                    aula: aula.nombre,
                    edificio: edificio.nombre,
                    diasGuardados: []
                };

                const encimado = buscarEncimado(nuevo);
                if (encimado) return mostrarError(encimado);

                // Revisar choques con otros talleres antes de agregarlo
                mostrarError('');
                cargando(true);
                revisarChoques(nuevo)
                    .then(function(choques) {
                        if (choques.length) return mostrarChoques(choques);
                        juntarEn(bloques, nuevo);
                        limpiarFormulario();
                        render();
                    })
                    .fail(() => mostrarError('No se pudo revisar si el horario choca con otros talleres. Intenta de nuevo.'))
                    .always(() => cargando(false));
            });

            // Quitar: los días que ya estaban guardados pasan a "Horarios inactivos"
            $ed.on('click', '.hz-quitar', function() {
                const [quitado] = bloques.splice(Number($(this).data('index')), 1);
                const guardados = quitado.dias.filter(d => (quitado.diasGuardados || []).includes(d));
                if (guardados.length && $ed.find('.hz-inactivos').length) {
                    juntarEn(inactivos, { ...quitado, dias: guardados, diasGuardados: guardados });
                }
                render();
            });

            // Los horarios inactivos ya no se reactivan: solo se muestran como consulta (historial)

            function tarjetaBloque(b, i, inactivo) {
                const minSemana = (aMinutos(b.fin) - aMinutos(b.inicio)) * b.dias.length;
                const dias = b.dias.slice().sort((x, y) => x - y).map(d => nombresDias[d]).join(', ');
                const boton = inactivo ?
                    `<span class="badge text-bg-secondary">Inactivo</span>` :
                    `<button type="button" class="btn btn-sm btn-outline-danger hz-quitar" data-index="${i}" title="Quitar horario">
                        <i class="bi bi-trash"></i>
                    </button>`;

                return `
                    <div class="d-flex align-items-center gap-3 p-2 px-3 rounded-3 border ${inactivo ? 'bg-body opacity-75' : 'bg-body-tertiary'}">
                        <div class="rounded-3 ${inactivo ? 'bg-secondary-subtle text-secondary' : 'bg-primary-subtle text-primary'} d-flex align-items-center justify-content-center flex-shrink-0" style="width:2.5rem;height:2.5rem;">
                            <i class="bi ${inactivo ? 'bi-calendar-x' : 'bi-calendar-week'}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold ${inactivo ? 'text-body-secondary' : ''}">${escaparHtml(dias)}</div>
                            <div class="small text-body-secondary">
                                <i class="bi bi-clock"></i> ${b.inicio} - ${b.fin} hrs
                                &nbsp;•&nbsp; <i class="bi bi-geo-alt"></i> ${escaparHtml(b.edificio)} · ${escaparHtml(b.aula)}
                            </div>
                        </div>
                        <span class="badge ${inactivo ? 'bg-secondary-subtle text-secondary-emphasis' : 'bg-primary-subtle text-primary-emphasis'}">${formatoHoras(minSemana)}/sem</span>
                        ${boton}
                    </div>`;
            }

            function render() {
                const $lista = $ed.find('.hz-lista').empty();
                const $inputs = $ed.find('.hz-inputs').empty();
                let totalMin = 0;

                bloques.forEach(function(b, i) {
                    totalMin += (aMinutos(b.fin) - aMinutos(b.inicio)) * b.dias.length;
                    $lista.append(tarjetaBloque(b, i, false));

                    // Inputs ocultos que se envían con el formulario
                    b.dias.forEach(d => $inputs.append($('<input>', { type: 'hidden', name: `horarios[${i}][dias][]`, value: d })));
                    $inputs.append($('<input>', { type: 'hidden', name: `horarios[${i}][idaula]`, value: b.idaula }));
                    $inputs.append($('<input>', { type: 'hidden', name: `horarios[${i}][hora_inicio]`, value: b.inicio }));
                    $inputs.append($('<input>', { type: 'hidden', name: `horarios[${i}][hora_fin]`, value: b.fin }));
                });

                $ed.find('.hz-conteo').text(bloques.length);
                $ed.find('.hz-total').text(bloques.length ? formatoHoras(totalMin) + ' semanales totales' : '');
                $ed.find('.hz-vacio').toggle(bloques.length === 0);

                // Horarios inactivos (solo en el modal de editar)
                const $inactivos = $ed.find('.hz-inactivos').empty();
                inactivos.forEach((b, i) => $inactivos.append(tarjetaBloque(b, i, true)));
                $ed.find('.hz-inactivos-conteo').text(inactivos.length);
                $ed.find('.hz-inactivos-seccion').toggle(inactivos.length > 0);

                // Avisar al formulario que cambió (para habilitar "Actualizar" al editar)
                $form.trigger('change');
            }

            // Si dejaron días marcados sin dar "Agregar", preguntar antes de guardar
            $form.on('submit', function(e) {
                if (e.isDefaultPrevented()) return;
                if (diasSeleccionados().length &&
                    !confirm('Marcaste días en Horarios pero no diste clic en "Agregar". ¿Guardar sin ese horario?')) {
                    e.preventDefault();
                }
            });

            // Valores iniciales del formulario de bloque
            $inicio.val('16:00');
            ajustarFin();
            render();

            return {
                // Carga los horarios guardados (una fila por día), separando activos e inactivos en bloques
                cargar: function(horarios) {
                    bloques = [];
                    inactivos = [];
                    (horarios || []).forEach(function(h) {
                        const dia = Number(h.dia);
                        const bloque = {
                            dias: [dia],
                            idaula: h.idaula,
                            inicio: horaCorta(h.hora_inicio),
                            fin: horaCorta(h.hora_fin),
                            aula: h.aula ? h.aula.nombre : 'Aula #' + h.idaula,
                            edificio: h.aula && h.aula.edificio ? h.aula.edificio.nombre : '',
                            diasGuardados: [dia]
                        };
                        juntarEn(String(h.estatus) === '1' ? bloques : inactivos, bloque);
                    });
                    limpiarFormulario();
                    render();
                }
            };
        }

        let editorHorariosAdd, editorHorariosEdit;
        // Maestro del catálogo que tiene el taller que se está editando (null en talleres anteriores)
        let maestroActualEdit = null;

        $(document).ready(function() {
            $('#idmaestro').select2({
                dropdownParent: $('#addTaller'),
                width: '100%',
                placeholder: 'Selecciona el maestro:'
            });
            $('#idmaestro_2').select2({
                dropdownParent: $('#editTaller'),
                width: '100%',
                placeholder: 'Selecciona el maestro:',
                allowClear: true
            });

            crearWizard('#addTaller');
            crearWizard('#editTaller');

            // Después del wizard, para que su validación al guardar corra primero
            editorHorariosAdd = crearEditorHorarios('#addTaller', () => ({
                idtaller: null,
                idmaestro: $('#idmaestro').val(),
                fechaini: $('#fechaini').val(),
                fechafin: $('#fechafin').val()
            }));
            editorHorariosEdit = crearEditorHorarios('#editTaller', () => ({
                idtaller: $('#idtaller_2').val(),
                // Si no eligen maestro (taller anterior), se usa el que ya tenía ligado
                idmaestro: $('#idmaestro_2').val() || maestroActualEdit,
                fechaini: $('#fechaini_2').val(),
                fechafin: $('#fechafin_2').val()
            }));

            // La fecha de cierre no puede ser antes de la de inicio
            $('#fechaini').on('change', function() {
                $('#fechafin').attr('min', this.value);
            });
        });
    </script>
    {{-- SCRIPTS RANGO DE EDAD --}}
    <script>
        // Texto de la columna "Edad" de las tablas
        function formatoEdad(min, max) {
            const hayMin = min !== null && min !== undefined && min !== '';
            const hayMax = max !== null && max !== undefined && max !== '';

            if (hayMin && hayMax) return min + ' a ' + max + ' años';
            if (hayMin) return 'Desde ' + min + ' años';
            if (hayMax) return 'Hasta ' + max + ' años';
            return 'Sin límite';
        }

        // La edad máxima no puede ser menor que la mínima (si se capturan las dos)
        function validarRangoEdad(idMin, idMax, idError) {
            const inputMin = document.getElementById(idMin);
            const inputMax = document.getElementById(idMax);
            const errorDiv = document.getElementById(idError);

            const invalido = inputMin.value !== '' && inputMax.value !== '' &&
                parseInt(inputMax.value, 10) < parseInt(inputMin.value, 10);
            const mensaje = invalido ? 'La edad máxima no puede ser menor que la edad mínima.' : '';

            inputMax.setCustomValidity(mensaje);
            errorDiv.innerText = mensaje;
            errorDiv.style.display = invalido ? 'block' : 'none';
        }
    </script>
    {{-- SCRIPTS EVITAR TALLERES DUPLICADOS --}}
    <script>
        $(document).ready(function() {
            // Validación al agregar un taller
            $('#nametaller').on('blur', function() {
                var nombre = $(this).val().trim();
                var errorDiv = $('#nametallerErrorAdd');

                errorDiv.hide().text(''); // Ocultar mensaje de error al iniciar

                if (nombre === '') return; // No hacer nada si el campo está vacío

                $.ajax({
                    url: '/ajaxConsulta/verificartaller',
                    method: 'GET',
                    data: {
                        nametaller: nombre
                    },
                    success: function(response) {
                        console.log(response); // Para depuración
                        if (response.existe) {
                            errorDiv.text('El taller ya está registrado.').show();
                            $('#nametaller').val(''); // Limpiar el input si ya existe
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en la consulta:', error);
                    }
                });
            });

            // Validación al editar un taller
            $('#nametaller_2').on('blur', function() {
                var nombre = $(this).val().trim();
                var idtaller = $('#idtaller_2').val();
                var errorDiv = $('#nametallerErrorEdit');

                errorDiv.hide().text(''); // Ocultar mensaje de error al iniciar

                if (nombre === '' || !idtaller) return; // Evitar consultas innecesarias

                $.ajax({
                    url: '/ajaxConsulta/verificartaller/' + idtaller,
                    method: 'GET',
                    data: {
                        nametaller: nombre
                    },
                    success: function(response) {
                        console.log(response); // Para depuración
                        if (response.existe) {
                            errorDiv.text('El taller ya existe.').show();
                            $('#nametaller_2').val(''); // Limpiar el input si ya existe
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en la consulta:', error);
                    }
                });
            });
        });
    </script>
    {{-- TABLA DE TALLERES ACTIVOS --}}
    <script>
        // $(document).ready(function() {
        var table = $('#tbltalleresact').DataTable({
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
                url: '/ajaxConsulta/Talleres/act',
                dataSrc: 'data'
            },
            columns: [
                {
                    data: 'nametaller'
                },
                {
                    data: 'maestro'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return formatoEdad(row.edad_min, row.edad_max);
                    }
                },
                {
                    data: 'fechainicio'
                },
                {
                    data: 'fechafin'
                },
                {
                    data: 'cupo'
                },
                {
                    data: 'disponibles'
                },
                {
                    data: 'inscripcion',
                    render: function(data, type, row) {
                        return '$' + parseFloat(data).toFixed(
                            2); // Esto muestra el precio con dos decimales
                    }
                },
                {
                    data: 'mensualidad',
                    render: function(data, type, row) {
                        return '$' + parseFloat(data).toFixed(2); // Muestra el valor con dos decimales
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
                targets: 10,
                orderable: false,
                render: function(data, type, row) {
                    return '<button onclick="buscarTaller(' + data.idtaller +
                        ');" type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editTaller"><i class="bi bi-pencil-square"></i></button>' +
                        '<a href="/upTaller/' + row.idtaller +
                        '/baja" class="btn btn-danger me-2" onclick="return confirm(\'¿Estás seguro de dar de baja el taller: ' +
                        row.nametaller + '?\')" ><i class="bi bi-chevron-bar-down"></i></a>' +
                        '<a href="/exportar-pase-lista/' + row.idtaller + '/' + encodeURIComponent(row.nametaller) +
                        '" class="btn btn-success btn-pase-lista" title="Pase de Lista" data-inscritos="' + row.inscritos +
                        '" data-taller="' + escaparHtml(row.nametaller).replace(/"/g, '&quot;') + '"><i class="fa-solid fa-table"></i></a>';
                    // <a class = 'btn btn-success btn-sm ms-2'       onclick="buscarAlumno(' + data.idalumno +');"
                    // href = '{{ url('/upFactor/ + row.id_factor + /baja') }}'
                    // onclick = "return confirm('¿Estás seguro de dar de alta el factor?')" >
                    //     Alta </a>
                }
            }, ],
        });
        // });

        function buscarTaller(idtaller) {
            //alert('Holis: ' + id);
            // Hacer solicitud AJAX para obtener los datos personales
            $.ajax({
                url: "/ajaxConsulta/edit/" + idtaller + "/taller",
                // url: /datospersonales/${idDato}/edit,
                type: 'GET',
                success: function(data) {
                    // console.log(data);
                    document.getElementById("idtaller_2").value = data.idtaller;
                    document.getElementById("nametaller_2").value = data.nametaller;
                    cargarMaestroEdit(data);
                    document.getElementById("fechaini_2").value = data.fechainicio;
                    document.getElementById("fechafin_2").value = data.fechafin;
                    document.getElementById("cupo_2").value = data.cupo;
                    document.getElementById("inscripcion_2").value = data.inscripcion;
                    document.getElementById("mensualidad_2").value = data.mensualidad;
                    document.getElementById("edad_min_2").value = data.edad_min ?? '';
                    document.getElementById("edad_max_2").value = data.edad_max ?? '';
                    validarRangoEdad('edad_min_2', 'edad_max_2', 'edadErrorEdit');
                    editorHorariosEdit.cargar(data.horarios);

                    // Configurar validaciones para detectar cambios
                    configurarValidacionesTaller();
                    // Mostrar el modal
                    $('#editTaller').modal('show');
                },
                error: function() {
                    alert('Error al cargar los datos del taller.');
                }
            });
        }

        // Habilita "Actualizar" solo si cambió algún campo (inputs o selects)
        function configurarValidacionesTaller() {
            const $form = $('#editTallerForm');
            const submitBtn = document.getElementById('submitEditTaller');
            const valoresIniciales = $form.serialize();

            submitBtn.disabled = true;
            // Se usa jQuery porque Select2 avisa sus cambios con eventos de jQuery
            $form.off('.cambios').on('input.cambios change.cambios', function() {
                submitBtn.disabled = $form.serialize() === valoresIniciales;
            });
        }

        // Llena el select de maestro del modal de editar
        function cargarMaestroEdit(data) {
            const $select = $('#idmaestro_2');
            const $aviso = $('#maestroAnteriorEdit');
            const catalogo = data.maestro_catalogo;
            maestroActualEdit = data.idmaestro;

            // Quitar la opción extra de un maestro inactivo de una edición anterior
            $select.find('option[data-inactivo]').remove();
            $aviso.hide().text('');

            if (catalogo) {
                // Si su maestro ya está inactivo, se agrega solo para este taller
                if ($select.find('option[value="' + catalogo.idmaestro + '"]').length === 0) {
                    $select.append($('<option>', {
                        value: catalogo.idmaestro,
                        text: catalogo.nombre_comp + ' (inactivo)',
                        'data-inactivo': 1
                    }));
                }
                $select.prop('required', true).val(String(catalogo.idmaestro));
            } else {
                // Taller anterior: solo tiene el maestro como texto
                $select.prop('required', false).val('');
                if (data.maestro) {
                    $aviso.text('Maestro anterior (sin catálogo): ' + data.maestro +
                        '. Elige el maestro del catálogo o déjalo vacío para conservar el anterior.').show();
                }
            }

            $select.trigger('change.select2');
            $select.closest('div').find('.select-error').hide();
            $select.next('.select2-container').find('.select2-selection').removeClass('select2-invalido');
        }
    </script>
    <script>
        // $(document).ready(function() {
            
        // });
    </script>

    {{-- TABLA DE TALLERES INACTIVOS --}}
    <script>
        // $(document).ready(function() {
        var table = $('#tbltalleresina').DataTable({
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
                url: '/ajaxConsulta/Talleres/ina',
                dataSrc: 'data'
            },
            columns: [
                {
                    data: 'nametaller'
                },
                {
                    data: 'maestro'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return formatoEdad(row.edad_min, row.edad_max);
                    }
                },
                {
                    data: 'fechainicio'
                },
                {
                    data: 'fechafin'
                },
                {
                    data: 'cupo'
                },
                {
                    data: 'disponibles'
                },
                {
                    data: 'inscripcion',
                    render: function(data, type, row) {
                        return '$' + parseFloat(data).toFixed(
                            2); // Esto muestra el precio con dos decimales
                    }
                },
                {
                    data: 'mensualidad',
                    render: function(data, type, row) {
                        return '$' + parseFloat(data).toFixed(2); // Muestra el valor con dos decimales
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
                targets: 10,
                orderable: false,
                render: function(data, type, row) {
                    return '<a href="/upTaller/' + row.idtaller +
                        '/alta" class="btn btn-success me-2" onclick="return confirm(\'¿Estás seguro de dar de alta el taller: ' +
                        row.nametaller + '?\')" ><i class="bi bi-chevron-bar-up"></i></a>';
                    // <a class = 'btn btn-success btn-sm ms-2'       onclick="buscarAlumno(' + data.idalumno +');"
                    // href = '{{ url('/upFactor/ + row.id_factor + /baja') }}'
                    // onclick = "return confirm('¿Estás seguro de dar de alta el factor?')" >
                    //     Alta </a>
                }
            }, ],
        });
        // });
    </script>
    {{-- EXPORTAR HORARIOS --}}
    <script>
        $(function() {
            const talleresHorario = @json($talleresHorario);
            const $periodo = $('#periodoHorarios');
            const $taller = $('#tallerHorarios');

            // Talleres del periodo elegido: los que se cruzan con él (si tienen otras fechas, se muestran)
            function llenarTalleres() {
                const [ini, fin] = ($periodo.val() || '|').split('|');
                $taller.empty().append(new Option('Todos los talleres', ''));
                talleresHorario
                    .filter(t => t.fechainicio <= fin && t.fechafin >= ini)
                    .forEach(t => {
                        const otrasFechas = (t.fechainicio !== ini || t.fechafin !== fin)
                            ? ` (${t.fechainicio.split('-').reverse().join('/')} al ${t.fechafin.split('-').reverse().join('/')})`
                            : '';
                        $taller.append(new Option(t.nametaller + otrasFechas, t.idtaller));
                    });
            }

            $periodo.on('change', llenarTalleres);
            $('#modalExportarHorarios').on('show.bs.modal', function() {
                $periodo.prop('selectedIndex', 0);
                llenarTalleres();
            });

            // Cerrar el modal al iniciar la descarga
            $('#formExportarHorarios').on('submit', function() {
                setTimeout(() => $('#modalExportarHorarios').modal('hide'), 300);
            });
        });
    </script>
    {{-- BAJA MASIVA DE TALLERES TERMINADOS --}}
    <script>
        $(function() {
            const $preview = $('#modalPreviewBajasTalleres');
            const $confirmar = $('#modalConfirmarBajasTalleres');
            const $btnBaja = $('#btnBajasTalleres');
            const $btnConfirmar = $('#btnConfirmarBajasTalleres');
            let candidatos = [];

            // Abrir la previsualización
            $('#btnPreviewBajasTalleres').on('click', function() {
                const $btn = $(this).prop('disabled', true);
                $.getJSON('{{ route('previewBajasTalleres') }}')
                    .done(function(res) {
                        candidatos = res.candidatos || [];
                        const bloqueados = res.bloqueados || [];

                        const $tbody = $('#bajasTalleresLista tbody').empty();
                        if (candidatos.length) {
                            candidatos.forEach(function(t, i) {
                                $tbody.append(`<tr>
                                    <td>${i + 1}</td>
                                    <td>${escaparHtml(t.nametaller)}</td>
                                    <td>${escaparHtml(t.maestro)}</td>
                                    <td>${t.fechainicio} - ${t.fechafin}</td>
                                </tr>`);
                            });
                        } else {
                            $tbody.append('<tr><td colspan="4" class="text-body-secondary">No hay talleres terminados para dar de baja.</td></tr>');
                        }
                        $('#bajasTalleresCount').text(candidatos.length);

                        $('#bajasTalleresBloqueadosTexto').text(
                            bloqueados.map(t => `${t.nametaller} (${t.inscritos} inscrito${t.inscritos == 1 ? '' : 's'})`).join(', ') + '.'
                        );
                        $('#bajasTalleresBloqueadosSeccion').toggle(bloqueados.length > 0);

                        $btnBaja.prop('disabled', candidatos.length === 0);
                        $preview.modal('show');
                    })
                    .fail(() => alert('Error al obtener la previsualización.'))
                    .always(() => $btn.prop('disabled', false));
            });

            // "Dar de baja": abrir la confirmación encima del modal
            $btnBaja.on('click', function() {
                const nombres = candidatos.map(t => t.nametaller).join(', ');
                $('#confirmarBajasTalleresTexto').text(
                    `¿Estás seguro de dar de baja ${candidatos.length === 1 ? 'el taller terminado' : 'los ' + candidatos.length + ' talleres terminados'}: ${nombres}?`
                );
                $confirmar.modal('show');
            });

            // El fondo oscuro de la confirmación debe quedar encima del primer modal
            $confirmar.on('shown.bs.modal', function() {
                $('.modal-backdrop').last().css('z-index', 1065);
            });

            // Confirmar: ejecutar la baja y recargar para ver el mensaje y las tablas actualizadas
            $btnConfirmar.on('click', function() {
                $btnConfirmar.prop('disabled', true);
                $btnConfirmar.find('.confirmar-texto').addClass('d-none');
                $btnConfirmar.find('.confirmar-cargando').removeClass('d-none');

                $.ajax({
                        url: '{{ route('bajaMasivaTalleres') }}',
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        dataType: 'json'
                    })
                    .done(() => window.location.reload())
                    .fail(function(xhr) {
                        alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error al dar de baja los talleres.');
                        $btnConfirmar.prop('disabled', false);
                        $btnConfirmar.find('.confirmar-texto').removeClass('d-none');
                        $btnConfirmar.find('.confirmar-cargando').addClass('d-none');
                    });
            });
        });
    </script>

    {{-- PASE DE LISTA: avisar antes de descargar si el taller no tiene alumnos inscritos --}}
    <script>
        $(document).on('click', '.btn-pase-lista', function(e) {
            if (Number($(this).data('inscritos')) === 0 &&
                !confirm('¿Estás seguro de descargar el pase de lista? El taller ' + $(this).data('taller') +
                    ' no tiene inscripciones activas, el Excel saldrá vacío.')) {
                e.preventDefault();
            }
        });
    </script>
@endsection
