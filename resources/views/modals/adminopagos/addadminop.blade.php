<!-- Modal -->
<div class="modal fade" id="addAdminOp" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Agregar formato</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addAdminOpForm" action="{{ route('saveAdminOp') }}" method="POST">
                    @csrf
                    {{-- <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="namearea" name="namearea"
                                style="text-transform: uppercase;" placeholder="Nombre del Área:" 
                                value="DIRECCIÓN DE CULTURA" required>
                        </div>
                    </div> --}}
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <textarea type="text" class="form-control" id="funlegal" name="funlegal" 
                            style="text-transform: uppercase;" rows="3" placeholder="Fundamento Legal:" required></textarea>
                        </div>
                    </div>
                    <button type="submit" id="submitAddAdminOp" class="btn btn-primary">Guardar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
