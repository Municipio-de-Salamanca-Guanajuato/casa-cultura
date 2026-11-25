<!-- Modal -->
<div class="modal fade" id="editEspacio" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Editar Espacio</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editEspacioForm" action="{{ route('actEspacio') }}" method="POST">
                    @csrf
                    <input type="hidden" id="id_espacio" name="id_espacio">
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="nameroom_2" name="nameroom_2"
                                style="text-transform: uppercase;" placeholder="Nombre del espacio:" required>
                            <div id="nametallerErrorAdd" style="color: red; display: none;"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="number" min="1" class="form-control" id="cupo_2" name="cupo_2"
                                placeholder="CUPO:" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="number" step="0.01" min="1" class="form-control" id="costo_2"
                                name="costo_2" placeholder="COSTO:" required>
                        </div>
                    </div>
                    <button type="submit" id="submitEditEspacio" class="btn btn-primary">Guardar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
