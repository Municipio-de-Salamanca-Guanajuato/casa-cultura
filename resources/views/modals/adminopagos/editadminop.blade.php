<!-- Modal -->
<div class="modal fade" id="editAdminOp" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Agregar formato</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAdminOpForm" action="{{ route('actAdminOp') }}" method="POST">
                    @csrf
                    <input type="hidden" id="idgeneral_2" name="idgeneral_2" >
                    {{-- <div class="mb-3 row">
                        <div class="col-sm-12">
                            value="DIRECCIÓN DE CULTURA"
                            <input type="text" class="form-control" id="namearea_2" name="namearea_2"
                                style="text-transform: uppercase;" placeholder="Nombre del Área:" 
                                required>
                        </div>
                    </div> --}}
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <textarea type="text" class="form-control" id="funlegal_2" name="funlegal_2" 
                            style="text-transform: uppercase;" rows="3" placeholder="Fundamento Legal:" required></textarea>
                        </div>
                    </div>
                    <button type="submit" id="submitEditAdminOp" class="btn btn-primary" disabled>Actualizar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
