<!-- Modal -->
<div class="modal fade" id="addInscripcion" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Inscripción de Alumno</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addInscripcionForm" action="{{ route('saveInscripcion') }}" method="POST">
                    @csrf
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            <select class="form-select" id="id_alumno" name="id_alumno" aria-label="Default select example" required>
                                <option value="" selected>Alumnos:</option>
                            </select>
                            <div id="infoEdadAlumno" class="form-text"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <select class="form-select" id="id_taller" name="id_taller" aria-label="Default select example" required disabled>
                                <option value="" selected>Primero selecciona un alumno</option>
                            </select>
                            <div id="infoTalleresEdad" class="form-text"></div>
                        </div>
                    </div>
                    <button type="submit" id="submitAddInscripcion" class="btn btn-primary" >Guardar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >Cerrar</button>
            </div>
        </div>
    </div>
</div>
