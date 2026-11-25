<!-- Modal -->
<div class="modal fade" id="editTaller" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="editTallerLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editTallerLabel">Editar Taller</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Indicador de pasos --}}
                <ul class="nav nav-pills nav-fill wizard-indicador mb-4">
                    <li class="nav-item">
                        <button type="button" class="nav-link" data-paso="1">
                            <span class="wizard-numero">1</span> Datos generales
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" data-paso="2">
                            <span class="wizard-numero">2</span> Cupo, costos y edad
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" data-paso="3">
                            <span class="wizard-numero">3</span> Horarios
                        </button>
                    </li>
                </ul>

                <form id="editTallerForm" action="{{ route('actTaller') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" id="idtaller_2" name="idtaller_2">

                    {{-- PASO 1: Datos generales --}}
                    <div class="wizard-paso" data-paso="1">
                        <div class="mb-3 row">
                            <div class="col-sm-12">
                                <label for="nametaller_2" class="form-label">Nombre del taller</label>
                                <input type="text" class="form-control" id="nametaller_2" name="nametaller_2"
                                    style="text-transform: uppercase;" placeholder="Nombre del taller:" maxlength="200" required>
                                <div id="nametallerErrorEdit" style="color: red; display: none;"></div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-sm-12">
                                <label for="idmaestro_2" class="form-label">Maestro</label>
                                <select class="form-select" id="idmaestro_2" name="idmaestro_2">
                                    <option value="">Selecciona el maestro:</option>
                                    @foreach ($maestros as $maestro)
                                        <option value="{{ $maestro->idmaestro }}">{{ $maestro->nombre_comp }}</option>
                                    @endforeach
                                </select>
                                <div class="select-error" style="color: red; display: none;">Selecciona el maestro del taller.</div>
                                {{-- Talleres anteriores: maestro capturado como texto, sin catálogo --}}
                                <div id="maestroAnteriorEdit" class="form-text text-warning-emphasis" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-text">FECHA DE INICIO:</span>
                                    <input type="date" class="form-control" id="fechaini_2" name="fechaini_2" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-text">FECHA DE CIERRE:</span>
                                    <input type="date" class="form-control" id="fechafin_2" name="fechafin_2" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PASO 2: Cupo, costos y edad --}}
                    <div class="wizard-paso d-none" data-paso="2">
                        <div class="mb-3 row">
                            <div class="col-sm-4">
                                <label for="cupo_2" class="form-label">Cupo</label>
                                <input type="number" min="1" max="65535" class="form-control" id="cupo_2" name="cupo_2"
                                    placeholder="CUPO:" required>
                            </div>
                            <div class="col-sm-4">
                                <label for="inscripcion_2" class="form-label">Inscripción</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="inscripcion_2"
                                        name="inscripcion_2" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="mensualidad_2" class="form-label">Mensualidad</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="mensualidad_2"
                                        name="mensualidad_2" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-text">EDAD MÍNIMA:</span>
                                    <input type="number" min="0" max="99" step="1" class="form-control" id="edad_min_2"
                                        name="edad_min_2" placeholder="Sin límite" oninput="validarRangoEdad('edad_min_2', 'edad_max_2', 'edadErrorEdit')">
                                    <span class="input-group-text">años</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-text">EDAD MÁXIMA:</span>
                                    <input type="number" min="0" max="99" step="1" class="form-control" id="edad_max_2"
                                        name="edad_max_2" placeholder="Sin límite" oninput="validarRangoEdad('edad_min_2', 'edad_max_2', 'edadErrorEdit')">
                                    <span class="input-group-text">años</span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-text">Déjalas vacías si el taller no tiene límite de edad. Las edades indicadas sí pueden inscribirse.</div>
                                <div id="edadErrorEdit" style="color: red; display: none;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- PASO 3: Horarios --}}
                    <div class="wizard-paso d-none" data-paso="3">
                        @include('modals.talleres.horarios', ['pref' => 'edit'])
                    </div>

                    {{-- Botones de navegación --}}
                    <div class="d-flex justify-content-between border-top pt-3">
                        <button type="button" class="btn btn-outline-secondary btn-wizard-anterior">
                            <i class="bi bi-arrow-left"></i> Anterior
                        </button>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-primary btn-wizard-siguiente">
                                Siguiente <i class="bi bi-arrow-right"></i>
                            </button>
                            <button type="submit" id="submitEditTaller" class="btn btn-success btn-wizard-guardar" disabled>
                                <i class="bi bi-check-lg"></i> Actualizar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
