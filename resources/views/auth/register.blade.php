@extends('layouts.default')

@section('content')
<section class="auth-page">
    <div class="auth-hero" style="background-image: linear-gradient(120deg, rgba(70, 50, 138, 0.92), rgba(21, 21, 21, 0.58)), url('{{ asset('images/bg-1.jpg') }}');">
        <div class="container">
            <nav class="auth-breadcrumbs" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Inicio</a>
                <span>/</span>
                <strong>Registrarse</strong>
            </nav>

            <div class="auth-hero-content">
                <div class="auth-hero-text">
                    <h1 class="auth-hero-title">Crear Cuenta</h1>
                    <p class="auth-hero-meta">
                        Únete a Restaurante San Felipe y disfruta de una experiencia gastronómica inigualable con pedidos directos a tu mesa o a tu puerta.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-body">
        <div class="container">
            <div class="auth-container auth-container--wide">
                <div class="auth-card">
                    <header class="auth-card__header">
                        <div class="auth-badge">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h2 class="auth-card__title">Crea tu cuenta</h2>
                        <p class="auth-card__subtitle">Completa tus datos para empezar a ordenar</p>
                    </header>

                    @php
                        $cartItemsCount = \App\Services\CartPricing::summarize()['items_count'] ?? 0;
                    @endphp

                    @if ($cartItemsCount > 0 && session('url.intended') && str_contains(session('url.intended'), 'checkout'))
                        <div class="auth-alert auth-alert--info" style="background: #f0f4ff; border: 1px solid #d0deff; color: #2c5282;" role="alert">
                            <div class="auth-alert__icon" style="color: var(--sf-purple, #6046b6); font-size: 16px; margin-top: 1px;"><i class="fas fa-shopping-bag"></i></div>
                            <div class="auth-alert__content">
                                <strong>¡Tus platillos te esperan en el carrito!</strong>
                                <p style="margin: 2px 0 0; font-size: 12.5px;">Crea tu cuenta para continuar directamente al pago de tu pedido.</p>
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
                                <strong>Por favor revisa los siguientes campos:</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="auth-form" id="registerForm">
                        @csrf

                        <div class="auth-grid-2">
                            {{-- Nombre --}}
                            <div class="auth-field">
                                <label class="auth-label" for="name">Nombre*</label>
                                <div class="auth-input-wrap @error('name') has-error @enderror">
                                    <span class="auth-input-icon">
                                        <i class="far fa-user"></i>
                                    </span>
                                    <input
                                        id="name"
                                        type="text"
                                        class="auth-input"
                                        name="name"
                                        value="{{ old('name') }}"
                                        placeholder="Tu nombre"
                                        required
                                        autocomplete="given-name"
                                        autofocus
                                    >
                                </div>
                                @error('name')
                                    <span class="auth-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Apellido --}}
                            <div class="auth-field">
                                <label class="auth-label" for="last_name">Apellido*</label>
                                <div class="auth-input-wrap @error('last_name') has-error @enderror">
                                    <span class="auth-input-icon">
                                        <i class="far fa-user"></i>
                                    </span>
                                    <input
                                        id="last_name"
                                        type="text"
                                        class="auth-input"
                                        name="last_name"
                                        value="{{ old('last_name') }}"
                                        placeholder="Tus apellidos"
                                        required
                                        autocomplete="family-name"
                                    >
                                </div>
                                @error('last_name')
                                    <span class="auth-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Correo Electrónico --}}
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
                                    >
                                </div>
                                @error('email')
                                    <span class="auth-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Teléfono --}}
                            <div class="auth-field">
                                <label class="auth-label" for="phone">Teléfono*</label>
                                <div class="auth-input-wrap @error('phone') has-error @enderror">
                                    <span class="auth-input-icon">
                                        <i class="fas fa-phone-alt"></i>
                                    </span>
                                    <input
                                        id="phone"
                                        type="tel"
                                        class="auth-input"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        placeholder="Ej. 961 123 4567"
                                        required
                                        autocomplete="tel"
                                    >
                                </div>
                                @error('phone')
                                    <span class="auth-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Dirección --}}
                            <div class="auth-field auth-field--full">
                                <label class="auth-label" for="address">Dirección de entrega*</label>
                                <div class="auth-input-wrap @error('address') has-error @enderror">
                                    <span class="auth-input-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <input
                                        id="address"
                                        type="text"
                                        class="auth-input"
                                        name="address"
                                        value="{{ old('address') }}"
                                        placeholder="Calle, número, colonia o referencias (ej. Segunda Sur Ote.)"
                                        required
                                        autocomplete="street-address"
                                    >
                                </div>
                                <span class="auth-hint">Usaremos esta dirección para tus pedidos a domicilio</span>
                                @error('address')
                                    <span class="auth-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Contraseña --}}
                            <div class="auth-field">
                                <label class="auth-label" for="password">Contraseña*</label>
                                <div class="auth-input-wrap @error('password') has-error @enderror">
                                    <span class="auth-input-icon">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input
                                        id="password"
                                        type="password"
                                        class="auth-input"
                                        name="password"
                                        placeholder="Mínimo 8 caracteres"
                                        required
                                        autocomplete="new-password"
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
                                @error('password')
                                    <span class="auth-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Confirmar Contraseña --}}
                            <div class="auth-field">
                                <label class="auth-label" for="password_confirmation">Confirmar Contraseña*</label>
                                <div class="auth-input-wrap">
                                    <span class="auth-input-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </span>
                                    <input
                                        id="password_confirmation"
                                        type="password"
                                        class="auth-input"
                                        name="password_confirmation"
                                        placeholder="Repite tu contraseña"
                                        required
                                        autocomplete="new-password"
                                    >
                                    <button
                                        type="button"
                                        class="auth-toggle-pwd"
                                        id="togglePasswordConfirm"
                                        title="Mostrar u ocultar contraseña"
                                        aria-label="Mostrar u ocultar contraseña"
                                    >
                                        <i class="far fa-eye" id="togglePasswordConfirmIcon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="capsLockWarning" class="auth-caps-warning" style="display: none;">
                            <i class="fas fa-arrow-up"></i>
                            <span>Bloq Mayús está activado</span>
                        </div>

                        <button type="submit" class="auth-submit-btn" id="registerSubmitBtn">
                            <span id="regBtnText">Crear Mi Cuenta</span>
                            <i class="fas fa-arrow-right" id="regBtnIcon"></i>
                        </button>
                    </form>

                    <footer class="auth-card__footer">
                        <p class="auth-register-prompt">
                            ¿Ya tienes una cuenta registrada?
                            <a href="{{ route('login') }}" class="auth-register-link">Inicia sesión aquí</a>
                        </p>
                    </footer>

                    <div class="auth-features">
                        <div class="auth-feature-item">
                            <i class="fas fa-bolt"></i>
                            <span>Registro en 1 minuto</span>
                        </div>
                        <div class="auth-feature-item">
                            <i class="fas fa-motorcycle"></i>
                            <span>Envíos directos</span>
                        </div>
                        <div class="auth-feature-item">
                            <i class="fas fa-lock"></i>
                            <span>Tus datos seguros</span>
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
        var pwdConfirmInput = document.getElementById("password_confirmation");
        var toggleBtn = document.getElementById("togglePassword");
        var toggleIcon = document.getElementById("togglePasswordIcon");
        var toggleConfirmBtn = document.getElementById("togglePasswordConfirm");
        var toggleConfirmIcon = document.getElementById("togglePasswordConfirmIcon");
        var capsWarning = document.getElementById("capsLockWarning");
        var registerForm = document.getElementById("registerForm");
        var submitBtn = document.getElementById("registerSubmitBtn");
        var btnText = document.getElementById("regBtnText");
        var btnIcon = document.getElementById("regBtnIcon");

        // Toggle password visibility
        function setupToggle(btn, input, icon) {
            if (btn && input) {
                btn.addEventListener("click", function () {
                    var isPassword = input.type === "password";
                    input.type = isPassword ? "text" : "password";
                    if (icon) {
                        icon.className = isPassword ? "far fa-eye-slash" : "far fa-eye";
                    }
                });
            }
        }

        setupToggle(toggleBtn, pwdInput, toggleIcon);
        setupToggle(toggleConfirmBtn, pwdConfirmInput, toggleConfirmIcon);

        // Caps Lock detection
        function checkCapsLock(event) {
            if (capsWarning) {
                if (event.getModifierState && event.getModifierState("CapsLock")) {
                    capsWarning.style.display = "inline-flex";
                } else {
                    capsWarning.style.display = "none";
                }
            }
        }

        [pwdInput, pwdConfirmInput].forEach(function (inp) {
            if (inp) {
                inp.addEventListener("keydown", checkCapsLock);
                inp.addEventListener("keyup", checkCapsLock);
                inp.addEventListener("focus", checkCapsLock);
                inp.addEventListener("blur", function () {
                    if (capsWarning) capsWarning.style.display = "none";
                });
            }
        });

        // Loading state on form submit
        if (registerForm && submitBtn) {
            registerForm.addEventListener("submit", function () {
                submitBtn.disabled = true;
                if (btnText) btnText.textContent = "Creando cuenta...";
                if (btnIcon) btnIcon.className = "fas fa-spinner fa-spin";
            });
        }
    });
</script>
@endsection
