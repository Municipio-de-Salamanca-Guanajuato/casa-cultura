<!-- Modal -->
<div class="modal fade" id="addRenta" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Agregar Renta</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addRentaForm" action="{{ route('saveRenta') }}" method="POST">
                    @csrf
                    {{-- <div>
                        <input type="number" name="cpt[]" id="con1" value="1">
                        <input type="number" name="cpt[]" id="con2" value="2">
                        <input type="number" name="cpt[]" id="con3" value="3">
                    </div> --}}
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="name" name="name"
                                style="text-transform: uppercase;" placeholder="Nombre Completo:" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="telefono" name="telefono"
                                placeholder="TELÉFONO:" oninput="validarTelefono(this, 'phoneErrorMessageAdd')"
                                minlength="10" maxlength="10" required>
                            <div id="phoneErrorMessageAdd" style="color: red; display: none;"></div>
                        </div>
                    </div>
                    {{-- <div class="mb-3 row">
                        <div class="col-sm-12">
                            <select class="form-select" id="id_ciudadano" name="id_ciudadano"
                                aria-label="Default select example" required>
                                <option value="" selected>Ciudadanos:</option>
                            </select>
                        </div>
                    </div> --}}
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <select class="form-select" id="id_espacio" name="id_espacio"
                                aria-label="Default select example" required>
                                <option value="" selected>Espacios:</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">FECHA DEL EVENTO:</span>
                            <input type="date" class="form-control" id="fechaeve" name="fechaeve"
                                placeholder="Fecha de inicio:" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">HORA INICIO:</span>
                            <input type="time" class="form-control" id="horaini" name="horaini"
                                placeholder="Hora de inicio:" required>
                            {{-- <div id="curpErrorMessageAdd" style="color: red; display: none;"></div> --}}
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="input-group col-sm-12">
                            <span class="input-group-text" id="inputGroup-sizing-default">HORA FIN:</span>
                            <input type="time" class="form-control" id="horafin" name="horafin"
                                placeholder="Hora de fin:" required>
                            {{-- <div id="curpErrorMessageAdd" style="color: red; display: none;"></div> --}}
                        </div>
                    </div>
                    <div class="mb-3 row ">
                        <div class="col-sm-10 mb-2 mb-sm-0">
                            <select class="form-select" id="id_cxrenta" name="id_cxrenta"
                                aria-label="Default select example" >
                                <option value="" selected>Conceptos por Renta:</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-primary btn-sm"
                                id="btnAgregarConcepto">Agregar</button>
                        </div>
                    </div>
                    <!-- Tabla de Conceptos Agregados -->
                    <div class="table-responsive mb-3">
                        <table class="table table-hover" id="tablaConceptos">
                            <thead>
                                <tr>
                                    <th scope="col">Concepto</th>
                                    <th scope="col">Cantidad</th>
                                    <th scope="col">Costo</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Las filas se agregarán dinámicamente aquí -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end fw-bold" id="tituloTotal">Total de Renta:</th>
                                    <th class="text-center fw-bold fs-5" id="montoTotal">$0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        {{-- <table class="table table-bordered" id="tablaConceptos">
                            <thead class="table-light">
                                <tr>
                                    <th>Concepto</th>
                                    <th width="150">Horas Extra</th>
                                    <th width="120">Costo</th>
                                    <th width="120">Total</th>
                                    <th width="80">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los conceptos se agregarán aquí dinámicamente -->
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="3" class="text-end">Costo Espacio:</th>
                                    <th class="text-center" id="costoEspacioDisplay">$0.00</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">Total Conceptos:</th>
                                    <th class="text-center" id="totalConceptosDisplay">$0.00</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end fw-bold">Total General:</th>
                                    <th class="text-center fw-bold fs-5" id="totalGeneral">$0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table> --}}
                    </div>
                    <!-- Campo hidden para el total general -->
                    <input type="hidden" id="totalGeneralHidden" name="total_general" value="0">
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"
                                    onchange="toggleInput()">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Requiere Factura Fiscal
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row" id="rfcContainer" style="display: none;">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="rfc" name="rfc"
                                style="text-transform: uppercase;" placeholder="RFC:" maxlength="13"
                                oninput="validarRFC(this)" autocomplete="off">
                            <div id="rfcErrorAdd" style="color: red; display: none;"></div>
                        </div>
                    </div>
                    <button type="submit" id="submitAddRenta" class="btn btn-primary">Guardar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
