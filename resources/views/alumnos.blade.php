@extends('layouts.carcasa')

@section('title', 'Alumnos')
@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <p>
                            <b style="text-transform: uppercase">Registrar Alumnos</b>
                            <br>
                            Listado de alumnos
                        </p>
                        <div style=" text-align: right;">
                            <button type="button" class="btn btn-md btn-success" data-bs-toggle="modal"
                                data-bs-target="#addAlumno">
                                <i class="bi bi-backpack"></i>
                            </button>
                        </div>
                    </div>
                    {{-- Alumnos activos --}}
                    <div class="card-body">
                        <h2 class="text-center">Alumnos activos</h2>
                        <table id="tblalumnosact" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>CURP</th>
                                    <th>Teléfono</th>
                                    <th>Direccion</th>
                                    <th>Tutor</th>
                                    <th>Teléfono de tutor</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    {{-- Alumnos inactivos --}}
                    <div class="card-body">
                        <h2 class="text-center">Alumnos inactivos</h2>
                        <table id="tblalumnosina" class="table table-striped dt-responsive nowrap border"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>CURP</th>
                                    <th>Teléfono</th>
                                    <th>Direccion</th>
                                    <th>Tutor</th>
                                    <th>Teléfono de tutor</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modales --}}
    @include('modals.alumnos.addalumno')
    @include('modals.alumnos.editalumno')
@endsection

