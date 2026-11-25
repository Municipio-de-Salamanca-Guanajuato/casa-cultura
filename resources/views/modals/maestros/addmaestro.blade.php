<!-- Modal -->
<div class="modal fade" id="addMaestro" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="addMaestroLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addMaestroLabel">Agregar Maestro</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addMaestroForm" action="{{ route('saveMaestro') }}" method="POST">
                    @csrf
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="nombre_comp" name="nombre_comp" maxlength="150"
                                style="text-transform: uppercase;" placeholder="Nombre completo del maestro:" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="telefono" name="telefono" inputmode="numeric"
                                placeholder="TELÉFONO (opcional):" maxlength="10"
                                oninput="validarTelefono(this, 'phoneErrorMessageAdd')">
                            <div id="phoneErrorMessageAdd" style="color: red; display: none;"></div>
                        </div>
                    </div>
                    <button type="submit" id="submitAddMaestro" class="btn btn-primary">Guardar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
