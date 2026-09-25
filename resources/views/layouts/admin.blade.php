<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administración') · San Felipe</title>

    <link href="{{ asset('admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/sf-admin.css') }}?v={{ filemtime(public_path('admin/css/sf-admin.css')) }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="sf-admin">
@php
    $user = auth()->user();
    $isAdmin = $user && $user->isAdmin();
    $isStaff = $user && ($user->isAdmin() || $user->isEmployee() || $user->isDelivery());
@endphp

<div id="ajax-toast" class="ajax-toast" hidden aria-live="polite">
    <span class="ajax-toast__icon" aria-hidden="true"></span>
    <span class="ajax-toast__msg"></span>
</div>

<div id="sf-confirm" class="sf-confirm" hidden>
    <div class="sf-confirm__backdrop" data-sf-confirm-dismiss></div>
    <div class="sf-confirm__dialog" role="alertdialog" aria-modal="true" aria-labelledby="sf-confirm-title" aria-describedby="sf-confirm-text">
        <div class="sf-confirm__icon" aria-hidden="true">
            <i class="fas fa-exclamation"></i>
        </div>
        <h3 class="sf-confirm__title" id="sf-confirm-title">¿Continuar?</h3>
        <p class="sf-confirm__text" id="sf-confirm-text"></p>
        <div class="sf-confirm__actions">
            <button type="button" class="sf-confirm__btn sf-confirm__btn--ghost" data-sf-confirm-dismiss>No, volver</button>
            <button type="button" class="sf-confirm__btn sf-confirm__btn--danger" id="sf-confirm-ok">Sí, confirmar</button>
        </div>
    </div>
</div>

@if (session('msg') || session('error'))
    <div id="sf-flash-data" hidden
         data-msg="{{ session('msg') }}"
         data-error="{{ session('error') }}"></div>
@endif

<div class="sf-admin-wrap">
    <aside class="sf-aside" id="sf-aside" aria-label="Menú administrativo">
        <a class="sf-aside__brand" href="{{ $isAdmin ? route('admin.home') : route('orders.index') }}">
            <img src="{{ asset('images/LogoN.png') }}" alt="San Felipe">
            <span class="sf-aside__brand-text">
                <strong>San Felipe</strong>
                <span>{{ $isAdmin ? 'Administración' : 'Operación' }}</span>
            </span>
        </a>

        <nav class="sf-aside__nav">
            @if ($isAdmin)
                <div class="sf-aside__label">General</div>
                <a class="sf-aside__link {{ request()->routeIs('admin.home') ? 'is-active' : '' }}" href="{{ route('admin.home') }}">
                    <i class="fas fa-fw fa-home"></i><span>Inicio</span>
                </a>

                <div class="sf-aside__label">Catálogo</div>
                <a class="sf-aside__link {{ request()->routeIs('products.*') ? 'is-active' : '' }}" href="{{ route('products.index') }}">
                    <i class="fas fa-fw fa-hamburger"></i><span>Productos</span>
                </a>
                <a class="sf-aside__link {{ request()->routeIs('categories.*') ? 'is-active' : '' }}" href="{{ route('categories.index') }}">
                    <i class="fas fa-fw fa-tags"></i><span>Categorías</span>
                </a>
                <a class="sf-aside__link {{ request()->routeIs('sliders.*') ? 'is-active' : '' }}" href="{{ route('sliders.index') }}">
                    <i class="fas fa-fw fa-image"></i><span>Deslizadores</span>
                </a>
            @endif

            @if ($isStaff)
                <div class="sf-aside__label">Operación</div>
                <a class="sf-aside__link {{ request()->routeIs('kitchen.*') ? 'is-active' : '' }}" href="{{ route('kitchen.index') }}">
                    <i class="fas fa-fw fa-fire"></i><span>Cocina</span>
                </a>
                <a class="sf-aside__link {{ request()->routeIs('cashier.*') ? 'is-active' : '' }}" href="{{ route('cashier.index') }}">
                    <i class="fas fa-fw fa-cash-register"></i><span>Caja</span>
                </a>
                <a class="sf-aside__link {{ request()->routeIs('orders.*') ? 'is-active' : '' }}" href="{{ route('orders.index') }}">
                    <i class="fas fa-fw fa-receipt"></i><span>Órdenes</span>
                </a>
            @endif

            @if ($isAdmin)
                <div class="sf-aside__label">Equipo</div>
                <a class="sf-aside__link {{ request()->routeIs('users.*') ? 'is-active' : '' }}" href="{{ route('users.index') }}">
                    <i class="fas fa-fw fa-users"></i><span>Usuarios</span>
                </a>
            @endif
        </nav>
    </aside>

    <div class="sf-aside__backdrop" id="sf-aside-backdrop" hidden></div>

    <div class="sf-main">
        <header class="sf-topbar">
            <button type="button" class="sf-topbar__menu" id="sf-aside-toggle" aria-label="Abrir menú" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="sf-topbar__title">@yield('page_title', 'Panel')</h1>

            <div class="sf-topbar__actions">
                <a href="{{ route('home') }}" class="sf-topbar__btn" target="_blank" rel="noopener">
                    <i class="fas fa-external-link-alt"></i>
                    <span>Ver sitio</span>
                </a>

                <div class="sf-notify" id="sf-notify">
                    <button type="button" class="sf-topbar__icon-btn" id="sf-notify-toggle" aria-label="Notificaciones" aria-expanded="false" aria-controls="sf-notify-panel">
                        <i class="fas fa-bell"></i>
                        <span id="sf-notify-badge" class="sf-notify-badge is-empty" hidden>0</span>
                    </button>
                    <div class="sf-admin-notify-panel" id="sf-notify-panel" hidden>
                        <div class="sf-notify-panel__head">
                            <strong>Notificaciones</strong>
                            <button type="button" class="sf-notify-panel__readall" id="sf-notify-readall">Marcar todas</button>
                        </div>
                        <div class="sf-notify-panel__list" id="sf-notify-list">
                            <p class="sf-notify-empty">Sin notificaciones</p>
                        </div>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="sf-topbar__user dropdown-toggle" type="button" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="{{ $user->image ? asset($user->image) : asset('images/no-image.jpg') }}" alt="">
                        <span class="d-none d-md-inline">{{ $user->name }}</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Editar perfil
                        </a>
                        <a class="dropdown-item" href="https://drive.google.com/file/d/1DFvQfGHRMwgGOGZbpP3WknBAlsBtl3Vx/view?usp=drivesdk" target="_blank" rel="noopener">
                            <i class="fas fa-mobile-alt fa-sm fa-fw mr-2 text-gray-400"></i> Descargar App
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Cerrar sesión
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="sf-content">
            @yield('content')
        </main>

        <footer class="sf-footer">
            &copy; {{ date('Y') }} Restaurante San Felipe de Jesús
        </footer>
    </div>
</div>

<script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin/js/sf-admin.js') }}?v={{ filemtime(public_path('admin/js/sf-admin.js')) }}"></script>
<script src="{{ asset('js/notifications.js') }}?v={{ filemtime(public_path('js/notifications.js')) }}"></script>
@stack('scripts')
</body>
</html>
