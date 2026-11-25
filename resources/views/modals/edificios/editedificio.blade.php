<!-- Modal -->
<div class="modal fade" id="editEdificio" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="editEdificioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editEdificioLabel">Editar Edificio</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editEdificioForm" action="{{ route('actEdificio') }}" method="POST">
                    @csrf
                    <input type="hidden" id="id_edificio" name="id_edificio">
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="nombre_2" name="nombre_2" maxlength="100"
                                style="text-transform: uppercase;" placeholder="Nombre del edificio:" required>
                        </div>
                    </div>
                    <button type="submit" id="submitEditEdificio" class="btn btn-primary" disabled>Guardar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
