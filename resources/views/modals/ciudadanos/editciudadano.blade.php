<!-- Modal -->
<div class="modal fade" id="editCiudadano" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Editar Ciudadano</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCiudadanoForm" action="{{ route('actCiudadano') }}" method="POST">
                    @csrf
                    <input type="hidden" id="id_ciudadano" name="id_ciudadano" >
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="name_2" name="name_2"
                                style="text-transform: uppercase;" placeholder="Nombre:" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="apellidop_2" name="apellidop_2"
                                style="text-transform: uppercase;" placeholder="Apellido Paterno:" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="apellidom_2" name="apellidom_2"
                                style="text-transform: uppercase;" placeholder="Apellido Materno:" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="telefono_2" name="telefono_2"
                                placeholder="TELÉFONO:" oninput="validarTelefono(this, 'phoneErrorMessageEdit')" minlength="10" maxlength="10" required>
                            <div id="phoneErrorMessageEdit" style="color: red; display: none;"></div>
                            {{-- <small id="errorTelefono" class="text-danger" style="display: none;">Debe contener 10 dígitos.</small> --}}
                        </div>
                    </div>
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="direccion_2" name="direccion_2"
                                style="text-transform: uppercase;" placeholder="Dirección:" required>
                        </div>
                    </div>
                    
                    <button type="submit" id="submitEditCiudadano" class="btn btn-primary" >Actualizar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>