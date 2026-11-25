<!-- Modal -->
<div class="modal fade" id="editAlumno" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Editar Alumno</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAlumnoForm" action="{{ route('actAlumno') }}" method="POST">
                    @csrf
                    <input type="hidden" id="idalumno_2" name="idalumno_2" >
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="name_2" name="name_2"
                                style="text-transform: uppercase;" placeholder="Nombre:" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="apellidop_2" name="apellidop_2"
                                style="text-transform: uppercase;" placeholder="Apellido Paterno:" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="apellidom_2" name="apellidom_2"
                                style="text-transform: uppercase;" placeholder="Apellido Materno:" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="curp_2" name="curp_2" placeholder="CURP:" minlength="18" maxlength="21"
                                style="text-transform: uppercase;" required oninput="removerEspacios(this)">
                                <div id="curpErrorMessage" style="color: red; display: none;"></div>
                                <div id="spinner" style="display:none;">Verificando...</div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="direccion_2" name="direccion_2"
                                style="text-transform: uppercase;" placeholder="Dirección:" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="telefono_2" name="telefono_2" placeholder="TELÉFONO:" 
                                oninput="validarTelefono_2(this, 'phoneErrorMessage_3')" minlength="10" maxlength="10">
                            <div id="phoneErrorMessage_3" style="color: red; display: none;"></div>
                        </div>
                    </div>
                    <div id="tutorFieldsEditar" style="display: none;">
                        <div class="mb-3 row">
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="nombretutor_2" name="nombretutor_2"
                                    style="text-transform: uppercase;" placeholder="Nombre del Tutor:">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="telefonotutor_2" name="telefonotutor_2" placeholder="TELÉFONO DEL TUTOR:"
                                oninput="validarTelefono_2(this, 'phoneErrorMessage_4')" minlength="10" maxlength="10">
                                <div id="phoneErrorMessage_4" style="color: red; display: none;"></div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" id="submitBtnEdit" class="btn btn-primary" disabled>Actualizar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
