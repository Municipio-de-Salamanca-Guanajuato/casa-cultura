<!-- Modal -->
<div class="modal fade" id="editMaestro" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="editMaestroLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editMaestroLabel">Editar Maestro</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editMaestroForm" action="{{ route('actMaestro') }}" method="POST">
                    @csrf
                    <input type="hidden" id="id_maestro" name="id_maestro">
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="nombre_comp_2" name="nombre_comp_2"
                                maxlength="150" style="text-transform: uppercase;"
                                placeholder="Nombre completo del maestro:" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="telefono_2" name="telefono_2" inputmode="numeric"
                                placeholder="TELÉFONO (opcional):" maxlength="10">
                            <div id="phoneErrorMessageEdit" style="color: red; display: none;"></div>
                        </div>
                    </div>
                    <button type="submit" id="submitEditMaestro" class="btn btn-primary" disabled>Guardar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