@section('scripts')
    {{-- AGREGAR ALUMNO --}}
    <script>
        function removerEspacios(input) {
            input.value = input.value.replace(/\s/g, ''); // Reemplaza todos los espacios
        }

        // document.getElementById('curp').addEventListener('blur', function() {
        //     let curp = this.value.trim();
        //     let errorDiv = document.getElementById('curpErrorMessageAdd');
        //     let submitBtn = document.getElementById('submitBtnAdd');

        //     if (curp.length === 18) {
        //         fetch(`/ajaxConsulta/verificarcurp/alumno?curp=${curp}`)
        //             .then(response => response.json())
        //             .then(data => {
        //                 if (data.existe) {
        //                     errorDiv.textContent = 'Esta CURP ya está registrada.';
        //                     errorDiv.style.display = 'block';
        //                     submitBtn.disabled = true;
        //                 } else {
        //                     errorDiv.style.display = 'none';
        //                     submitBtn.disabled = false;
        //                 }
        //             })
        //             .catch(error => console.error('Error en la consulta:', error));
        //     }
        // });

        document.addEventListener('DOMContentLoaded', function() {
            const curpInput = document.getElementById('curp');
            const submitBtn = document.getElementById('submitBtnAdd');
            const tutorFields = document.getElementById('tutorFieldsAgregar');
            const telefonoInput = document.getElementById('telefono');
            const telefonoTutorInput = document.getElementById('telefonotutor');
            const form = document.getElementById('addAlumnoForm');
            const errorMessageCurp = document.getElementById('curpErrorMessageAdd');
            const errorMessageTelefono = document.getElementById('phoneErrorMessageAdd');
            const errorMessageTelefonoTutor = document.getElementById('phoneErrorMessageAdd_2');

            let curpRegistrada = false;

            const regexCurp =
                /^[A-Z]{4}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]\d(?:-[0-9]+)?$/;

            window.validarTelefono = function(input, errorDivId) {
                let valor = input.value.replace(/\D/g, '');
                input.value = valor;
                let errorDiv = document.getElementById(errorDivId);
                if (valor.length !== 10) {
                    errorDiv.textContent = 'Debe contener 10 dígitos.';
                    errorDiv.style.display = 'block';
                    return false;
                } else {
                    errorDiv.style.display = 'none';
                    return true;
                }
            };

            // 1) Función global para validar que la fecha del CURP sea real
            function fechaCurpEsValida(curp) {
                const yearPart = parseInt(curp.substr(4, 2), 10);
                const month = parseInt(curp.substr(6, 2), 10);
                const day = parseInt(curp.substr(8, 2), 10);
                const year = yearPart > 30 ? 1900 + yearPart : 2000 + yearPart;
                const d = new Date(year, month - 1, day);
                return d.getFullYear() === year && (d.getMonth() + 1) === month && d.getDate() === day;
            }

            // 2) Listener principal
            curpInput.addEventListener('input', function() {
                const curp = this.value = this.value.toUpperCase();
                tutorFields.style.display = 'none';
                telefonoInput.required = false;
                errorMessageCurp.style.display = 'none';

                if (curp.length >= 18 && curp.length <= 21 && regexCurp.test(curp)) {
                    // validar fecha real
                    if (!fechaCurpEsValida(curp)) {
                        errorMessageCurp.textContent = 'La fecha en el CURP no es válida.';
                        errorMessageCurp.style.display = 'block';
                        curpRegistrada = false;
                        validarFormulario();
                        return;
                    }

                    // calcular edad
                    const yearPart = parseInt(curp.substr(4, 2), 10);
                    const month = parseInt(curp.substr(6, 2), 10) - 1;
                    const day = parseInt(curp.substr(8, 2), 10);
                    const year = yearPart > 30 ? 1900 + yearPart : 2000 + yearPart;
                    const birthDate = new Date(year, month, day);

                    let age = new Date().getFullYear() - birthDate.getFullYear();
                    if (new Date() < new Date(new Date().getFullYear(), birthDate.getMonth(), birthDate
                            .getDate())) {
                        age--;
                    }

                    if (age < 18) {
                        tutorFields.style.display = 'block';
                        telefonoInput.required = false;
                    } else {
                        telefonoInput.required = true;
                    }

                    verificarCurpEnBaseDeDatos(curp);
                } else {
                    errorMessageCurp.textContent = curp.length < 18 ?
                        'El CURP debe tener mínimo 18 caracteres.' :
                        'Por favor, introduce un CURP válido.';
                    errorMessageCurp.style.display = 'block';
                    curpRegistrada = false;
                    validarFormulario();
                }
            });

            function verificarCurpEnBaseDeDatos(curp) {
                fetch(`/ajaxConsulta/verificarcurp/agregar?curp=${curp}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.exists) {
                            errorMessageCurp.textContent = 'Esta CURP ya está registrada.';
                            errorMessageCurp.style.display = 'block';
                            curpRegistrada = true;
                        } else {
                            errorMessageCurp.style.display = 'none';
                            curpRegistrada = false;
                        }
                        validarFormulario();
                    })
                    .catch(console.error);
            }

            telefonoInput.addEventListener('input', () => {
                validarTelefono(telefonoInput, 'phoneErrorMessageAdd');
                validarFormulario();
            });
            telefonoTutorInput.addEventListener('input', () => {
                validarTelefono(telefonoTutorInput, 'phoneErrorMessageAdd_2');
                validarFormulario();
            });

            // 3) validarFormulario ahora incluye la fecha real:
            function validarFormulario() {
                const curp = curpInput.value;
                const curpValida = regexCurp.test(curp) && !curpRegistrada && fechaCurpEsValida(curp);
                const telValida = telefonoInput.required ? validarTelefono(telefonoInput, 'phoneErrorMessageAdd') :
                    true;
                const tutValida = tutorFields.style.display === 'block' ?
                    validarTelefono(telefonoTutorInput, 'phoneErrorMessageAdd_2') :
                    true;
                submitBtn.disabled = !(curpValida && telValida && tutValida);
            }

            form.addEventListener('submit', function(event) {
                if (!regexCurp.test(curpInput.value)) {
                    event.preventDefault();
                    alert('Por favor, introduce un CURP válido.');
                }
            });
        });

        // document.addEventListener('DOMContentLoaded', function() {
        //     const curpInput = document.getElementById('curp');
        //     const submitBtn = document.getElementById('submitBtnAdd');
        //     const tutorFields = document.getElementById('tutorFieldsAgregar');
        //     const telefonoInput = document.getElementById('telefono');
        //     const form = document.getElementById('addAlumnoForm');
        //     const errorMessage = document.getElementById('curpErrorMessageAdd'); // Mensaje de error para CURP
        //     const phoneErrorMessage = document.getElementById('phoneErrorMessageAdd'); // Mensaje de error para Teléfono
        //     const errorMessageTelefonoTutor = document.getElementById('curpErrorMessageAdd_2');

        //     // Expresión regular para validar CURP
        //     const regex = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{2}$/;



        //     // Evento para validar CURP y manejar campos de tutor
        //     curpInput.addEventListener('input', function() {
        //         const curp = this.value.toUpperCase();
        //         this.value = curp; // Asegurar mayúsculas
        //         submitBtn.disabled = true; // Deshabilitar botón por defecto
        //         tutorFields.style.display = 'none'; // Ocultar campos de tutor
        //         telefonoInput.required = false; // Por defecto, no es requerido

        //         if (curp.length === 18 && regex.test(curp)) {
        //             // Extraer fecha de nacimiento del CURP
        //             const year = parseInt(curp.substr(4, 2)) + (curp.substr(4, 2) > 30 ? 1900 : 2000);
        //             const month = parseInt(curp.substr(6, 2)) - 1; // Meses van de 0 a 11
        //             const day = parseInt(curp.substr(8, 2));
        //             const birthDate = new Date(year, month, day);

        //             // Calcular edad
        //             const today = new Date();
        //             let age = today.getFullYear() - birthDate.getFullYear();
        //             if (today < new Date(today.getFullYear(), birthDate.getMonth(), birthDate.getDate())) {
        //                 age--;
        //             }

        //             // Manejar campos según la edad
        //             if (age < 18) {
        //                 tutorFields.style.display = 'block';
        //             } else {
        //                 telefonoInput.required = true; // Hacer el teléfono requerido
        //             }

        //             submitBtn.disabled = !validarTelefono(
        //                 telefonoInput);; // Habilitar botón // Verifica si el teléfono es válido
        //             errorMessage.style.display = 'none'; // Ocultar mensaje de error si el CURP es válido
        //         } else if (curp.length !== 18) {
        //             // Mostrar mensaje de error si CURP tiene menos de 18 caracteres
        //             submitBtn.disabled = true;
        //             errorMessage.textContent = 'El CURP debe tener 18 caracteres.';
        //             errorMessage.style.display =
        //                 'block'; // Mostrar mensaje de error si el CURP es incompleto
        //         } else {
        //             // Mostrar mensaje de error si CURP es inválido
        //             submitBtn.disabled = true;
        //             errorMessage.textContent = 'Por favor, introduce un CURP válido.';
        //             errorMessage.style.display = 'block'; // Mostrar mensaje de error si el CURP es inválido
        //         }
        //     });

        // Validar solo números en teléfono
        // function validarTelefono(input, errorDiv) {
        //     let valor = input.value.replace(/\D/g, ''); // Eliminar caracteres no numéricos
        //     input.value = valor; // Actualizar el valor con solo números

        //     if (valor.length !== 10) {
        //         errorDiv.textContent = 'Debe contener exactamente 10 dígitos.';
        //         errorDiv.style.display = 'block';
        //         return false;
        //     } else {
        //         errorDiv.style.display = 'none';
        //         return true;
        //     }
        // }

        // Validación de teléfono (exactamente 10 dígitos)
        // function validarTelefono(input) {
        //     const telefono = input.value.replace(/\D/g, ''); // Elimina caracteres no numéricos
        //     input.value = telefono.slice(0, 10); // Limita a 10 caracteres numéricos

        //     if (telefono.length !== 10) {
        //         phoneErrorMessage.textContent = "El número de teléfono debe contener exactamente 10 dígitos.";
        //         phoneErrorMessage.style.display = "block";
        //         submitBtn.disabled = true;
        //         return false;
        //     } else {
        //         phoneErrorMessage.style.display = "none";
        //         submitBtn.disabled = false;
        //         return true;
        //     }
        // }

        // Validación final al enviar el formulario
        //     form.addEventListener('submit', function(event) {
        //         // if (!regex.test(curpInput.value)) {
        //         //     event.preventDefault();
        //         //     alert('Por favor, introduce un CURP válido.');
        //         // }
        //         if (!regex.test(curpInput.value)) {
        //             event.preventDefault();
        //             alert('Por favor, introduce un CURP válido.');
        //         }
        //         if (!validarTelefono(telefonoInput)) {
        //             event.preventDefault();
        //             alert('Por favor, introduce un número de teléfono válido.');
        //         }
        //     });

        //     // Asegurar validación del teléfono al cargar la página
        //     validarTelefono(telefonoInput);
        // });

        // document.addEventListener('DOMContentLoaded', function() {
        //     const curpInput = document.getElementById('curp');
        //     const submitBtn = document.getElementById('submitBtnAdd');
        //     const tutorFields = document.getElementById('tutorFieldsAgregar');
        //     const telefonoInput = document.getElementById('telefono');
        //     const form = document.getElementById('addAlumnoForm');

        //     // Expresión regular para validar CURP
        //     const regex = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{2}$/;

        //     // Evento para validar CURP y manejar campos de tutor
        //     curpInput.addEventListener('input', function() {
        //         const curp = this.value.toUpperCase();
        //         this.value = curp; // Asegurar mayúsculas
        //         submitBtn.disabled = true; // Deshabilitar botón por defecto
        //         tutorFields.style.display = 'none'; // Ocultar campos de tutor
        //         telefonoInput.required = false; // Por defecto, no es requerido

        //         if (curp.length === 18 && regex.test(curp)) {
        //             // Extraer fecha de nacimiento del CURP
        //             const year = parseInt(curp.substr(4, 2)) + (curp.substr(4, 2) > 30 ? 1900 : 2000);
        //             const month = parseInt(curp.substr(6, 2)) - 1; // Meses van de 0 a 11
        //             const day = parseInt(curp.substr(8, 2));
        //             const birthDate = new Date(year, month, day);

        //             // Calcular edad
        //             const today = new Date();
        //             let age = today.getFullYear() - birthDate.getFullYear();
        //             if (today < new Date(today.getFullYear(), birthDate.getMonth(), birthDate.getDate())) {
        //                 age--;
        //             }

        //             // Manejar campos según la edad
        //             if (age < 18) {
        //                 tutorFields.style.display = 'block';
        //             } else {
        //                 telefonoInput.required = true; // Hacer el teléfono requerido
        //             }

        //             submitBtn.disabled = false; // Habilitar botón
        //         }
        //     });

        //     // Validación final al enviar el formulario
        //     form.addEventListener('submit', function(event) {
        //         if (!regex.test(curpInput.value)) {
        //             event.preventDefault();
        //             alert('Por favor, introduce un CURP válido.');
        //         }
        //     });
        // });
    </script>

    {{-- EDITAR ALUMNO --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const curpInput = document.getElementById('curp_2');
            const telefonoInput = document.getElementById('telefono_2');
            const telefonotutorInput = document.getElementById('telefonotutor_2');
            const submitBtn = document.getElementById('submitBtnEdit');
            const errorMessageCurp = document.getElementById('curpErrorMessage');
            const errorMessagePhone = document.getElementById('phoneErrorMessage_3');
            const errorMessagePhoneTutor = document.getElementById('phoneErrorMessage_4');
            const form = document.getElementById('editAlumnoForm');
            const idAlumnoInput = document.getElementById('idalumno_2');

            const regexCurp = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{2}(?:-[0-9]+)?$/;
            const regexTelefono = /^[0-9]{10}$/;

            // 1) Guardamos los valores iniciales al abrir el modal
            const initialCurp = curpInput.value;
            const initialTelefono = telefonoInput.value;
            const initialTelefonoTutor = telefonotutorInput.value;

            let curpValido = false;
            let curpUnico = true;
            let telefonoValido = true;
            let telefonotutorValido = true;

            // 2) Comprueba si ALGÚN campo cambió
            function isDirty() {
                return (
                    curpInput.value !== initialCurp ||
                    telefonoInput.value !== initialTelefono ||
                    telefonotutorInput.value !== initialTelefonoTutor
                );
            }

            // 3) Actualiza el estado del botón incluyendo la condición de "dirty"
            function actualizarEstadoBoton() {
                const debeHabilitar =
                    curpValido &&
                    curpUnico &&
                    telefonoValido &&
                    telefonotutorValido &&
                    isDirty();
                submitBtn.disabled = !debeHabilitar;
            }

            // 4) Función para validar fecha real en CURP
            function fechaCURPValida(curp) {
                const fecha = curp.substring(4, 10); // AAMMDD
                const año = parseInt(fecha.substring(0, 2), 10);
                const mes = parseInt(fecha.substring(2, 4), 10);
                const dia = parseInt(fecha.substring(4, 6), 10);
                const siglo = año < 50 ? 2000 : 1900;
                const d = new Date(siglo + año, mes - 1, dia);
                return (
                    d.getFullYear() === siglo + año &&
                    d.getMonth() + 1 === mes &&
                    d.getDate() === dia
                );
            }

            // 5) Validación de CURP en tiempo real
            curpInput.addEventListener('input', function() {
                const curp = this.value = this.value.toUpperCase();
                // 5.1 Formato y longitud
                if (curp.length < 18 || !regexCurp.test(curp)) {
                    curpValido = false;
                    errorMessageCurp.textContent =
                        'El CURP debe tener mínimo 18 caracteres y formato válido.';
                    errorMessageCurp.style.display = 'block';
                    curpUnico = true; // evitamos el doble mensaje
                    actualizarEstadoBoton();
                    return;
                }
                // 5.2 Fecha real
                if (!fechaCURPValida(curp)) {
                    curpValido = false;
                    errorMessageCurp.textContent = 'La fecha en el CURP no es válida.';
                    errorMessageCurp.style.display = 'block';
                    curpUnico = true;
                    actualizarEstadoBoton();
                    return;
                }

                // 5.3 Todo bien hasta ahora
                curpValido = true;
                errorMessageCurp.style.display = 'none';

                // 5.4 Verificar unicidad
                verificarCURP(curp)
                    .then(actualizarEstadoBoton)
                    .catch(err => {
                        console.error(err);
                        curpUnico = false;
                        actualizarEstadoBoton();
                    });
            });

            // 6) Validación de teléfono
            telefonoInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
                telefonoValido = this.value === '' || regexTelefono.test(this.value);
                if (!telefonoValido) {
                    errorMessagePhone.textContent =
                        'El teléfono debe contener exactamente 10 dígitos numéricos.';
                    errorMessagePhone.style.display = 'block';
                } else {
                    errorMessagePhone.style.display = 'none';
                }
                actualizarEstadoBoton();
            });

            // 7) Validación de teléfono del tutor
            telefonotutorInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
                telefonotutorValido = this.value === '' || regexTelefono.test(this.value);
                if (!telefonotutorValido) {
                    errorMessagePhoneTutor.textContent =
                        'El teléfono del tutor debe contener exactamente 10 dígitos numéricos.';
                    errorMessagePhoneTutor.style.display = 'block';
                } else {
                    errorMessagePhoneTutor.style.display = 'none';
                }
                actualizarEstadoBoton();
            });

            // 8) Verificar CURP único en BD
            async function verificarCURP(curp) {
                try {
                    const response = await fetch(
                        `/ajaxConsulta/verificarcurp/editar?curp=${curp}&idalumno=${idAlumnoInput.value}`);
                    const data = await response.json();
                    if (data.exists) {
                        curpUnico = false;
                        errorMessageCurp.textContent = 'Este CURP ya está registrado en otro alumno.';
                        errorMessageCurp.style.display = 'block';
                    } else {
                        curpUnico = true;
                        errorMessageCurp.style.display = 'none';
                    }
                } catch (error) {
                    console.error('Error al verificar el CURP:', error);
                    curpUnico = false;
                }
            }

            // 9) Validación final al enviar
            form.addEventListener('submit', function(event) {
                const curp = curpInput.value.trim();
                if (!regexCurp.test(curp) || !fechaCURPValida(curp)) {
                    event.preventDefault();
                    alert('Introduce un CURP válido con fecha real.');
                }
                if (telefonoInput.value && !regexTelefono.test(telefonoInput.value)) {
                    event.preventDefault();
                    alert('El teléfono debe contener exactamente 10 dígitos numéricos.');
                }
                if (telefonotutorInput.value && !regexTelefono.test(telefonotutorInput.value)) {
                    event.preventDefault();
                    alert('El teléfono del tutor debe contener exactamente 10 dígitos numéricos.');
                }
            });
        });
    </script>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const curpInput = document.getElementById('curp_2');
            const telefonoInput = document.getElementById('telefono_2');
            const telefonotutorInput = document.getElementById(
                'telefonotutor_2'); // Asegúrate de que el ID sea correcto
            const submitBtn = document.getElementById('submitBtnEdit');
            const errorMessageCurp = document.getElementById('curpErrorMessage');
            const errorMessagePhone = document.getElementById('phoneErrorMessage_3');
            const errorMessagePhoneTutor = document.getElementById('phoneErrorMessage_4');
            const form = document.getElementById('editAlumnoForm');
            const idAlumnoInput = document.getElementById('idalumno_2'); // ID del alumno en edición

            // Expresión regular para validar CURP
            const regexCurp = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{2}(?:-[0-9]+)?$/;

            // Expresión regular para validar teléfono (exactamente 10 dígitos numéricos)
            const regexTelefono = /^[0-9]{10}$/;

            let curpValido = false;
            let curpUnico = true;
            let telefonoValido = true;
            let telefonotutorValido = true;

            function actualizarEstadoBoton() {
                if (curpValido && curpUnico && telefonoValido && telefonotutorValido) {
                    submitBtn.disabled = false;
                } else {
                    submitBtn.disabled = true;
                }
            }

            // Validar CURP en tiempo real
            curpInput.addEventListener('input', function() {
                const curp = this.value.toUpperCase();
                this.value = curp; // Asegurar mayúsculas

                // 1) Validación de longitud y regex
                if ((curp.length < 18) || !regexCurp.test(curp)) {
                    curpValido = false; // ← marca inválido
                    submitBtn.disabled = true;
                    errorMessageCurp.textContent = 'El CURP debe tener mínimo 18 caracteres';
                    errorMessageCurp.style.display = 'block';
                } // 2) Validación de fecha real
                else if (!fechaCURPValida(curp)) {
                    curpValido = false; // ← marca inválido
                    submitBtn.disabled = true;
                    errorMessageCurp.textContent = 'La fecha en el CURP no es válida.';
                    errorMessageCurp.style.display = 'block';
                } // 3) Formato y fecha OK: marcar CURP válido y lanzar verificación única
                else {
                    curpValido = true; // ← marca válido
                    errorMessageCurp.style.display = 'none';
                    verificarCURP(curp)
                        .then(() => {
                            actualizarEstadoBoton(); // ← IMPORTANTE: actualizar aquí
                        })
                        .catch(error => console.error(error));
                    // .then(() => console.log('Verificación completada'))
                    // .catch(error => console.error('Error en la verificación:', error));
                }
            });

            // if ((curp.length < 18) || !regexCurp.test(curp)) {
            //     submitBtn.disabled = true;
            //     errorMessageCurp.textContent = 'El CURP debe tener mínimo 18 caracteres';
            //     errorMessageCurp.style.display = 'block';
            // } else {
            //     errorMessageCurp.style.display = 'none';
            //     verificarCURP(curp) // Llamar la función async
            //         .then(() => {
            //             console.log('Verificación completada');
            //         })
            //         .catch(error => {
            //             console.error('Error en la verificación:', error);
            //         });
            // }

            // Verificar si el CURP ya existe en la base de datos
            // await verificarCURP(curp);

            // Validar teléfono en tiempo real
            telefonoInput.addEventListener('input', function() {
                // Eliminar cualquier carácter no numérico
                this.value = this.value.replace(/[^0-9]/g, '');
                telefonoValido = this.value.length === 0 || regexTelefono.test(this.value);

                if (!telefonoValido) {
                    errorMessagePhone.textContent =
                        'El teléfono debe contener exactamente 10 dígitos numéricos.';
                    errorMessagePhone.style.display = 'block';
                } else {
                    errorMessagePhone.style.display = 'none';
                }

                actualizarEstadoBoton();
                // !regexTelefono.test(this.value)
                // if (this.value.length > 0 && !regexTelefono.test(this.value)) {
                //     // this.setCustomValidity('El teléfono debe contener exactamente 10 dígitos numéricos.');
                //     errorMessagePhone.textContent =
                //         'El teléfono debe contener exactamente 10 dígitos numéricos.';
                //     errorMessagePhone.style.display = 'block';
                //     submitBtn.disabled = true;
                // } else {
                //     // this.setCustomValidity('');
                //     errorMessagePhone.style.display = 'none';
                //     submitBtn.disabled = false;
                // }
            });

            // Validar teléfono del tutor en tiempo real
            telefonotutorInput.addEventListener('input', function() {
                // Eliminar cualquier carácter no numérico
                this.value = this.value.replace(/[^0-9]/g, '');
                telefonotutorValido = this.value.length === 0 || regexTelefono.test(this.value);

                if (!telefonotutorValido) {
                    errorMessagePhoneTutor.textContent =
                        'El teléfono del tutor debe contener exactamente 10 dígitos numéricos.';
                    errorMessagePhoneTutor.style.display = 'block';
                } else {
                    errorMessagePhoneTutor.style.display = 'none';
                }

                actualizarEstadoBoton();

                // if (this.value.length > 0 && !regexTelefono.test(this.value)) {
                //     errorMessagePhoneTutor.textContent =
                //         'El teléfono del tutor debe contener exactamente 10 dígitos numéricos.';
                //     errorMessagePhoneTutor.style.display = 'block';
                //     submitBtn.disabled = true;
                // } else {
                //     errorMessagePhoneTutor.style.display = 'none';
                //     submitBtn.disabled = false;
                // }

                // if (!regexTelefono.test(this.value)) {
                //     errorMessagePhoneTutor.textContent =
                //         'El teléfono del tutor debe contener exactamente 10 dígitos numéricos.';
                //     errorMessagePhoneTutor.style.display = 'block';
                //     submitBtn.disabled = true;
                // } else {
                //     errorMessagePhoneTutor.style.display = 'none';
                //     submitBtn.disabled = false;
                // }
            });

            // Verificar si el CURP ya está registrado en otro alumno
            async function verificarCURP(curp) {
                const idAlumno = idAlumnoInput.value; // ID del alumno actual

                try {
                    const response = await fetch(
                        `/ajaxConsulta/verificarcurp/editar?curp=${curp}&idalumno=${idAlumno}`);
                    const data = await response.json();

                    if (data.exists) {
                        curpUnico = false;
                        // submitBtn.disabled = true;
                        errorMessageCurp.textContent = 'Este CURP ya está registrado en otro alumno.';
                        errorMessageCurp.style.display = 'block';
                    } else {
                        curpUnico = true;
                        // submitBtn.disabled = false;
                        errorMessageCurp.style.display = 'none';
                    }
                    actualizarEstadoBoton(); // ← Asegúrate de llamar aquí también
                } catch (error) {
                    console.error('Error al verificar el CURP:', error);
                    curpUnico = false;
                    submitBtn.disabled = true; // Deshabilitar por seguridad si hay un error
                }
            }

            function fechaCURPValida(curp) {
                const fecha = curp.substring(4, 10); // AAMMDD
                const año = parseInt(fecha.substring(0, 2), 10);
                const mes = parseInt(fecha.substring(2, 4), 10);
                const dia = parseInt(fecha.substring(4, 6), 10);

                const siglo = (año < 50) ? 2000 : 1900;
                const fechaCompleta = new Date(siglo + año, mes - 1, dia);

                return fechaCompleta.getFullYear() === siglo + año &&
                    fechaCompleta.getMonth() + 1 === mes &&
                    fechaCompleta.getDate() === dia;
            }

            // function verificarCURP(curp) {
            //     const idAlumno = idAlumnoInput.value; // Obtener ID del alumno en edición

            //     fetch(`/ajaxConsulta/verificarcurp/editar?curp=${curp}&idalumno=${idAlumno}`)
            //         .then(response => response.json())
            //         .then(data => {
            //             if (data.exists) { // Se usa `exists` porque así lo definimos en el backend
            //                 submitBtn.disabled = true;
            //                 errorMessageCurp.textContent = 'Este CURP ya está registrado en otro alumno.';
            //                 errorMessageCurp.style.display = 'block';
            //             } else {
            //                 submitBtn.disabled = false;
            //                 errorMessageCurp.style.display = 'none';
            //             }
            //         })
            //         .catch(error => {
            //             console.error('Error al verificar el CURP:', error);
            //         });
            // }

            // Validación final al enviar el formulario
            form.addEventListener('submit', function(event) {
                // if (!regexCurp.test(curpInput.value)) {
                //     event.preventDefault();
                //     alert('Por favor, introduce un CURP válido.');
                // }
                const curp = curpInput.value.trim();

                if (!regexCurp.test(curp)) {
                    event.preventDefault();
                    alert('Por favor, introduce un CURP válido.');
                }

                if (telefonoInput.value.length > 0 && !regexTelefono.test(telefonoInput.value)) {
                    event.preventDefault();
                    alert('El teléfono debe contener exactamente 10 dígitos numéricos.');
                }
                // if (!regexTelefono.test(telefonoInput.value)) {
                //     event.preventDefault();
                //     alert('El teléfono debe contener exactamente 10 dígitos numéricos.');
                // }

                if (telefonotutorInput.value.length > 0 && !regexTelefono.test(telefonotutorInput.value)) {
                    event.preventDefault();
                    alert('El teléfono del tutor debe contener exactamente 10 dígitos numéricos.');
                }
            });
        });
    </script> --}}

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function () {
            const curpInput = document.getElementById('curp_2');
            const telefonoInput = document.getElementById('telefono_2');
            const submitBtn = document.getElementById('submitBtnEdit');
            const errorMessage = document.getElementById('curpErrorMessage');
            const form = document.getElementById('editAlumnoForm');
            const idAlumnoInput = document.getElementById('idalumno_2'); // ID del alumno que se está editando
    
            // Expresión regular para validar CURP
            const regexCURP = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{2}$/;
    
            // Expresión regular para validar teléfono (exactamente 10 dígitos numéricos)
            const regexTelefono = /^[0-9]{10}$/;
    
            // Validar CURP en tiempo real
            curpInput.addEventListener('input', function () {
                const curp = this.value.toUpperCase();
                this.value = curp; // Asegurar mayúsculas
                
                if (curp.length !== 18 || !regexCURP.test(curp)) {
                    submitBtn.disabled = true;
                    errorMessage.textContent = 'El CURP debe tener 18 caracteres y ser válido.';
                    errorMessage.style.display = 'block';
                } else {
                    errorMessage.style.display = 'none';
                    verificarCURP(curp);
                }
            });
    
            // Validar teléfono en tiempo real
            telefonoInput.addEventListener('input', function () {
                if (!regexTelefono.test(this.value)) {
                    this.setCustomValidity('El teléfono debe contener exactamente 10 dígitos numéricos.');
                } else {
                    this.setCustomValidity('');
                }
            });
    
            // Función para verificar si el CURP ya está registrado
            function verificarCURP(curp) {
                const idAlumno = idAlumnoInput.value; // Obtener el ID del alumno en edición
                fetch(`/ajaxConsulta/verificarcurp/alumno?curp=${curp}&idalumno=${idAlumno}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.existe) {
                            submitBtn.disabled = true;
                            errorMessage.textContent = 'Este CURP ya está registrado en otro alumno.';
                            errorMessage.style.display = 'block';
                        } else {
                            submitBtn.disabled = false;
                            errorMessage.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error al verificar el CURP:', error);
                    });
            }
    
            // Validación final al enviar el formulario
            form.addEventListener('submit', function (event) {
                if (!regexCURP.test(curpInput.value)) {
                    event.preventDefault();
                    alert('Por favor, introduce un CURP válido.');
                }
    
                if (!regexTelefono.test(telefonoInput.value)) {
                    event.preventDefault();
                    alert('El teléfono debe contener exactamente 10 dígitos numéricos.');
                }
            });
        });
    </script> --}}

    {{-- <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     const curpInput = document.getElementById('curp_2');
        //     const submitBtn = document.getElementById('submitBtnEdit');
        //     const tutorFields = document.getElementById('tutorFieldsEditar');
        //     const nombretutor = document.getElementById('nombretutor_2');
        //     const telefonotutor = document.getElementById('telefonotutor_2');
        //     const form = document.getElementById('editAlumnoForm');
        //     const inputs = form.querySelectorAll('input'); // Seleccionar todos los inputs del formulario
        //     const errorMessage = document.getElementById('curpErrorMessage'); // Mensaje de error para CURP

        //     // Expresión regular para validar CURP
        //     const regex = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{2}$/;

        //     // Guardar valores iniciales al cargar el modal
        //     let initialData = {};

        //     function guardarValoresIniciales() {
        //         initialData = {};
        //         inputs.forEach(input => {
        //             initialData[input.id] = input.value;
        //         });
        //     }

        //     // Detectar cambios en cualquier campo
        //     function manejarCambios() {
        //         const isDataChanged = Array.from(inputs).some(input => initialData[input.id] !== input.value);

        //         if (isDataChanged) {
        //             // Validar CURP solo si el campo CURP ha cambiado
        //             if (curpInput.value.length === 18 && regex.test(curpInput.value)) {
        //                 submitBtn.disabled = false; // Habilitar si el CURP es válido
        //                 errorMessage.style.display = 'none'; // Ocultar mensaje de error si el CURP es válido
        //             } else if (curpInput.value.length !== 18) {
        //                 submitBtn.disabled = true; // Deshabilitar si el CURP tiene menos de 18 caracteres
        //                 errorMessage.textContent = 'El CURP debe tener 18 caracteres.';
        //                 errorMessage.style.display = 'block'; // Mostrar mensaje de error si el CURP es incompleto
        //             } else {
        //                 submitBtn.disabled = true; // Deshabilitar si el CURP es inválido
        //                 errorMessage.textContent = 'Por favor, introduce un CURP válido.';
        //                 errorMessage.style.display = 'block'; // Mostrar mensaje de error si el CURP es inválido
        //             }
        //         } else {
        //             submitBtn.disabled = true; // Deshabilitar si no hay cambios
        //         }
        //     }

        //     // Validar CURP en tiempo real
        //     curpInput.addEventListener('input', function() {
        //         const curp = this.value.toUpperCase();
        //         this.value = curp; // Asegurar mayúsculas
        //         manejarCambios(); // Revalidar cambios
        //     });

        //     // Agregar evento para detectar cambios en cualquier campo
        //     inputs.forEach(input => {
        //         input.addEventListener('input', manejarCambios);
        //     });

        //     // Mostrar siempre los campos de tutor al cargar el modal
        //     function cargarCampos() {
        //         tutorFields.style.display = 'block';
        //     }

        //     // Validación final al enviar el formulario
        //     form.addEventListener('submit', function(event) {
        //         if (curpInput.value.length !== 18 || !regex.test(curpInput.value)) {
        //             event.preventDefault();
        //             alert('Por favor, introduce un CURP válido.');
        //         }
        //     });

        //     // Llamar a cargarCampos y guardar valores iniciales al abrir el modal
        //     cargarCampos();
        //     guardarValoresIniciales();
        // });

        // document.addEventListener('DOMContentLoaded', function() {
        //     const curpInput = document.getElementById('curp_2');
        //     const submitBtn = document.getElementById('submitBtnEdit');
        //     const tutorFields = document.getElementById('tutorFieldsEditar');
        //     const nombretutor = document.getElementById('nombretutor_2');
        //     const telefonotutor = document.getElementById('telefonotutor_2');
        //     const form = document.getElementById('editAlumnoForm');
        //     const inputs = form.querySelectorAll('input'); // Seleccionar todos los inputs del formulario

        //     // Expresión regular para validar CURP
        //     const regex = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{2}$/;

        //     // Guardar valores iniciales al cargar el modal
        //     let initialData = {};

        //     function guardarValoresIniciales() {
        //         initialData = {};
        //         inputs.forEach(input => {
        //             initialData[input.id] = input.value;
        //         });
        //     }

        //     // Detectar cambios en cualquier campo
        //     function manejarCambios() {
        //         const isDataChanged = Array.from(inputs).some(input => initialData[input.id] !== input.value);

        //         if (isDataChanged) {
        //             // Validar CURP solo si el campo CURP ha cambiado
        //             if (curpInput.value.length === 18 && !regex.test(curpInput.value)) {
        //                 submitBtn.disabled = true; // Mantener deshabilitado si el CURP es inválido
        //             } else {
        //                 submitBtn.disabled = false; // Habilitar si hay cambios y el CURP es válido
        //             }
        //         } else {
        //             submitBtn.disabled = true; // Deshabilitar si no hay cambios
        //         }
        //     }

        //     // Validar CURP en tiempo real
        //     curpInput.addEventListener('input', function() {
        //         const curp = this.value.toUpperCase();
        //         this.value = curp; // Asegurar mayúsculas
        //         manejarCambios(); // Revalidar cambios
        //     });

        //     // Agregar evento para detectar cambios en cualquier campo
        //     inputs.forEach(input => {
        //         input.addEventListener('input', manejarCambios);
        //     });

        //     // Mostrar siempre los campos de tutor al cargar el modal
        //     function cargarCampos() {
        //         tutorFields.style.display = 'block';
        //     }

        //     // Validación final al enviar el formulario
        //     form.addEventListener('submit', function(event) {
        //         if (curpInput.value.length === 18 && !regex.test(curpInput.value)) {
        //             event.preventDefault();
        //             alert('Por favor, introduce un CURP válido.');
        //         }
        //     });

        //     // Llamar a cargarCampos y guardar valores iniciales al abrir el modal
        //     cargarCampos();
        //     guardarValoresIniciales();
        // });



        // $('#editAlumnoForm').submit(function(event) {
        //     event.preventDefault(); // Evita que el formulario se envíe de inmediato

        //     // Obtener los datos del formulario
        //     var formData = $(this).serialize();

        //     // Mostrar los datos en la consola
        //     console.log('Datos enviados al servidor:', formData);

        //     // Hacer la solicitud AJAX (si estás usando AJAX)
        //     $.ajax({
        //         url: $(this).attr('action'), // Usar la URL definida en el atributo action del formulario
        //         method: 'POST', // El método HTTP
        //         data: formData, // Los datos del formulario
        //         success: function(response) {
        //             console.log('Respuesta del servidor:', response);
        //             // Aquí puedes manejar la respuesta del servidor, como mostrar un mensaje de éxito
        //         },
        //         error: function(xhr, status, error) {
        //             console.log('Error:', error); // Mostrar el error si ocurre algo
        //         }
        //     });
        // });


        // document.addEventListener('DOMContentLoaded', function () {
        //     const curpInput = document.getElementById('curp_2');
        //     const submitBtn = document.getElementById('submitBtnEdit');
        //     const tutorFields = document.getElementById('tutorFieldsEditar');
        //     const nombretutor = document.getElementById('nombretutor_2');
        //     const telefonotutor = document.getElementById('telefonotutor_2');
        //     const form = document.getElementById('editAlumnoForm');

        //     // Expresión regular para validar CURP
        //     const regex = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{2}$/;

        //     // Cargar todos los campos al abrir el modal
        //     function cargarCampos() {
        //         tutorFields.style.display = 'block'; // Mostrar siempre los campos de tutor
        //     }

        //     // Validar CURP al modificarlo
        //     curpInput.addEventListener('input', function () {
        //         const curp = this.value.toUpperCase();
        //         this.value = curp; // Asegurar mayúsculas
        //         submitBtn.disabled = true; // Deshabilitar botón por defecto

        //         if (curp.length === 18 && regex.test(curp)) {
        //             submitBtn.disabled = false; // Habilitar botón
        //         }
        //     });

        //     // Asegurar que siempre se carguen los campos al abrir el modal
        //     document.getElementById('editAlumnoForm').addEventListener('reset', cargarCampos);

        //     // Validación final al enviar el formulario
        //     form.addEventListener('submit', function (event) {
        //         if (!regex.test(curpInput.value)) {
        //             event.preventDefault();
        //             alert('Por favor, introduce un CURP válido.');
        //         }
        //     });

        //     // Llamar a cargarCampos al cargar el modal
        //     cargarCampos();
        // });
    </script> --}}

    {{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        const curpInput = document.getElementById('curp');
        const curpError = document.createElement('span');
        curpError.id = 'curpError';
        curpError.style.color = 'red';
        curpError.style.display = 'none';
        curpError.textContent = 'El CURP no es válido.';
        curpInput.parentNode.appendChild(curpError);

        curpInput.addEventListener('input', function () {
            const curp = this.value.toUpperCase();
            const regex = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{2}$/;


            console.log("Longitud del CURP ingresado:", curp.length);

            console.log("CURP ingresado:", curp); // Debug
            console.log("¿Pasa la regex?", regex.test(curp)); // Debug

            if (curp.length === 18 && !regex.test(curp)) {
                curpError.style.display = 'inline';
            } else {
                curpError.style.display = 'none';
            }
        });
    });
    </script> --}}

    {{-- TABLA DE ALUMNOS ACTIVOS --}}
    <script>
        // $(document).ready(function() {
        var table = $('#tblalumnosact').DataTable({
            scrollX: true,
            "lengthMenu": [
                [15, 20, 50, -1],
                [15, 20, 50, "Todos"]
            ],
            language: {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            ajax: {
                url: '/ajaxConsulta/Alumnos/act',
                dataSrc: 'data'
            },
            columns: [{
                    data: 'nombreCompleto'
                },
                {
                    data: 'curp'
                },
                {
                    data: 'telefono'
                },
                {
                    data: 'direccion'
                },
                {
                    data: 'nombretutor'
                },
                {
                    data: 'telefonotutor'
                },
                {
                    data: 'estatus',
                    render: function(data, type, row) {
                        return data == 1 ? 'Activo' : 'Inactivo';
                    }
                },
                {
                    data: null
                },
            ],
            columnDefs: [{
                targets: 7,
                orderable: false,
                render: function(data, type, row) {
                    return '<button onclick="buscarAlumno(' + data.idalumno +
                        ');" type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editAlumno"><i class="bi bi-pencil-square"></i></button>' +
                        '<a href="/upAlumno/' + row.idalumno +
                        '/baja" class="btn btn-danger me-2" onclick="return confirm(\'¿Estás seguro de dar de baja el alumno: ' +
                        row.name + '?\')" ><i class="bi bi-chevron-bar-down"></i></a>';
                    // <a class = 'btn btn-success btn-sm ms-2' 
                    // href = '{{ url('/upFactor/ + row.id_factor + /baja') }}'
                    // onclick = "return confirm('¿Estás seguro de dar de alta el factor?')" >
                    //     Alta </a>
                }
            }, ],
        });
        // });

        function buscarAlumno(idalumno) {
            //alert('Holis: ' + id);
            // Hacer solicitud AJAX para obtener los datos personales
            $.ajax({
                url: "/ajaxConsulta/edit/" + idalumno + "/alumno",
                // url: /datospersonales/${idDato}/edit,
                type: 'GET',
                success: function(data) {
                    // console.log(data);
                    document.getElementById("idalumno_2").value = data.idalumno;
                    document.getElementById("name_2").value = data.name;
                    document.getElementById("apellidop_2").value = data.apellidop;
                    document.getElementById("apellidom_2").value = data.apellidom;
                    document.getElementById("curp_2").value = data.curp;
                    document.getElementById("direccion_2").value = data.direccion;
                    document.getElementById("telefono_2").value = data.telefono;
                    document.getElementById("nombretutor_2").value = data.nombretutor;
                    document.getElementById("telefonotutor_2").value = data.telefonotutor;

                    // Rellenar los campos del formulario con los datos obtenidos
                    // $('#name_2').val(data.name);
                    // document.getElementById("codigoConceptoEditar").value = data.codigoConcepto;
                    // document.getElementById("importeConceptoEditar").value = data.importeConcepto;

                    // Mostrar siempre los campos de tutor
                    document.getElementById("tutorFieldsEditar").style.display = 'block';

                    // Habilitar el botón si hay cambios en los campos
                    configurarValidaciones();
                    // Mostrar el modal
                    $('#editAlumno').modal('show');
                },
                error: function() {
                    alert('Error al cargar los datos del alumno.');
                }
            });
        }

        function configurarValidaciones() {
            const form = document.getElementById('editAlumnoForm');
            const submitBtn = document.getElementById('submitBtnEdit');
            const inputs = form.querySelectorAll('input');

            // Guardar los valores iniciales
            const valoresIniciales = {};
            inputs.forEach(input => {
                valoresIniciales[input.id] = input.value;
            });

            // Habilitar el botón si se detecta un cambio
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    let hayCambios = false;
                    inputs.forEach(i => {
                        if (i.value !== valoresIniciales[i.id]) {
                            hayCambios = true;
                        }
                    });
                    submitBtn.disabled = !hayCambios;
                });
            });
        }
    </script>

    {{-- TABLA DE ALUMNOS INACTIVOS --}}
    <script>
        // $(document).ready(function() {
        var table = $('#tblalumnosina').DataTable({
            scrollX: true,
            "lengthMenu": [
                [15, 20, 50, -1],
                [15, 20, 50, "Todos"]
            ],
            language: {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            ajax: {
                url: '/ajaxConsulta/Alumnos/ina',
                dataSrc: 'data'
            },
            columns: [{
                    data: 'nombreCompleto'
                },
                {
                    data: 'curp'
                },
                {
                    data: 'telefono'
                },
                {
                    data: 'direccion'
                },
                {
                    data: 'nombretutor'
                },
                {
                    data: 'telefonotutor'
                },
                {
                    data: 'estatus',
                    render: function(data, type, row) {
                        return data == 1 ? 'Activo' : 'Inactivo';
                    }
                },
                {
                    data: null
                },
            ],
            columnDefs: [{
                targets: 7,
                orderable: false,
                render: function(data, type, row) {
                    return '<a href="/upAlumno/' + row.idalumno +
                        '/alta" class="btn btn-success me-2" onclick="return confirm(\'¿Estás seguro de dar de alta el alumno: ' +
                        row.name + '?\')" ><i class="bi bi-chevron-bar-up"></i></a>';
                    // <a class = 'btn btn-success btn-sm ms-2' 
                    // href = '{{ url('/upFactor/ + row.id_factor + /baja') }}'
                    // onclick = "return confirm('¿Estás seguro de dar de alta el factor?')" >
                    //     Alta </a>
                }
            }, ],
        });
        // });
    </script>

@endsection
