<!-- Modal -->
<div class="modal fade" id="addOpagoInsc" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Generar Orden de Pago de la Inscripción</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addOpagoInscForm" action="{{ route('saveOpagoAlumno') }}" method="POST">
                    @csrf
                    <input type="hidden" id="idinscripcion" name="idinscripcion">
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Nombre:" required readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="taller" name="taller"
                                placeholder="Taller:" required readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            {{-- <input type="text" class="form-control" id="" name=""
                                placeholder="Concepto:" required> --}}
                            <select class="form-select" id="id_concepto" name="id_concepto"
                                aria-label="Default select example" required>
                                <option value="" selected>Concepto:</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="inscripcion" name="inscripcion"
                                placeholder="Importe:" required readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Fecha límite:</span>
                            <input type="date" class="form-control" id="fechalimite" name="fechalimite"
                                required >
                        </div>
                    </div>
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            {{-- <input type="text" class="form-control" id="" name=""
                                placeholder=":"> --}}
                            <select class="form-select" id="id_funlegal" name="id_funlegal"
                                aria-label="Default select example" required>
                                <option value="" selected>Fundamento Legal:</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" id="submitAddOpInsc" class="btn btn-primary" >Generar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
