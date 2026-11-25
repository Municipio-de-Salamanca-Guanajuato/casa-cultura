<!-- Modal -->
<div class="modal fade" id="addRepoTarjeton" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Solicitud de Reposicion</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addRepoTarjetonForm" action="{{ route('saveTarjeton') }}" method="POST">
                    @csrf
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <select class="form-select" id="id_inscripcion" name="id_inscripcion"
                                aria-label="Default select example" required>
                                <option value="" selected>Inscripciones:</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <label for="costo_actual" class="form-label">Costo actual de reposicion</label>
                            <input type="text" class="form-control" id="costo_actual"
                                value="{{ $conceptoRepoTarjeton ? '$' . number_format($conceptoRepoTarjeton->costo, 2) : 'Concepto no configurado' }}"
                                readonly>
                        </div>
                    </div>
                    <button type="submit" id="submitAddInscripcion" class="btn btn-primary">Guardar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
