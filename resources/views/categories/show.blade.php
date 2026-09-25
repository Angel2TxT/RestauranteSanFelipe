@extends('layouts.default')

@section('content')

@php
  $productCount = $category->products->count();
  $gridClass = $productCount <= 3 ? 'category-products category-products--few' : 'category-products';
@endphp

<section class="category-page">
  <div class="category-hero" style="background-image: linear-gradient(120deg, rgba(70, 50, 138, 0.9), rgba(21, 21, 21, 0.5)), url({{ asset($category->image ?: 'images/bg-1.jpg') }});">
    <div class="container">
      <nav class="category-breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('home') }}">Inicio</a>
        <span>/</span>
        <a href="{{ route('shop') }}">Productos</a>
        <span>/</span>
        <strong>{{ $category->name }}</strong>
      </nav>

      <div class="category-hero-content">
        @if ($category->icon)
          <span class="category-hero-icon linearicons-{{ $category->icon }}"></span>
        @endif
        <div class="category-hero-text">
          <h1 class="category-hero-title">{{ $category->name }}</h1>
          <p class="category-hero-meta">
            {{ $productCount }}
            {{ $productCount === 1 ? 'producto disponible' : 'productos disponibles' }}
          </p>
        </div>
      </div>
    </div>
  </div>

  <div class="category-body">
    <div class="container">
      @if ($productCount)
        <div class="category-toolbar">
          <p class="category-toolbar-text">Elige un platillo y agrégalo al carrito</p>
          <a class="category-toolbar-link" href="{{ route('shop') }}">Ver todos los productos</a>
        </div>

        <div class="row row-30 shop-products-grid {{ $gridClass }} justify-content-center">
          @foreach ($category->products as $product)
            <x-product :$product />
          @endforeach
        </div>
      @else
        <div class="shop-empty category-empty">
          <h5>No hay productos en esta categoría</h5>
          <p>Pronto agregaremos más opciones a {{ $category->name }}.</p>
          <a class="shop-filters-btn shop-filters-btn--text" href="{{ route('shop') }}">Ir a productos</a>
        </div>
      @endif

      @if (isset($otherCategories) && $otherCategories->count())
        <div class="category-more">
          <h4 class="category-more-title">Otras categorías</h4>
          <div class="category-more-grid">
            @foreach ($otherCategories as $other)
              <a class="category-more-card" href="{{ route('categories.display', $other) }}">
                <span class="category-more-card-icon linearicons-{{ $other->icon }}"></span>
                <span class="category-more-card-name">{{ $other->name }}</span>
              </a>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  </div>
</section>

@endsection
