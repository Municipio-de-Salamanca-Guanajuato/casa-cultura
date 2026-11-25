<!-- Modal -->
<div class="modal fade" id="editAula" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="editAulaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editAulaLabel">Editar Aula</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAulaForm" action="{{ route('actAula') }}" method="POST">
                    @csrf
                    <input type="hidden" id="id_aula" name="id_aula">
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <select class="form-select" id="idedificio_2" name="idedificio_2" required>
                                <option value="" disabled>Selecciona el edificio:</option>
                                @foreach ($edificios as $edificio)
                                    <option value="{{ $edificio->idedificio }}">{{ $edificio->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="nombre_2" name="nombre_2" maxlength="100"
                                style="text-transform: uppercase;" placeholder="Nombre del aula:" required>
                        </div>
                    </div>
                    <button type="submit" id="submitEditAula" class="btn btn-primary" disabled>Guardar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
