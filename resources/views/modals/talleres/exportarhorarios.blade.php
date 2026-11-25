<!-- Modal: exportar horarios a Excel -->
<div class="modal fade" id="modalExportarHorarios" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('exportarHorarios') }}" method="GET" id="formExportarHorarios">
                <div class="modal-header">
                    <h5 class="modal-title">Exportar horarios a Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    @if ($periodos->isEmpty())
                        <div class="alert alert-warning mb-0">No hay talleres activos con horario para exportar.</div>
                    @else
                        <div class="mb-3">
                            <label for="periodoHorarios" class="form-label">Periodo</label>
                            <select class="form-select" id="periodoHorarios" name="periodo" required>
                                @foreach ($periodos as $p)
                                    <option value="{{ $p['valor'] }}">
                                        {{ \Carbon\Carbon::parse($p['fechainicio'])->format('d/m/Y') }} al
                                        {{ \Carbon\Carbon::parse($p['fechafin'])->format('d/m/Y') }}
                                        ({{ $p['talleres'] }} taller{{ $p['talleres'] == 1 ? '' : 'es' }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                Incluye también los talleres con otras fechas que se cruzan con el periodo
                                (ej. uno de agosto a noviembre sale en el de febrero a noviembre).
                            </div>
                        </div>

                        <div>
                            <label for="tallerHorarios" class="form-label">Talleres</label>
                            <select class="form-select" id="tallerHorarios" name="idtaller">
                                <option value="">Todos los talleres</option>
                            </select>
                        </div>

                        <div class="alert alert-light border small mt-3 mb-0">
                            Una hoja por día y edificio (ej. <strong>LUNES LEONA VICARIO (LV)</strong>), con las aulas
                            en columnas y medias horas de 9:00 a 21:00. Si eliges un solo taller, solo salen los días y
                            aulas donde tiene horario.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" @disabled($periodos->isEmpty())>
                        <i class="fa-solid fa-file-excel"></i> Descargar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
