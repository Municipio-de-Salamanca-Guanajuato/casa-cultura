<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}" />

    {{-- BOOTSTRAP CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    {{-- SELECT2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    {{-- ICONOS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- DATATABLES CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.bootstrap5.min.css">

    {{-- STYLES PERSONALIZADOS --}}
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />

    {{-- FONT AWESOME --}}
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @yield('styles')
</head>

<body class="sb-nav-fixed">
    @include('layouts.nav') {{-- Navbar --}}

    <div class="container mt-4">
        <div id="layoutSidenav_content">
            {{-- MENSAJE DE EXITO --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="alert" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i> <!-- Ícono de tacha simple -->
                    </button>
                </div>
            @endif

            {{-- MENSAJE DE ADVERTENCIA --}}
            @if (session('status'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn btn-outline-warning" data-bs-dismiss="alert" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i> <!-- Ícono de tacha simple -->
                    </button>
                </div>
            @endif

            {{-- MENSAJE DE ERROR --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="alert" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i> <!-- Ícono de tacha simple -->
                    </button>
                </div>
            @endif
            <main>
                @yield('content') {{-- Contenido principal --}}
            </main>
            {{-- Modales --}}
            @include('modals.password.editpass')
        </div>
    </div>
    </div>

    <script>
        document.getElementById("editPasswordForm").addEventListener("submit", function(event) {
            let pass1 = document.getElementById("passwnew").value;
            let pass2 = document.getElementById("passwnewup").value;
            let errorNew = document.getElementById("passnewError");
            let errorConfirm = document.getElementById("passnewupError");
            let errorGeneral = document.getElementById("passwordError");

            // Reiniciar mensajes de error y ocultarlos
            errorNew.textContent = "";
            errorConfirm.textContent = "";
            errorGeneral.textContent = "";
            errorNew.style.display = "none";
            errorConfirm.style.display = "none";
            errorGeneral.style.display = "none";

            let isValid = true;

            // Validar mínimo 8 caracteres
            if (pass1.length < 8) {
                errorNew.textContent = "La contraseña debe tener mínimo 8 caracteres.";
                errorNew.style.display = "block"; // Mostrar mensaje
                isValid = false;
            }

            if (pass2.length < 8) {
                errorConfirm.textContent = "La confirmación de contraseña debe tener mínimo 8 caracteres.";
                errorConfirm.style.display = "block"; // Mostrar mensaje
                isValid = false;
            }

            // Validar que las contraseñas coincidan
            if (pass1 !== pass2) {
                errorGeneral.textContent = "Las contraseñas no coinciden.";
                errorGeneral.style.display = "block"; // Mostrar mensaje
                isValid = false;
            }

            // Si hay errores, evitar el envío del formulario
            if (!isValid) {
                event.preventDefault();
            }
        });
    </script>

    {{-- JQUERY --}}
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>

    {{-- SELECT2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- BOOTSTRAP JS --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script> --}}

    {{-- DATATABLES JS --}}
    {{-- <script src="https://cdn.datatables.net/2.1.8/js/jquery.dataTables.min.js"></script> --}}
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.bootstrap5.min.js"></script>

    {{-- SCRIPTS PERSONALIZADOS --}}
    <script src="{{ asset('js/scripts.js') }}"></script>

    {{-- CONFIGURACIÓN DE TOOLTIP --}}
    <script>
        // Espera a que el DOM esté completamente cargado
        document.addEventListener('DOMContentLoaded', function() {
            // Detecta el clic en el botón para mostrar/ocultar la barra lateral
            document.querySelector('#sidebarToggle').addEventListener('click', function() {
                setTimeout(function() {
                    $.fn.dataTable.tables({
                        visible: true,
                        api: true
                    }).columns.adjust().draw();
                    // if (typeof table !== 'undefined') {
                    //     table.columns.adjust().draw(); // Ajusta y redibuja la tabla si existe
                    // }
                }, 300); // Da tiempo para que termine la animación
            });
            // Configuración global para ocultar mensajes flash después de un tiempo
            const alertElement = document.querySelector('.alert');
            if (alertElement) {
                setTimeout(() => {
                    alertElement.classList.add('fade-out'); // Agregar clase para ocultar con animación
                }, 5000); // Cambiar a 5000ms (5 segundos) o el tiempo deseado
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    @yield('scripts')
    {{-- <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script> --}}

</body>

</html>
