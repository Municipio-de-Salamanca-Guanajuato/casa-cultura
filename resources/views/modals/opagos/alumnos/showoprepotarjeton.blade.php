<!-- Modal -->
<div class="modal fade" id="showOpagoRepoTarjeton" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Orden de Pago Reposición de Tarjetón</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- <form id="addOpagoInscForm" action="{{ route('saveOpagoAlumno') }}" method="POST"> --}}
                    {{-- @csrf --}}
                    <input type="hidden" id="idopago" name="idopago">
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Folio de Casa de la Cultura</span>
                            <input type="text" class="form-control" id="foliocc" name="foliocc"
                                placeholder="Folio:" required readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Folio cajas</span>
                            <input type="text" class="form-control" id="foliocajas" name="foliocajas"
                                placeholder="Folio cajas:" required readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Alumno</span>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Nombre:" required readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Taller</span>
                            <input type="text" class="form-control" id="taller" name="taller"
                                placeholder="Taller:" required readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Concepto</span>
                            <input type="text" class="form-control" id="concepto" name="concepto"
                                placeholder="Concepto:" required readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default"> Monto Inscripción</span>
                            <input type="text" class="form-control" id="montototal" name="montototal"
                                placeholder="Importe:" required readonly>
                        </div>
                    </div>
                    {{-- <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Fecha límite:</span>
                            <input type="date" class="form-control" id="fechalimite" name="fechalimite"
                                required readonly>
                        </div>
                    </div> --}}
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Estatus del pago</span>
                            <input type="text" class="form-control" id="estatuspago" name="estatuspago"
                                placeholder="Estatus del pago:" required readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Fecha del pago</span>
                            <input type="text" class="form-control" id="fechapago" name="fechapago"
                                placeholder="Fecha del pago:" required readonly>
                        </div>
                    </div>
                    {{-- <button type="submit" id="submitAddOpInsc" class="btn btn-primary" >Generar</button> --}}
                {{-- </form> --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
 