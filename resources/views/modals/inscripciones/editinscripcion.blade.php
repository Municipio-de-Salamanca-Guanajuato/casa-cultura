<!-- Modal -->
<div class="modal fade" id="editInscripcion" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Editar inscripción del alumno</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editInscripcionForm" action="{{ route('actInscripcion') }}" method="POST">
                    @csrf
                    <input type="hidden" id="idinscripcion_2" name="idinscripcion_2" >
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            <select class="form-select" id="id_alumno_2" name="id_alumno_2" aria-label="Default select example" required>
                                <option value="" selected>Alumnos:</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <select class="form-select" id="id_taller_2" name="id_taller_2" aria-label="Default select example" required>
                                <option value="" selected>Talleres:</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" id="submitEditInscripcion" class="btn btn-primary" >Actualizar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >Cerrar</button>
            </div>
        </div>
    </div>
</div>
