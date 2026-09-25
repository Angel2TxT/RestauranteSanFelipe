<header class="sf-header">
  <div class="sf-topbar">
    <div class="container sf-topbar__inner">
      <div class="sf-topbar__contacts">
        <a href="tel:+529191366544"><i class="mdi mdi-phone"></i><span>+52 919-136-6544</span></a>
        <a href="https://maps.app.goo.gl/s5PKDvSKJ95TUJmh6" target="_blank" rel="noopener">
          <i class="mdi mdi-map-marker"></i><span>Tila, Chiapas</span>
        </a>
      </div>
      <div class="sf-topbar__social">
        <a href="https://web.facebook.com/hospedajesanfelipedejesus" aria-label="Facebook"><i class="mdi mdi-facebook"></i></a>
        <a href="#" aria-label="Instagram"><i class="mdi mdi-instagram"></i></a>
      </div>
    </div>
  </div>

  <div class="sf-nav">
    <div class="container sf-nav__inner">
      <button type="button" class="sf-nav__toggle" id="sf-nav-toggle" aria-label="Abrir menú" aria-expanded="false" aria-controls="sf-menu">
        <span></span><span></span><span></span>
      </button>

      <a class="sf-nav__logo" href="{{ route('home') }}">
        <img src="{{ asset('images/logoSFB.jpg') }}" alt="Restaurante San Felipe" width="150" height="48">
      </a>

      <nav class="sf-menu" id="sf-menu">
        <a class="sf-menu__link {{ Request::is('/') ? 'is-active' : '' }}" href="{{ route('home') }}">Inicio</a>
        <a class="sf-menu__link {{ Request::is('shop') || Request::is('categories/*') || Request::is('products/*') ? 'is-active' : '' }}" href="{{ route('shop') }}">Productos</a>

        @guest
          <a class="sf-menu__link {{ Request::is('login') ? 'is-active' : '' }}" href="{{ route('login') }}">Login</a>
          <a class="sf-menu__link {{ Request::is('register') ? 'is-active' : '' }}" href="{{ route('register') }}">Registrarse</a>
        @else
          @if (!auth()->user()->isClient())
            <a class="sf-menu__link" href="{{ route('admin.home') }}">Admin</a>
          @endif

          <div class="sf-menu__user">
            <button type="button" class="sf-menu__user-btn" id="sf-user-toggle" aria-expanded="false">
              {{ auth()->user()->name }}
              <i class="fas fa-chevron-down"></i>
            </button>
            <div class="sf-menu__dropdown" id="sf-user-menu">
              <a href="{{ route('orders.my') }}">Mis órdenes</a>
              <a href="{{ route('logout') }}"
                 onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Salir
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
              </form>
            </div>
          </div>
        @endguest
      </nav>

      <div class="sf-nav__actions">
        <button type="button" class="sf-btn sf-btn--qr" data-bs-toggle="modal" data-bs-target="#qrModal" onclick="changeImage()">
          <i class="fas fa-qrcode"></i>
          <span>QR</span>
        </button>

        @auth
          <div class="sf-notify" id="sf-notify">
            <button type="button" class="sf-btn sf-btn--notify" id="sf-notify-toggle" aria-label="Notificaciones" aria-expanded="false" aria-controls="sf-notify-panel">
              <i class="fas fa-bell"></i>
              <span id="sf-notify-badge" class="sf-notify-badge is-empty" hidden>0</span>
            </button>
            <div class="sf-notify-panel" id="sf-notify-panel" hidden>
              <div class="sf-notify-panel__head">
                <strong>Notificaciones</strong>
                <button type="button" class="sf-notify-panel__readall" id="sf-notify-readall">Marcar todas</button>
              </div>
              <div class="sf-notify-panel__list" id="sf-notify-list">
                <p class="sf-notify-empty">Sin notificaciones</p>
              </div>
            </div>
          </div>

          <button type="button" class="sf-btn sf-btn--cart" id="sf-cart-open" aria-label="Abrir carrito">
            <i class="fas fa-shopping-bag"></i>
            <span id="cart-count" class="sf-cart-badge{{ Cart::instance('shopping')->content()->count() ? '' : ' is-empty' }}">
              {{ Cart::instance('shopping')->content()->count() }}
            </span>
          </button>
        @endauth
      </div>
    </div>
  </div>

  <div class="sf-cart-backdrop" id="sf-cart-backdrop" hidden></div>
  <aside class="sf-cart" id="sf-cart" aria-hidden="true">
    <div class="sf-cart__header">
      <h5>Carrito</h5>
      <button type="button" class="sf-cart__close" id="sf-cart-close" aria-label="Cerrar carrito">
        <span></span><span></span>
      </button>
    </div>
    <div class="sf-cart__body">
      <div id="cart-panel">
        @include('layouts.partials.cart-panel')
      </div>
    </div>
  </aside>
</header>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js"></script>

<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="qrModalLabel">CÓDIGO QR</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="qrImage" src="{{ asset('images/QR3.png') }}" alt="QR Code" class="img-fluid"
          style="max-width: 50%; cursor: pointer;" onclick="changeImage()">
      </div>
    </div>
  </div>
</div>

<script>
  let qrImages = [
    '{{ asset('images/QR4.png') }}',
    '{{ asset('images/QR5.png') }}',
    '{{ asset('images/QR3.png') }}'
  ];
  let currentIndex = 0;

  function changeImage() {
    currentIndex = (currentIndex + 1) % qrImages.length;
    document.getElementById('qrImage').src = qrImages[currentIndex];
  }
</script>
