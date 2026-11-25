<!-- Modal -->
<div class="modal fade" id="editPassword" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Cambiar contraseña</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editPasswordForm" action="{{ route('actUser') }}" method="POST">
                    @csrf
                    {{-- <input type="text" id="id_user" name="id_user"> --}}
                    {{-- <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Contraseña Actual</span>
                            <input type="text" class="form-control" id="passwa" name="passwa"
                                placeholder="Contraseña Actual:" required readonly>
                        </div>
                    </div> --}}
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Nueva Contraseña</span>
                            <input type="password" class="form-control" id="passwnew" name="passwnew" placeholder=""
                                required>
                            <div id="passnewError" style="color: red; display: none;"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Confirmar Contraseña</span>
                            <input type="password" class="form-control" id="passwnewup" name="passwnewup" placeholder=""
                                required>
                            <div id="passnewupError" style="color: red; display: none;"></div>
                        </div>
                    </div>

                    <div id="passwordError" style="color: red; display: none;"></div>

                    <button type="submit" id="submitBtnUp" class="btn btn-primary">Actualizar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
