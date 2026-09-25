@if ($products->count())
  <div class="shop-results-meta">
    Mostrando {{ $products->firstItem() }}–{{ $products->lastItem() }} de {{ $products->total() }}
  </div>

  <div class="row row-30 shop-products-grid">
    @foreach ($products as $product)
      <x-product :$product />
    @endforeach
  </div>

  @if ($products->hasPages())
    <div class="shop-pagination">
      {{ $products->links() }}
    </div>
  @endif
@else
  <div class="shop-empty">
    <div class="shop-empty__icon" aria-hidden="true">
      <i class="fas fa-utensils"></i>
    </div>
    <h2>No se encontraron productos</h2>
    <p>Prueba otra búsqueda, categoría u orden.</p>
    <a class="shop-empty__cta" href="{{ route('shop') }}" id="shop-empty-clear">Ver todos</a>
  </div>
@endif
