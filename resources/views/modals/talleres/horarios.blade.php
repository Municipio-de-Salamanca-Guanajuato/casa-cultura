{{-- Paso de horarios del taller (compartido por los modales de agregar y editar). Requiere $pref (add | edit). --}}
<div class="horarios-editor" data-pref="{{ $pref }}">
    {{-- Armar un bloque --}}
    <div class="p-3 rounded-3 bg-body-tertiary border mb-3">
        <label class="form-label fw-semibold mb-2">Días de la semana</label>
        <div class="d-flex flex-wrap gap-2 mb-3">
            @foreach (\App\Models\TallerHorario::DIAS as $num => $dia)
                <input type="checkbox" class="btn-check hz-dia" id="hz-{{ $pref }}-dia-{{ $num }}" value="{{ $num }}" autocomplete="off">
                <label class="btn btn-outline-primary hz-dia-btn" for="hz-{{ $pref }}-dia-{{ $num }}">
                    <span class="fw-semibold d-block">{{ mb_substr($dia, 0, 3) }}</span>
                    <small class="d-none d-sm-block">{{ $dia }}</small>
                </label>
            @endforeach
        </div>

        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label mb-1">Edificio</label>
                <select class="form-select hz-edificio">
                    <option value="">Edificio:</option>
                    @foreach ($edificios as $edificio)
                        <option value="{{ $edificio->idedificio }}">{{ $edificio->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">Aula</label>
                <select class="form-select hz-aula" disabled>
                    <option value="">Primero el edificio</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1">Hora inicio</label>
                <select class="form-select hz-inicio"></select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1">Hora fin</label>
                <select class="form-select hz-fin"></select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="button" class="btn btn-primary hz-agregar">
                    <span class="hz-agregar-texto"><i class="bi bi-plus-lg"></i> Agregar</span>
                    <span class="hz-agregar-cargando d-none"><span class="spinner-border spinner-border-sm"></span> Revisando…</span>
                </button>
            </div>
        </div>
        <div class="hz-error text-danger small mt-2" style="display: none;"></div>
        @if ($edificios->isEmpty())
            <div class="form-text text-danger mt-2">No hay edificios con aulas activas. Regístralos en Administración de Talleres.</div>
        @endif
    </div>

    {{-- Bloques agregados --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="text-uppercase small fw-semibold text-body-secondary">
            Sesiones configuradas (<span class="hz-conteo">0</span>)
        </span>
        <span class="small text-primary fw-semibold hz-total"></span>
    </div>
    <div class="hz-lista d-grid gap-2"></div>
    <div class="hz-vacio text-center text-body-secondary small border rounded-3 py-3">
        <i class="bi bi-calendar-x"></i> Sin horarios. Puedes guardar el taller y agregarlos después.
    </div>

    @if ($pref === 'edit')
        {{-- Horarios dados de baja: solo consulta (ya no se reactivan) --}}
        <div class="hz-inactivos-seccion mt-4" style="display: none;">
            <div class="text-uppercase small fw-semibold text-body-secondary mb-1">
                Horarios inactivos (<span class="hz-inactivos-conteo">0</span>)
            </div>
            <div class="form-text mt-0 mb-2">Solo consulta. Si necesitas ese horario otra vez, agrégalo arriba.</div>
            <div class="hz-inactivos d-grid gap-2"></div>
        </div>
    @endif

    {{-- Aquí se generan los inputs ocultos horarios[i][...] que se envían al guardar --}}
    <div class="hz-inputs"></div>
</div>
