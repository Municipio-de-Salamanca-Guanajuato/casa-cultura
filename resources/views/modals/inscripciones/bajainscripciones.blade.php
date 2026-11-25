<!-- Modal Preview Bajas -->
<div class="modal fade" id="modalPreviewBajas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Previsualizar bajas de inscripciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de dar de baja las inscripciones de los alumnos que pagaron completo en periodos
                    pasados?</p>
                <p><strong>Candidatos encontrados:</strong> <span id="previewCount">0</span></p>

                <div id="previewListWrapper" style="max-height:300px; overflow:auto;">
                    <table class="table table-sm" id="previewList">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Alumno</th>
                                <th>Taller</th>
                                <th>Periodo</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="alert alert-warning mt-2">
                    <strong>Nota:</strong> Se darán de baja las inscripciones cuyo taller ya terminó y que tengan todas
                    sus órdenes pagadas.
                </div>
            </div>
            <div class="modal-footer">
                <button id="btnCancelPreview" type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Cancelar</button>
                <button id="btnExecuteBajas" type="button" class="btn btn-danger" disabled>  Dar de baja</button>
            </div>
        </div>
    </div>
</div>
