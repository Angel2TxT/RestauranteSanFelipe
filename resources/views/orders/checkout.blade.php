@extends('layouts.default')

@section('content')
@php
  $cart = Cart::instance('shopping');
  $cartCount = $cart->count();
  $cartTotal = $cart->priceTotal();
  $selectedType = old('order_type', 'dine_in');
@endphp

<section class="checkout-page">
  <div class="checkout-hero" style="background-image: linear-gradient(120deg, rgba(70, 50, 138, 0.92), rgba(21, 21, 21, 0.55)), url({{ asset('images/bg-1.jpg') }});">
    <div class="container">
      <nav class="checkout-breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('home') }}">Inicio</a>
        <span>/</span>
        <a href="{{ route('shop') }}">Productos</a>
        <span>/</span>
        <strong>Checkout</strong>
      </nav>

      <div class="checkout-hero-content">
        <div class="checkout-hero-text">
          <h1 class="checkout-hero-title">Checkout</h1>
          <p class="checkout-hero-meta">
            Completa tus datos para confirmar
            {{ $cartCount }}
            {{ $cartCount === 1 ? 'producto' : 'productos' }}
          </p>
        </div>
        <a class="checkout-hero-cta" href="{{ route('shop') }}">Seguir comprando</a>
      </div>
    </div>
  </div>

  <div class="checkout-body">
    <div class="container">
      <div class="checkout-grid">
        <div class="checkout-main">
          <div class="checkout-card">
            <header class="checkout-card__head">
              <h2>Tus datos</h2>
              <p>Usamos tu información de cuenta; puedes ajustarla si hace falta.</p>
            </header>

            @if ($errors->any())
              <div class="checkout-errors" role="alert">
                <strong>Revisa estos datos:</strong>
                <ul>
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            @if (session('error'))
              <div class="checkout-errors" role="alert">
                <p>{{ session('error') }}</p>
              </div>
            @endif

            <form class="checkout-form" action="{{ route('orders.proccess.checkout') }}" method="POST" id="checkout-form">
              @csrf

              <div class="checkout-fields">
                <div class="checkout-field">
                  <label class="checkout-label" for="name">Nombre*</label>
                  <input class="checkout-input" id="name" type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required autocomplete="given-name">
                </div>

                <div class="checkout-field">
                  <label class="checkout-label" for="last_name">Apellido*</label>
                  <input class="checkout-input" id="last_name" type="text" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" required autocomplete="family-name">
                </div>

                <div class="checkout-field">
                  <label class="checkout-label" for="email">Email*</label>
                  <input class="checkout-input" id="email" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required autocomplete="email">
                </div>

                <div class="checkout-field">
                  <label class="checkout-label" for="phone">Teléfono*</label>
                  <input class="checkout-input" id="phone" type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required autocomplete="tel">
                </div>
              </div>

              <fieldset class="checkout-type">
                <legend class="checkout-label">Tipo de orden*</legend>
                <div class="checkout-type__options" role="radiogroup" aria-label="Tipo de orden">
                  <label class="checkout-type__option {{ $selectedType === 'dine_in' ? 'is-active' : '' }}">
                    <input type="radio" name="order_type" value="dine_in" {{ $selectedType === 'dine_in' ? 'checked' : '' }}>
                    <span class="checkout-type__icon"><i class="mdi mdi-silverware-fork-knife"></i></span>
                    <span class="checkout-type__text">
                      <strong>En el restaurante</strong>
                      <small>Elige tu mesa</small>
                    </span>
                  </label>

                  <label class="checkout-type__option {{ $selectedType === 'delivery' ? 'is-active' : '' }}">
                    <input type="radio" name="order_type" value="delivery" {{ $selectedType === 'delivery' ? 'checked' : '' }}>
                    <span class="checkout-type__icon"><i class="mdi mdi-bike"></i></span>
                    <span class="checkout-type__text">
                      <strong>A domicilio</strong>
                      <small>Envío a tu dirección</small>
                    </span>
                  </label>

                  <label class="checkout-type__option {{ $selectedType === 'pickup' ? 'is-active' : '' }}">
                    <input type="radio" name="order_type" value="pickup" {{ $selectedType === 'pickup' ? 'checked' : '' }}>
                    <span class="checkout-type__icon"><i class="mdi mdi-store"></i></span>
                    <span class="checkout-type__text">
                      <strong>Para llevar</strong>
                      <small>Recoger en local</small>
                    </span>
                  </label>
                </div>
              </fieldset>

              <div class="checkout-field checkout-field--full" id="table_field" @if($selectedType !== 'dine_in') hidden @endif>
                <label class="checkout-label" for="table_id">Mesa disponible*</label>
                <select class="checkout-input js-native-select" id="table_id" name="table_id">
                  <option value="">Selecciona una mesa</option>
                  @foreach ($tables as $table)
                    <option value="{{ $table->id }}" @selected(old('table_id') == $table->id)>{{ $table->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="checkout-field checkout-field--full" id="address_field" @if($selectedType !== 'delivery') hidden @endif>
                <label class="checkout-label" for="address">Dirección de entrega*</label>
                <input class="checkout-input" id="address" type="text" name="address" value="{{ old('address', auth()->user()->address) }}" readonly>
                <p class="checkout-hint">Se usa la dirección registrada en tu cuenta.</p>
              </div>

              <div class="checkout-field checkout-field--full">
                <label class="checkout-label" for="notes">Notas del pedido</label>
                <textarea class="checkout-input checkout-textarea" id="notes" name="notes" rows="4" placeholder="Ej. sin cebolla, punto de la carne, etc.">{{ old('notes') }}</textarea>
              </div>

              <div class="checkout-actions">
                <button type="submit" class="checkout-submit" @disabled($cartCount === 0)>
                  Confirmar pedido
                </button>
              </div>
            </form>
          </div>
        </div>

        <aside class="checkout-aside">
          <div class="checkout-summary">
            <header class="checkout-summary__head">
              <h2>Tu orden</h2>
              <span>{{ $cartCount }} {{ $cartCount === 1 ? 'artículo' : 'artículos' }}</span>
            </header>

            <div class="checkout-summary__list">
              <x-cart />
            </div>

            @if ($cartCount > 0)
              <footer class="checkout-summary__foot">
                <div class="checkout-summary__total">
                  <span>Total</span>
                  <strong>${{ $cartTotal }}</strong>
                </div>
                <p class="checkout-summary__note">Al confirmar, tu pedido quedará pendiente de preparación.</p>
              </footer>
            @endif
          </div>
        </aside>
      </div>
    </div>
  </div>
</section>

<script>
  (function () {
    function toggleOrderOptions() {
      var checked = document.querySelector('input[name="order_type"]:checked');
      var orderType = checked ? checked.value : 'dine_in';
      var tableField = document.getElementById('table_field');
      var addressField = document.getElementById('address_field');
      var tableSelect = document.getElementById('table_id');

      if (tableField) {
        tableField.hidden = orderType !== 'dine_in';
        if (tableSelect && orderType !== 'dine_in') {
          tableSelect.value = '';
        }
      }
      if (addressField) {
        addressField.hidden = orderType !== 'delivery';
      }

      document.querySelectorAll('.checkout-type__option').forEach(function (option) {
        var input = option.querySelector('input');
        option.classList.toggle('is-active', !!(input && input.checked));
      });
    }

    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('input[name="order_type"]').forEach(function (input) {
        input.addEventListener('change', toggleOrderOptions);
      });
      toggleOrderOptions();
    });
  })();
</script>
@endsection
