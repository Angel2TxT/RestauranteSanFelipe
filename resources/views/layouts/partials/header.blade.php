<header class="section page-header">
    <!-- RD Navbar-->
    <div class="rd-navbar-wrap">
        <nav class="rd-navbar rd-navbar-modern" data-layout="rd-navbar-fixed" data-sm-layout="rd-navbar-fixed"
            data-md-layout="rd-navbar-fixed" data-md-device-layout="rd-navbar-fixed" data-lg-layout="rd-navbar-static"
            data-lg-device-layout="rd-navbar-fixed" data-xl-layout="rd-navbar-static"
            data-xl-device-layout="rd-navbar-static" data-xxl-layout="rd-navbar-static"
            data-xxl-device-layout="rd-navbar-static" data-lg-stick-up-offset="56px" data-xl-stick-up-offset="56px"
            data-xxl-stick-up-offset="56px" data-lg-stick-up="true" data-xl-stick-up="true" data-xxl-stick-up="true">
            <div class="rd-navbar-inner-outer">
                <div class="rd-navbar-inner">
                    <!-- RD Navbar Panel-->
                    <div class="rd-navbar-panel">
                        <!-- RD Navbar Toggle-->
                        <button class="rd-navbar-toggle"
                            data-rd-navbar-toggle=".rd-navbar-nav-wrap"><span></span></button>
                        <!-- RD Navbar Brand-->
                        <div class="rd-navbar-qr-button">
                            <button type="button" class="btn btn-primary btn-sm px-2 py-2" data-bs-toggle="modal"
                                data-bs-target="#qrModal">
                                <i class="fas fa-qrcode" style="font-size: 0.8rem;"></i>
                                <!-- Puedes ajustar el tamaño del ícono también -->
                            </button>
                        </div>

                        <div class="rd-navbar-brand"><a class="brand" href=""><img class="brand-logo-dark"
                                    src="images/logoSFB.png" alt="" width="198" height="66"
                                    style="position: relative; left: 5px;" /></a>
                        </div>

                    </div>
                    <div class="rd-navbar-right rd-navbar-nav-wrap">
                        <div class="rd-navbar-aside">
                            <ul class="rd-navbar-contacts-2">
                                <li>
                                    <div class="unit unit-spacing-xs">
                                        <div class="unit-left"><span class="icon mdi mdi-phone"></span></div>
                                        <div class="unit-body"><a class="phone" href="tel:#">+52 919-136-6544</a>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="unit unit-spacing-xs">
                                        <div class="unit-left"><span class="icon mdi mdi-map-marker"></span></div>
                                        <div class="unit-body"><a class="address"
                                                href="https://maps.app.goo.gl/s5PKDvSKJ95TUJmh6">Tila, chiapas, Segunda
                                                Sur Ote.</a></div>
                                    </div>
                                </li>
                            </ul>
                            <ul class="list-share-2">
                                <li><a class="icon mdi mdi-facebook" href="#"></a></li>
                                <li><a class="icon mdi mdi-twitter" href="#"></a></li>
                                <li><a class="icon mdi mdi-instagram" href="#"></a></li>
                                <li><a class="icon mdi mdi-google-plus" href="#"></a></li>
                            </ul>
                        </div>
                        <div class="rd-navbar-main">
                            <!-- RD Navbar Nav-->
                            <ul class="rd-navbar-nav">
                                <li class="rd-nav-item {{ Request::is('/') ? 'active' : '' }}">
                                    <a class="rd-nav-link" href="{{ route('home') }}">Inicio</a>
                                </li>
                                <li class="rd-nav-item {{ Request::is('shop') ? 'active' : '' }}">
                                    <a class="rd-nav-link" href="{{ route('shop') }}">Productos</a>
                                </li>




                                @guest

                                    <li class="rd-nav-item {{ Request::is('login') ? 'active' : '' }}">
                                        <a class="rd-nav-link" href="{{ route('login') }}">Login</a>
                                    </li>

                                    <li class="rd-nav-item {{ Request::is('register') ? 'active' : '' }}">
                                        <a class="rd-nav-link" href="{{ route('register') }}">Registrarse</a>
                                    </li>
                                @else
                                    <li class="rd-nav-item">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                data-toggle="dropdown" aria-expanded="false" style="font-size: 1.1rem">
                                                {{ auth()->user()->name }}
                                            </button>
                                            <div class="dropdown-menu">

                                                <a class="dropdown-item" href="{{ route('orders.my') }}">
                                                    Mis Ordenes
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="{{ route('logout') }}"
                                                    onclick="event.preventDefault();
                                      document.getElementById('logout-form').submit();">
                                                    {{ __('Logout') }}
                                                </a>

                                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                    class="d-none">
                                                    @csrf
                                                </form>

                                            </div>
                                        </div>

                                    </li>

                                @endguest


                            </ul>

                            @auth
                                <div class="rd-navbar-qr-button ms-auto">
                                    <button type="button" class="btn btn-primary btn-sm px-5 py-4" data-bs-toggle="modal"
                                        data-bs-target="#qrModal" onclick="changeImage()">
                                        <i class="fas fa-qrcode" style="font-size: 0.8rem;"> qr</i>
                                    </button>
                                </div>
                            @endauth



                        </div>

                    </div>

                    <div class="rd-navbar-project-hamburger rd-navbar-project-hamburger-open rd-navbar-fixed-element-1"
                        data-multitoggle=".rd-navbar-inner" data-multitoggle-blur=".rd-navbar-wrap"
                        data-multitoggle-isolate="data-multitoggle-isolate">
                        <span class="fas fa-shopping-cart" style="font-size: 1.5 rem; margin-left: -50px;"><span
                                style="font-size: 1rem">{{ Cart::instance('shopping')->content()->count() }}</span></span>

                    </div>

                    <div class="rd-navbar-project">

                        <div class="rd-navbar-project-header">

                            <h5 class="rd-navbar-project-title">Carrito</h5>
                            <div class="rd-navbar-project-hamburger rd-navbar-project-hamburger-close"
                                data-multitoggle=".rd-navbar-inner" data-multitoggle-blur=".rd-navbar-wrap"
                                data-multitoggle-isolate="data-multitoggle-isolate">
                                <div class="project-close"><span></span><span></span></div>
                            </div>
                        </div>
                        <div class="rd-navbar-project-content rd-navbar-content">
                            <div>
                                <div class="row gutters-20" data-lightgallery="group">

                                    <div class="col-12">

                                        <x-cart />



                                        <a href="{{ route('orders.checkout') }}"
                                            class="button button-secondary button-winona">Checkout</a>

                                    </div>


                                </div>

                            </div>

                        </div>

                    </div>


                </div>

            </div>

        </nav>

    </div>
</header>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js"></script>
<!-- Modal para mostrar la imagen -->




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

<!-- Agregar el siguiente JavaScript para cambiar las imágenes -->
<script>
    let qrImages = [
        '{{ asset('images/QR4.png') }}',
        '{{ asset('images/QR5.png') }}', // Primera imagen
        '{{ asset('images/QR3.png') }}' // Tercera imagen
    ];
    let currentIndex = 0;

    function changeImage() {
        // Cambiar la imagen cada vez que se hace clic
        currentIndex = (currentIndex + 1) % qrImages.length; // Esto asegura que se recorra el arreglo circularmente
        document.getElementById('qrImage').src = qrImages[currentIndex];
    }
</script>
