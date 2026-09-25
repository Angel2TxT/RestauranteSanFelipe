<div class="col-6 col-lg-4 col-xl-3 mb-4">
  <div class="product-flip" tabindex="0">
    <article class="product">
      <div class="product-face product-face-front">
        <div class="product-media">
          <div class="product-figure">
            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" loading="lazy">
          </div>
          @if ($product->category)
            <span class="product-badge product-badge-new">{{ $product->category->name }}</span>
          @endif
        </div>

        <div class="product-body">
          <h6 class="product-title">{{ $product->name }}</h6>
          @if ($product->description)
            <p class="product-excerpt">{{ \Illuminate\Support\Str::limit($product->description, 48) }}</p>
          @endif
          <div class="product-price-wrap">
            <div class="product-price">${{ number_format($product->price, 2) }}</div>
          </div>
        </div>
      </div>

      <div class="product-face product-face-back">
        <div class="product-back-inner">
          <h6 class="product-back-title">{{ $product->name }}</h6>
          <div class="product-back-price">${{ number_format($product->price, 2) }}</div>
          <div class="product-back-actions">
            <a class="product-back-btn product-back-btn-primary js-add-to-cart" href="{{ route('cart.add', $product) }}" data-url="{{ route('cart.add', $product) }}">Agregar</a>
            <a class="product-back-btn product-back-btn-secondary" href="{{ route('products.display', $product) }}">Ver</a>
          </div>
        </div>
      </div>
    </article>
  </div>
</div>
