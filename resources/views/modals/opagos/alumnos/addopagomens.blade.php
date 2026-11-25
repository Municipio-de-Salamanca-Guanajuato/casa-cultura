
{{-- -------------------------------- --}}
<div class="modal fade" id="addOpagoMens" tabindex="-1" aria-labelledby="modalMensualidadesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formMensualidades" action="/ruta-para-guardar-mensualidades" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMensualidadesLabel">Generar Órdenes de Pago para Mensualidades</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Información fija de la inscripción -->
                    <input type="hidden" id="idinscripcion" name="idinscripcion" value="">
                    <div class="mb-3">
                        <label>Alumno:</label>
                        <input type="text" id="nombre_alumno" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Taller:</label>
                        <input type="text" id="nombre_taller" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Costo de Inscripción:</label>
                        <input type="text" id="costo_inscripcion" class="form-control" readonly>
                    </div>

                    <hr>
                    <!-- Contenedor para los formularios dinámicos de mensualidades -->
                    <div id="mensualidadesContainer">
                        <!-- Aquí se agregará dinámicamente cada bloque de mensualidad -->
                        <div class="mensualidadItem mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="mes[]">Mes</label>
                                    <select name="mes[]" class="form-select">
                                        <!-- Opciones generadas dinámicamente o predefinidas -->
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="fechalimite_mensual[]">Fecha Límite</label>
                                    <input type="date" name="fechalimite_mensual[]" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="monto_mensual[]">Monto Mensual</label>
                                    <input type="number" name="monto_mensual[]" class="form-control" readonly
                                        value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Botón para agregar otra mensualidad -->
                    <button type="button" id="btnAgregarMensualidad" class="btn btn-secondary">Agregar otra
                        mensualidad</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Ordenes de Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>
