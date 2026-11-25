<!-- Modal -->
<div class="modal fade" id="addAula" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="addAulaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addAulaLabel">Agregar Aula</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addAulaForm" action="{{ route('saveAula') }}" method="POST">
                    @csrf
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <select class="form-select" id="idedificio" name="idedificio" required>
                                <option value="" selected disabled>Selecciona el edificio:</option>
                                @foreach ($edificios as $edificio)
                                    <option value="{{ $edificio->idedificio }}"
                                        {{ old('idedificio') == $edificio->idedificio ? 'selected' : '' }}>
                                        {{ $edificio->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($edificios->isEmpty())
                                <div class="form-text text-danger">No hay edificios activos. Registra uno primero.</div>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="100"
                                style="text-transform: uppercase;" placeholder="Nombre del aula:" required>
                        </div>
                    </div>
                    <button type="submit" id="submitAddAula" class="btn btn-primary">Guardar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
