@extends('layouts.default')

@section('content')
<section class="auth-page">
    <div class="auth-hero" style="background-image: linear-gradient(120deg, rgba(70, 50, 138, 0.92), rgba(21, 21, 21, 0.58)), url('{{ asset('images/bg-1.jpg') }}');">
        <div class="container">
            <nav class="auth-breadcrumbs" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Inicio</a>
                <span>/</span>
                <strong>Iniciar Sesión</strong>
            </nav>

            <div class="auth-hero-content">
                <div class="auth-hero-text">
                    <h1 class="auth-hero-title">Iniciar Sesión</h1>
                    <p class="auth-hero-meta">
                        Accede a tu cuenta de Restaurante San Felipe para realizar pedidos, consultar tu historial y disfrutar de nuestras mejores especialidades.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-body">
        <div class="container">
            <div class="auth-container">
                <div class="auth-card">
                    <header class="auth-card__header">
                        <div class="auth-badge">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h2 class="auth-card__title">¡Bienvenido de nuevo!</h2>
                        <p class="auth-card__subtitle">Ingresa tus credenciales para continuar</p>
                    </header>

                    @if (session('status'))
                        <div class="auth-alert auth-alert--success" role="alert">
                            <div class="auth-alert__icon"><i class="fas fa-check-circle"></i></div>
                            <div class="auth-alert__content">
                                {{ session('status') }}
                            </div>
                        </div>
                    @endif

                    @php
                        $cartItemsCount = \App\Services\CartPricing::summarize()['items_count'] ?? 0;
                    @endphp

                    @if ($cartItemsCount > 0 && session('url.intended') && str_contains(session('url.intended'), 'checkout'))
                        <div class="auth-alert auth-alert--info" style="background: #f0f4ff; border: 1px solid #d0deff; color: #2c5282;" role="alert">
                            <div class="auth-alert__icon" style="color: var(--sf-purple, #6046b6); font-size: 16px; margin-top: 1px;"><i class="fas fa-shopping-bag"></i></div>
                            <div class="auth-alert__content">
                                <strong>¡Tus platillos te esperan en el carrito!</strong>
                                <p style="margin: 2px 0 0; font-size: 12.5px;">Inicia sesión para continuar al pago de tu pedido.</p>
                            </div>
                        </div>
                    @elseif ($cartItemsCount === 0 && session('url.intended') && str_contains(session('url.intended'), 'checkout'))
                        @php
                            session()->forget('url.intended');
                        @endphp
                    @endif

                    @if ($errors->any())
                        <div class="auth-alert auth-alert--danger" role="alert">
                            <div class="auth-alert__icon"><i class="fas fa-exclamation-circle"></i></div>
                            <div class="auth-alert__content">
                                <strong>No pudimos iniciar sesión:</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="auth-form" id="loginForm">
                        @csrf

                        <div class="auth-field">
                            <label class="auth-label" for="email">Correo Electrónico*</label>
                            <div class="auth-input-wrap @error('email') has-error @enderror">
                                <span class="auth-input-icon">
                                    <i class="far fa-envelope"></i>
                                </span>
                                <input
                                    id="email"
                                    type="email"
                                    class="auth-input"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="ejemplo@correo.com"
                                    required
                                    autocomplete="email"
                                    autofocus
                                >
                            </div>
                            @error('email')
                                <span class="auth-field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="auth-field">
                            <div class="auth-label-row">
                                <label class="auth-label" for="password">Contraseña*</label>
                                @if (Route::has('password.request'))
                                    <a class="auth-forgot-link" href="{{ route('password.request') }}">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                @endif
                            </div>
                            <div class="auth-input-wrap @error('password') has-error @enderror">
                                <span class="auth-input-icon">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input
                                    id="password"
                                    type="password"
                                    class="auth-input"
                                    name="password"
                                    placeholder="Tu contraseña secreta"
                                    required
                                    autocomplete="current-password"
                                >
                                <button
                                    type="button"
                                    class="auth-toggle-pwd"
                                    id="togglePassword"
                                    title="Mostrar u ocultar contraseña"
                                    aria-label="Mostrar u ocultar contraseña"
                                >
                                    <i class="far fa-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            <div id="capsLockWarning" class="auth-caps-warning" style="display: none;">
                                <i class="fas fa-arrow-up"></i>
                                <span>Bloq Mayús está activado</span>
                            </div>
                            @error('password')
                                <span class="auth-field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="auth-field-row">
                            <label class="auth-checkbox-label" for="remember">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    id="remember"
                                    class="auth-checkbox"
                                    {{ old('remember') ? 'checked' : '' }}
                                >
                                <span class="auth-checkbox-custom"></span>
                                <span class="auth-checkbox-text">Recordar mi sesión</span>
                            </label>
                        </div>

                        <button type="submit" class="auth-submit-btn" id="loginSubmitBtn">
                            <span id="btnText">Iniciar Sesión</span>
                            <i class="fas fa-arrow-right" id="btnIcon"></i>
                        </button>
                    </form>

                    <footer class="auth-card__footer">
                        <p class="auth-register-prompt">
                            ¿Aún no tienes una cuenta?
                            <a href="{{ route('register') }}" class="auth-register-link">Regístrate aquí</a>
                        </p>
                    </footer>

                    <div class="auth-features">
                        <div class="auth-feature-item">
                            <i class="fas fa-shield-alt"></i>
                            <span>Acceso seguro</span>
                        </div>
                        <div class="auth-feature-item">
                            <i class="fas fa-bolt"></i>
                            <span>Pedidos ágiles</span>
                        </div>
                        <div class="auth-feature-item">
                            <i class="fas fa-receipt"></i>
                            <span>Historial de órdenes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var pwdInput = document.getElementById("password");
        var toggleBtn = document.getElementById("togglePassword");
        var toggleIcon = document.getElementById("togglePasswordIcon");
        var capsWarning = document.getElementById("capsLockWarning");
        var loginForm = document.getElementById("loginForm");
        var submitBtn = document.getElementById("loginSubmitBtn");
        var btnText = document.getElementById("btnText");
        var btnIcon = document.getElementById("btnIcon");

        // Toggle password visibility
        if (toggleBtn && pwdInput) {
            toggleBtn.addEventListener("click", function () {
                var isPassword = pwdInput.type === "password";
                pwdInput.type = isPassword ? "text" : "password";
                if (toggleIcon) {
                    toggleIcon.className = isPassword ? "far fa-eye-slash" : "far fa-eye";
                }
            });
        }

        // Caps Lock detection
        if (pwdInput && capsWarning) {
            function checkCapsLock(event) {
                if (event.getModifierState && event.getModifierState("CapsLock")) {
                    capsWarning.style.display = "inline-flex";
                } else {
                    capsWarning.style.display = "none";
                }
            }

            pwdInput.addEventListener("keydown", checkCapsLock);
            pwdInput.addEventListener("keyup", checkCapsLock);
            pwdInput.addEventListener("focus", checkCapsLock);
            pwdInput.addEventListener("blur", function () {
                capsWarning.style.display = "none";
            });
        }

        // Loading state on form submit
        if (loginForm && submitBtn) {
            loginForm.addEventListener("submit", function () {
                submitBtn.disabled = true;
                if (btnText) btnText.textContent = "Iniciando sesión...";
                if (btnIcon) btnIcon.className = "fas fa-spinner fa-spin";
            });
        }
    });
</script>
@endsection
