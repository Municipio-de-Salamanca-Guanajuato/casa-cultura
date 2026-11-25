<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Metadatos básicos -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}" />

    <title>Casa de la Cultura | Acceso</title>

    <!-- Fuente: Montserrat (estilo profesional y legible) -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Estilos base del layout (aislados bajo .auth-page para no afectar otras vistas) -->
    <style>
        :root {
            /* Paleta neutra con color de acento */
            --color-bg: #f5f7fb;
            --color-surface: #ffffff;
            --color-text: #1f2937;
            --color-muted: #6b7280;
            --color-accent: #2563eb;
            /* azul profesional */
            --radius: 12px;
            --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.08);
            --shadow-sm: 0 8px 18px rgba(0, 0, 0, 0.06);
        }

        /* Reset mínimo y tipografía */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            /* Pila sans-serif del sistema para mejor rendimiento y consistencia */
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji", sans-serif;
            color: var(--color-text);
            background: var(--color-bg);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /*
         |------------------------------------------------------------
         | Estilos específicos para la página de autenticación
         | Se aplican sólo si la vista envuelve su contenido en .auth-page
         |------------------------------------------------------------
        */
        .auth-page {
            min-height: 100vh;
        }

        /* Grid principal de 2 columnas (formulario + imagen) */
        .auth-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }

        /* Columna izquierda: panel/formulario */
        .auth-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 32px;
            background: #ffffff;
        }

        /* Contenedor del formulario para ancho controlado */
        .auth-card {
            background: var(--color-surface);
            width: 100%;
            max-width: 660px;
            border-radius: var(--radius);
            /* box-shadow: var(--shadow-lg); */
            padding: 32px;
        }

        .auth-header {
            margin-bottom: 24px;
            text-align: center;
        }

        .auth-title {
            margin: 0 0 8px 0;
            font-weight: 600;
            font-size: 1.10rem;
        }

        .auth-subtitle {
            margin: 0;
            color: var(--color-muted);
            font-weight: 500;
            font-size: 0.95rem;
        }

        /* Insignia/etiqueta pequeña sobre el título */
        .auth-badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            background: #eef2ff;
            color: var(--color-accent);
            font-weight: 700;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
            margin-bottom: 10px;
        }

        /* Logo dentro del formulario de acceso */
        .auth-logo {
            height: 80px;
            width: auto;
            display: block;
            margin: 0 auto 12px;
        }

        /* Campos de formulario */
        .form-field {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .input-control {
            width: 100%;
            appearance: none;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 0.95rem;
            transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        .input-control:focus {
            outline: none;
            background: #fff;
            border-color: #952E48;
            box-shadow: 0 0 0 3px #7f1d1d;
        }

        /* Etiquetas flotantes (label integrado dentro del input) */
        .form-field--float {
            position: relative;
        }

        .input-control--float {
            padding: 16px 14px 10px 14px;
        }

        .float-label {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-muted);
            background: transparent;
            padding: 0 4px;
            pointer-events: none;
            transition: all 0.18s ease;
        }

        .input-control--float:focus+.float-label,
        .input-control--float:not(:placeholder-shown)+.float-label {
            top: -8px;
            background: var(--color-surface);
            font-size: 0.78rem;
            color: #000000;
        }

        /* Sufijo dentro del input (botón mostrar/ocultar) */
        .input-has-suffix {
            padding-right: 44px;
        }

        .input-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: var(--color-muted);
            cursor: pointer;
            font-weight: 700;
        }

        .input-toggle:focus {
            outline: none;
            color: var(--color-accent);
        }

        .btn-primary-login {
            display: inline-block;
            width: 100%;
            background: #952E48;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px 16px;
            font-weight: 700;
            letter-spacing: 0.2px;
            cursor: pointer;
            box-shadow: var(--shadow-sm);
            transition: transform 0.05s ease, filter 0.2s ease, box-shadow 0.2s ease;
        }

        .caps-hint {
            display: none;
            color: var(--color-muted);
            font-size: 0.8rem;
            margin-top: 6px;
        }

        /* Fila recordarme + link */
        .auth-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 8px 0 16px 0;
        }

        .checkbox {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox input {
            width: 18px;
            height: 18px;
        }

        .link {
            color: var(--color-accent);
            text-decoration: none;
            font-weight: 600;
        }

        .link:hover {
            text-decoration: underline;
        }

        /* Botón principal */
        .btn-primary {
            display: inline-block;
            width: 100%;
            background: var(--color-accent);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px 16px;
            font-weight: 700;
            letter-spacing: 0.2px;
            cursor: pointer;
            box-shadow: var(--shadow-sm);
            transition: transform 0.05s ease, filter 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-primary:hover {
            filter: brightness(0.95);
        }

        .btn-primary:active {
            transform: translateY(1px);
        }

        /* Mensajes */
        .alert {
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 0.95rem;
            margin-bottom: 12px;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background: #fef2f2;
            color: #7f1d1d;
            border: 1px solid #fecaca;
        }

        /* Columna derecha: imagen a altura completa */
        .auth-illustration {
            min-height: 100vh;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Responsivo: en móvil la imagen va arriba */
        @media (max-width: 992px) {
            .auth-grid {
                grid-template-columns: 1fr;
            }

            .auth-illustration {
                order: -1;
                min-height: 38vh;
            }

            .auth-panel {
                padding: 28px 20px;
            }

            .auth-card {
                padding: 24px;
            }
        }

        /* En pantallas pequeñas, ocultar por completo la columna de imagen */
        @media (max-width: 768px) {
            .auth-illustration {
                display: none;
            }

            .auth-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <!-- Vite (si tu app lo usa) -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div id="app">
        @yield('content')
    </div>

    <!-- Sección para scripts específicos de cada vista -->
    @yield('scripts')
</body>

</html>
