@extends('layouts.login')

@section('content')
    <!--
        Estructura general del login:
        - Grid de 2 columnas:
            1) Izquierda: formulario de acceso
            2) Derecha: imagen a altura completa
        - En móvil, la imagen se coloca arriba del formulario automáticamente (media query en el layout)
    -->
    <div class="auth-page">
        <div class="auth-grid">
            <!-- Columna izquierda: panel con el formulario -->
            <div class="auth-panel">
                <div class="auth-card">
                    <div class="auth-header">
                        <!-- <span class="auth-badge">Iniciar sesión</span> -->
                        <img src="{{ asset('images/escudoArma.png') }}" alt="Escudo de Salamanca" class="auth-logo">
                        <h1 class="auth-title">SISTEMA DE CASA DE LA CULTURA</h1>
                        <p class="auth-subtitle">Accede al sistema de Casa de la Cultura de Salamanca, Guanajuato
                        </p>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">{{ session('status') }}</div>
                    @endif

                    @error('email')
                        <div class="alert alert-danger" role="alert">{{ $message }}</div>
                    @enderror

                    @error('password')
                        <div class="alert alert-danger" role="alert">{{ $message }}</div>
                    @enderror

                    <form id="loginForm" method="POST" action="{{ route('login') }}" novalidate>
                        @csrf

                        <!-- Campo: Email o Usuario (label integrado) -->
                        <div class="form-field form-field--float">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autocomplete="email" autofocus class="input-control input-control--float" placeholder=" "
                                aria-describedby="emailError" aria-invalid="false">
                            <label for="email" class="float-label">Correo electrónico</label>
                            <small id="emailError" class="error-text" role="alert" style="color:#dc2626"></small>
                        </div>

                        <!-- Campo: Contraseña (label integrado) -->
                        <div class="form-field form-field--float">
                            <input id="password" type="password" name="password" required minlength="6"
                                autocomplete="current-password" class="input-control input-control--float input-has-suffix"
                                placeholder=" " aria-describedby="passwordError capsHint" aria-invalid="false">
                            <label for="password" class="float-label">Contraseña</label>
                            <button type="button" id="togglePassword" class="input-toggle"
                                aria-label="Mostrar u ocultar contraseña">Ver</button>
                            <small id="passwordError" class="error-text" role="alert" style="color:#dc2626"></small>
                            <div id="capsHint" class="caps-hint">Bloq Mayús activado</div>
                        </div>

                        <!-- Fila: Recordarme y ¿Olvidaste tu contraseña? -->

                        <!-- reCAPTCHA -->
                        <div class="form-field">
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"
                                data-callback="onRecaptchaSuccess" data-expired-callback="onRecaptchaExpired"
                                data-error-callback="onRecaptchaExpired"></div>
                            <small id="recaptchaError" class="error-text" role="alert" style="color:#dc2626">
                                @error('g-recaptcha-response')
                                    {{ $message }}
                                @enderror
                            </small>
                        </div>

                        <!-- Botón de envío (deshabilitado hasta marcar el reCAPTCHA) -->
                        <div class="form-field" style="margin-top: 8px;">
                            <button type="submit" id="loginBtn" class="btn-primary-login" disabled
                                style="opacity:.55;cursor:not-allowed;">Iniciar sesión</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Columna derecha: ilustración a pantalla completa -->
            <div class="auth-illustration" style="background-image: url('{{ asset('images/cultura.jpg') }}');"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <!--
                                                                                                                                                                                            Espacio para JS adicional del login (validaciones, toggles de contraseña, etc.).
                                                                                                                                                                                            Mantener este bloque para futuras ampliaciones sin modificar el layout.
                                                                                                                                                                                        -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        // Callbacks del reCAPTCHA: habilitan/deshabilitan el botón de inicio de sesión
        function setLoginEnabled(enabled) {
            const btn = document.getElementById('loginBtn');
            if (!btn) return;
            btn.disabled = !enabled;
            btn.style.opacity = enabled ? '' : '.55';
            btn.style.cursor = enabled ? '' : 'not-allowed';
        }

        function onRecaptchaSuccess() {
            document.getElementById('recaptchaError').textContent = '';
            setLoginEnabled(true);
        }

        function onRecaptchaExpired() {
            setLoginEnabled(false);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            if (!form) return;

            const emailInput = document.getElementById('email');
            const passInput = document.getElementById('password');
            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');
            const togglePassword = document.getElementById('togglePassword');
            const capsHint = document.getElementById('capsHint');

            function setError(input, errorEl, message) {
                errorEl.textContent = message || '';
                input.setAttribute('aria-invalid', message ? 'true' : 'false');
                input.style.borderColor = message ? '#ef4444' : '#e5e7eb';
            }

            function validateEmail() {
                const value = emailInput.value.trim();
                if (!value) {
                    setError(emailInput, emailError, 'El correo es obligatorio.');
                    return false;
                }
                // Usa la validación nativa del navegador para tipo email
                if (!emailInput.checkValidity()) {
                    setError(emailInput, emailError, 'Ingresa un correo válido.');
                    return false;
                }
                setError(emailInput, emailError, '');
                return true;
            }

            function validatePassword() {
                const value = passInput.value;
                if (!value) {
                    setError(passInput, passwordError, 'La contraseña es obligatoria.');
                    return false;
                }
                if (value.length < 6) {
                    setError(passInput, passwordError, 'La contraseña debe tener al menos 6 caracteres.');
                    return false;
                }
                setError(passInput, passwordError, '');
                return true;
            }

            emailInput.addEventListener('input', validateEmail);
            passInput.addEventListener('input', validatePassword);

            // Toggle mostrar/ocultar contraseña
            togglePassword?.addEventListener('click', function() {
                const isPassword = passInput.getAttribute('type') === 'password';
                passInput.setAttribute('type', isPassword ? 'text' : 'password');
                togglePassword.textContent = isPassword ? 'Ocultar' : 'Ver';
            });

            // Aviso de Bloq Mayús
            passInput.addEventListener('keydown', function(e) {
                const caps = e.getModifierState && e.getModifierState('CapsLock');
                capsHint.style.display = caps ? 'block' : 'none';
            });
            passInput.addEventListener('blur', function() {
                capsHint.style.display = 'none';
            });

            form.addEventListener('submit', function(e) {
                const okEmail = validateEmail();
                const okPass = validatePassword();
                if (!okEmail || !okPass) {
                    e.preventDefault();
                    (okEmail ? passInput : emailInput).focus();
                    return;
                }

                // Respaldo en el navegador (la validación real se hace en el servidor)
                const captchaOk = typeof grecaptcha !== 'undefined' && grecaptcha.getResponse().length > 0;
                if (!captchaOk) {
                    e.preventDefault();
                    document.getElementById('recaptchaError').textContent =
                        'Por favor confirma que no eres un robot.';
                }
            });
        });
    </script>
@endsection
