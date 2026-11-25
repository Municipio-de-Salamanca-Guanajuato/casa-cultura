<div class="modal fade" id="addConcepto" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Agregar Concepto</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addConceptoForm" action="{{ route('saveConcepto') }}" method="POST">
                    @csrf
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="concepto" name="concepto"
                                placeholder="Concepto:" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="number" step="0.01" min="0" class="form-control" id="costo"
                                name="costo" placeholder="COSTO:" required>
                        </div>
                    </div>
                    <button type="submit" id="submitAddConcepto" class="btn btn-primary">Guardar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
