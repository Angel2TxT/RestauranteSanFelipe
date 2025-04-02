@extends('layouts.default')

@section('content')

<section class="bg-gray-7">
    <div class="breadcrumbs-custom box-transform-wrap context-dark">
        <div class="container">
            <h3 class="breadcrumbs-custom-title">Checkout</h3>
            <div class="breadcrumbs-custom-decor"></div>
        </div>
        <div class="box-transform" style="background-image: url(images/bg-1.jpg);"></div>
    </div>
    <div class="container">
        <ul class="breadcrumbs-custom-path">
            <li><a href="#">Inicio</a></li>
            <li class="active"><a href="#">Checkout</a></li>
        </ul>
    </div>
</section>

<div class="container mt-4 mb-4">
    <div class="row">
        <!-- Detalles -->
        <div class="col-12 col-md-7 mb-4 mb-md-0">
            <h3 class="mb-4">Detalles</h3>

            <x-errors />

            <form action="{{ route('orders.proccess.checkout') }}" method="POST">
                @csrf
                <div class="row row-20 gutters-20">
                    <!-- Formulario de detalles (no cambiar) -->
                    <div class="col-md-6">
                        <div class="form-wrap">
                            <label class="form-label" for="name">Nombre*</label>
                            <input class="form-input" id="name" type="text" value="{{ auth()->user()->name ?? old('name') }}" name="name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-wrap">
                            <label class="form-label" for="last_name">Apellido*</label>
                            <input class="form-input" id="last_name" type="text" value="{{ auth()->user()->last_name ?? old('last_name') }}" name="last_name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-wrap">
                            <label class="form-label" for="email">Email*</label>
                            <input class="form-input" id="email" type="email" value="{{ auth()->user()->email ?? old('email') }}" name="email">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-wrap">
                            <label class="form-label" for="phone">Teléfono*</label>
                            <input class="form-input" id="phone" type="text" value="{{ auth()->user()->phone ?? old('phone') }}" name="phone">
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-wrap">
                            <label class="form-label" for="order_type">Tipo de Orden*</label>
                            <select class="form-input" id="order_type" name="order_type" onchange="toggleOrderOptions()">
                                <option value="dine_in">Consumo en el restaurante</option>
                                <option value="delivery">Envío a domicilio</option>
                                <option value="pickup">Recoger en el restaurante</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Select para la mesa (solo si es consumo en el restaurante) -->
                    <div class="col-md-12" id="table_field" style="display: none;">
                        <div class="form-wrap">
                            <label class="form-label" for="table_id"></label>
                            <select class="form-input" id="table_id" name="table_id">
                                <option value="">Selecciona una mesa</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table->id }}">{{ $table->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Campo de dirección (solo si es delivery) -->
                    <div class="col-md-12" id="address_field" style="display: none;">
                        <div class="form-wrap">
                            <label class="form-label" for="address">Dirección*</label>
                            <input class="form-input" id="address" type="text" value="{{ auth()->user()->address ?? '' }}" name="address" readonly>
                        </div>
                    </div>

                    <!-- Especificaciones del cliente -->
                    <div class="col-md-12">
                        <label class="form-label rd-input-label" for="notes">Especificación del cliente</label>
                        <textarea class="form-input" id="notes" rows="5" cols="5" name="notes">{{ old('notes') }}</textarea>
                    </div>
                    @php $cartCount = Cart::instance('shopping')->count(); @endphp 
                    <button type="submit" class="btn btn-primary mt-4 float-right" 
        @if($cartCount == 0) disabled @endif>
    Ordenar
</button>
                </form>
            </div>
        </div>

        <!-- Tu orden -->
        <div class="col-12 col-md-5">
            <h3 class="mb-4">Tu orden</h3>
            <x-cart />
        </div>
       



    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let cartCount = {{ Cart::instance('shopping')->count() }};
        
        if (cartCount === 0) {
            alert("El carrito está vacío. Serás redirigido a la página de inicio.");
            window.location.href = "{{ route('home') }}";
        }
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.addEventListener("keydown", function (event) {
            if (event.getModifierState("CapsLock")) {
                showCapsLockWarning(true);
            } else {
                showCapsLockWarning(false);
            }
        });

        document.addEventListener("keyup", function (event) {
            if (!event.getModifierState("CapsLock")) {
                showCapsLockWarning(false);
            }
        });

        function showCapsLockWarning(show) {
            let warning = document.getElementById("caps-lock-warning");

            if (!warning) {
                warning = document.createElement("div");
                warning.id = "caps-lock-warning";
                warning.style.position = "fixed";
                warning.style.bottom = "20px";
                warning.style.right = "20px";
                warning.style.backgroundColor = "red";
                warning.style.color = "white";
                warning.style.padding = "10px";
                warning.style.borderRadius = "5px";
                warning.style.boxShadow = "0 0 10px rgba(0,0,0,0.5)";
                warning.style.display = "none";
                warning.innerText = "⚠️ Bloq Mayús está activado";
                document.body.appendChild(warning);
            }

            warning.style.display = show ? "block" : "none";
        }
    });
</script>


<script>
    function toggleOrderOptions() {
        var orderType = document.getElementById("order_type").value;
        var tableField = document.getElementById("table_field");
        var addressField = document.getElementById("address_field");

        // Mostrar/ocultar los campos según el tipo de orden seleccionado
        tableField.style.display = (orderType === "dine_in") ? "block" : "none";
        addressField.style.display = (orderType === "delivery") ? "block" : "none";
    }

    document.addEventListener("DOMContentLoaded", function() {
        toggleOrderOptions();
    });
</script>

@endsection
