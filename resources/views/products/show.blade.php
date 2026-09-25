@extends('layouts.default')

@section('content')
<section class="product-page">
  <div class="product-hero" style="background-image: linear-gradient(120deg, rgba(70, 50, 138, 0.92), rgba(21, 21, 21, 0.55)), url({{ asset($product->image ?: 'images/bg-1.jpg') }});">
    <div class="container">
      <nav class="product-breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('home') }}">Inicio</a>
        <span>/</span>
        <a href="{{ route('shop') }}">Productos</a>
        @if ($product->category)
          <span>/</span>
          <a href="{{ route('shop', ['category' => $product->category_id]) }}">{{ $product->category->name }}</a>
        @endif
        <span>/</span>
        <strong>{{ $product->name }}</strong>
      </nav>
    </div>
  </div>

  <div class="product-body">
    <div class="container">
      <div class="product-detail">
        <div class="product-detail__media">
          <img src="{{ asset($product->image ?: 'images/no-image.jpg') }}" alt="{{ $product->name }}">
        </div>

        <div class="product-detail__info">
          @if ($product->category)
            <a class="product-detail__category" href="{{ route('shop', ['category' => $product->category_id]) }}">
              {{ $product->category->name }}
            </a>
          @endif

          <h1 class="product-detail__title">{{ $product->name }}</h1>
          <div class="product-detail__price">${{ number_format((float) $product->price, 2) }}</div>

          <p class="product-detail__text">
            {{ $product->description ?: 'Sin descripción disponible.' }}
          </p>

          <div class="product-detail__actions">
            <a
              href="{{ route('cart.add', $product) }}"
              data-url="{{ route('cart.add', $product) }}"
              class="product-detail__btn product-detail__btn--primary js-add-to-cart"
            >
              Agregar al carrito
            </a>
            <a href="{{ route('shop') }}" class="product-detail__btn product-detail__btn--ghost">
              Volver al menú
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
