<!-- Modal: previsualizar baja masiva de talleres terminados -->
<div class="modal fade" id="modalPreviewBajasTalleres" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Previsualizar bajas de talleres terminados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>Talleres activos cuya fecha de fin ya concluyó.</p>

                {{-- Se pueden dar de baja --}}
                <p class="mb-1"><strong>Se darán de baja:</strong> <span id="bajasTalleresCount">0</span></p>
                <div style="max-height:260px; overflow:auto;">
                    <table class="table table-sm" id="bajasTalleresLista">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Taller</th>
                                <th>Maestro</th>
                                <th>Periodo</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                {{-- No se pueden dar de baja (tienen inscripciones activas) --}}
                <div id="bajasTalleresBloqueadosSeccion" class="mt-3" style="display: none;">
                    <div class="alert alert-danger mb-2">
                        <strong>No se pueden dar de baja (tienen inscripciones activas):</strong>
                        <span id="bajasTalleresBloqueadosTexto"></span>
                        <br>Primero usa la baja de inscripciones en Inscripción.
                    </div>
                </div>

                <div class="alert alert-warning mt-2 mb-0">
                    <strong>Nota:</strong> Solo se dan de baja los talleres que ya terminaron y que no tienen
                    inscripciones activas.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnBajasTalleres" type="button" class="btn btn-danger" disabled>Dar de baja</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: confirmación (se abre encima del anterior) -->
<div class="modal fade" id="modalConfirmarBajasTalleres" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle text-danger"></i> Confirmar baja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="confirmarBajasTalleresTexto"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnConfirmarBajasTalleres" type="button" class="btn btn-danger">
                    <span class="confirmar-texto">Confirmar</span>
                    <span class="confirmar-cargando d-none"><span class="spinner-border spinner-border-sm"></span> Procesando…</span>
                </button>
            </div>
        </div>
    </div>
</div>
