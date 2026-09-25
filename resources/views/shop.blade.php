@extends('layouts.default')

@section('content')
@php
  $total = $products->total();
  $hasFilters = request()->filled('search') || request()->filled('category') || request()->filled('sort');
@endphp

<section class="shop-page">
  <div class="shop-hero" style="background-image: linear-gradient(120deg, rgba(70, 50, 138, 0.92), rgba(21, 21, 21, 0.55)), url({{ asset('images/bg-1.jpg') }});">
    <div class="container">
      <nav class="shop-breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('home') }}">Inicio</a>
        <span>/</span>
        <strong>Productos</strong>
      </nav>

      <div class="shop-hero-content">
        <div class="shop-hero-text">
          <h1 class="shop-hero-title">Productos</h1>
          <p class="shop-hero-meta" id="shop-hero-meta">
            {{ $total }}
            {{ $total === 1 ? 'platillo disponible' : 'platillos disponibles' }}
            @if ($hasFilters)
              · filtros activos
            @endif
          </p>
        </div>
        <a class="shop-hero-cta" href="{{ route('home') }}#menu">Ver categorías</a>
      </div>
    </div>
  </div>

  <div class="shop-body">
    <div class="container">
      <form method="GET" action="{{ route('shop') }}" id="shop-form" class="shop-filters-box" data-ajax-shop>
        <div class="shop-filters-row">
          <div class="shop-filters-item shop-filters-item--sort">
            <label class="shop-filters-label" for="shop-sort">Ordenar</label>
            <select id="shop-sort" name="sort" class="shop-filters-input js-native-select">
              <option value="">Por precio</option>
              <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Menor precio</option>
              <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Mayor precio</option>
            </select>
          </div>

          <div class="shop-filters-item shop-filters-item--category">
            <label class="shop-filters-label" for="shop-category">Categoría</label>
            <select id="shop-category" name="category" class="shop-filters-input js-native-select">
              <option value="">Todas</option>
              @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ (string) request('category') === (string) $category->id ? 'selected' : '' }}>
                  {{ $category->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="shop-filters-item shop-filters-item--search">
            <label class="shop-filters-label" for="shop-search">Buscar</label>
            <div class="shop-filters-search">
              <input
                id="shop-search"
                type="search"
                name="search"
                class="shop-filters-input"
                placeholder="Buscar producto..."
                value="{{ request('search') }}"
                autocomplete="off"
              >
              <button class="shop-filters-btn shop-filters-btn--icon" type="submit" aria-label="Buscar">
                <i class="fas fa-search"></i>
              </button>
              <a class="shop-filters-btn shop-filters-btn--text" href="{{ route('shop') }}" id="shop-clear">Limpiar</a>
            </div>
          </div>
        </div>
      </form>

      <div id="shop-results" class="shop-results">
        @include('shop._products')
      </div>
    </div>
  </div>
</section>
@endsection
