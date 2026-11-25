<!-- Modal -->
<div class="modal fade" id="showOpagoMens" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Ordenes de Pago Mensualidades: <span class="fw-bold text-black" id="alumnoMens"></span></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalOrdenesContent">
                    {{-- <form id="addOpagoInscForm" action="{{ route('saveOpagoAlumno') }}" method="POST"> --}}
                    {{-- @csrf --}}
                    {{-- <input type="text" id="idinsc" name="idinsc"> --}}
                    {{-- <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Folio</span>
                            <input type="text" class="form-control" id="foliocc_2" name="foliocc_2"
                                placeholder="Folio:" required readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Folio cajas</span>
                            <input type="text" class="form-control" id="foliocajas_2" name="foliocajas_2"
                                placeholder="Folio cajas:" required readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Alumno</span>
                            <input type="text" class="form-control" id="name_2" name="name_2"
                                placeholder="Nombre:" required readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Taller</span>
                            <input type="text" class="form-control" id="taller_2" name="taller_2"
                                placeholder="Taller:" required readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Concepto</span>
                            <input type="text" class="form-control" id="concepto_2" name="concepto_2"
                                placeholder="Concepto:" required readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Inscripción</span>
                            <input type="text" class="form-control" id="montototal_2" name="montototal_2"
                                placeholder="Importe:" required readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Mes</span>
                            <input type="text" class="form-control" id="mespago_2" name="mespago_2"
                                placeholder="Mes:" required readonly>
                        </div>
                    </div> --}}
                    {{-- <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Fecha límite:</span>
                            <input type="date" class="form-control" id="fechalimite" name="fechalimite"
                                required readonly>
                        </div>
                    </div> --}}
                    {{-- <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">Estatus del pago</span>
                            <input type="text" class="form-control" id="estatuspago_2" name="estatuspago_2"
                                placeholder="Estatus del pago:" required readonly>
                        </div>
                    </div> --}}
                    {{-- <button type="submit" id="submitAddOpInsc" class="btn btn-primary" >Generar</button> --}}
                    {{-- </form> --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
