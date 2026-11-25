<!-- Modal -->
<div class="modal fade" id="addAlumno" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Agregar Alumno</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addAlumnoForm" action="{{ route('saveAlumno') }}" method="POST">
                    @csrf
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="name" name="name"
                                style="text-transform: uppercase;" placeholder="Nombre:" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="apellidop" name="apellidop"
                                style="text-transform: uppercase;" placeholder="Apellido Paterno:" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="apellidom" name="apellidom"
                                style="text-transform: uppercase;" placeholder="Apellido Materno:" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="curp" name="curp" placeholder="CURP:" minlength="18" maxlength="21"
                                style="text-transform: uppercase;" required oninput="removerEspacios(this)">
                                <div id="curpErrorMessageAdd" style="color: red; display: none;"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="direccion" name="direccion"
                                style="text-transform: uppercase;" placeholder="Dirección:" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="telefono" name="telefono"
                                placeholder="TELÉFONO:" oninput="validarTelefono(this, 'phoneErrorMessageAdd')" minlength="10" maxlength="10">
                            <div id="phoneErrorMessageAdd" style="color: red; display: none;"></div>
                            {{-- <small id="errorTelefono" class="text-danger" style="display: none;">Debe contener 10 dígitos.</small> --}}
                        </div>
                    </div>
                    <div id="tutorFieldsAgregar" style="display: none;">
                        <div class="mb-3 row">
                            {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="nombretutor" name="nombretutor"
                                    style="text-transform: uppercase;" placeholder="Nombre del Tutor:">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            {{-- <label for="inputPassword" class="col-sm-10 col-form-label">Título de la noticia:</label> --}}
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="telefonotutor" name="telefonotutor"
                                    placeholder="TELÉFONO DEL TUTOR:" oninput="validarTelefono(this, 'phoneErrorMessageAdd_2')" minlength="10" maxlength="10">
                                <div id="phoneErrorMessageAdd_2" style="color: red; display: none;"></div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" id="submitBtnAdd" class="btn btn-primary" disabled>Guardar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        const curpInput = document.getElementById('curp');
        const submitBtn = document.getElementById('submitBtnAdd');
        const tutorFields = document.getElementById('tutorFieldsAgregar');
        const form = document.getElementById('addAlumnoForm');

        // Expresión regular para validar CURP
        const regex = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{2}$/;



        // Evento para validar y calcular edad
        curpInput.addEventListener('input', function () {
            const curp = this.value.toUpperCase();
            this.value = curp; // Asegurar mayúsculas
            submitBtn.disabled = true; // Deshabilitar botón por defecto
            tutorFields.style.display = 'none'; // Ocultar campos de tutor

            if (curp.length === 18 && regex.test(curp)) {
                // Extraer fecha de nacimiento del CURP
                const year = parseInt(curp.substr(4, 2)) + (curp.substr(4, 2) > 30 ? 1900 : 2000);
                const month = parseInt(curp.substr(6, 2)) - 1; // Meses van de 0 a 11
                const day = parseInt(curp.substr(8, 2));
                const birthDate = new Date(year, month, day);

                // Calcular edad
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                if (today < new Date(today.getFullYear(), birthDate.getMonth(), birthDate.getDate())) {
                    age--;
                }

                // Mostrar campos de tutor si es menor de edad
                if (age < 18) {
                    tutorFields.style.display = 'block';
                }

                submitBtn.disabled = false; // Habilitar botón
            }
        });

        // Validación final al enviar el formulario
        form.addEventListener('submit', function (event) {
            if (!regex.test(curpInput.value)) {
                event.preventDefault();
                alert('Por favor, introduce un CURP válido.');
            }
        });
    });
</script> --}}

{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('curp').addEventListener('input', function() {
            let curp = this.value.toUpperCase();
            if (curp.length === 18) {
                let year = parseInt(curp.substr(4, 2));
                let month = parseInt(curp.substr(6, 2));
                let day = parseInt(curp.substr(8,
                    2));

                // Ajustar el año según el siglo 
                year = year > new Date().getFullYear() % 100 ? 1900 + year : 2000 + year;
                let birthDate = new Date(year, month - 1, day);
                let age = new Date().getFullYear() - birthDate.getFullYear();
                let monthDiff = new Date().getMonth() - birthDate.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && new Date().getDate() < birthDate.getDate())) {
                    age--;
                }

                // Mostrar u ocultar campos de tutor según la edad 
                if (age < 18) {
                    document.getElementById('tutor-fields').style.display = 'block';
                } else {
                    document.getElementById('tutor-fields').style.display = 'none';
                }
            } else {
                document.getElementById('tutor-fields').style.display = 'none';
            }
        });
    });
</script> --}}

{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        const curpInput = document.getElementById('curp');
        const submitBtn = document.getElementById('submitBtn');

        // Evento para convertir a mayúsculas y validar longitud del CURP
        curpInput.addEventListener('input', function() {
            let curp = this.value.toUpperCase();
            this.value = curp; // Convertir el valor a mayúsculas
            // Habilitar/deshabilitar el botón de envío según la longitud del CURP
            if (curp.length === 18) {
                submitBtn.disabled = false;
                let year = parseInt(curp.substr(4, 2));
                let month = parseInt(curp.substr(6, 2));
                let day = parseInt(curp.substr(8,
                    2));
                // Ajustar el año según el siglo
                year = year > new Date().getFullYear() % 100 ? 1900 + year : 2000 + year;

                let birthDate = new Date(year, month - 1, day);
                let age = new Date().getFullYear() - birthDate.getFullYear();

                let monthDiff = new Date().getMonth() - birthDate.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && new Date().getDate() < birthDate.getDate())) {
                    age--;
                }

                // Mostrar u ocultar campos de tutor según la edad
                if (age < 18) {
                    document.getElementById('tutor-fields').style.display = 'block';
                } else {
                    document.getElementById('tutor-fields').style.display = 'none';
                }
            } else {
                submitBtn.disabled = true;
                document.getElementById('tutor-fields').style.display = 'none';
            }
        });

        // Validación final al enviar el formulario
        document.getElementById('addAlumnoForm').addEventListener('submit', function(event) {
            if (curpInput.value.length !== 18) {
                event.preventDefault();
                alert('El CURP debe tener exactamente 18 caracteres.');
            }
        });
    });
</script> --}}
