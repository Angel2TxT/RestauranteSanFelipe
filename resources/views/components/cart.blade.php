<div class="cart-drawer" id="cart-table">
@php
    $summary = \App\Services\CartPricing::summarize();
@endphp
    @forelse ($summary['lines'] as $line)
        <article class="cart-item" data-cart-row="{{ $line['row_id'] }}">
            <div class="cart-item-media">
                <img
                    src="{{ asset($line['image'] ?: 'images/no-image.jpg') }}"
                    alt="{{ $line['name'] }}"
                    loading="lazy"
                >
            </div>

            <div class="cart-item-body">
                <div class="cart-item-top">
                    <h6 class="cart-item-name">{{ $line['name'] }}</h6>
                    <a
                        href="{{ route('cart.remove', $line['row_id']) }}"
                        class="cart-item-remove js-cart-remove"
                        data-url="{{ route('cart.remove', $line['row_id']) }}"
                        aria-label="Quitar {{ $line['name'] }}"
                        title="Quitar"
                    >
                        <span class="fas fa-trash-alt"></span>
                    </a>
                </div>

                <div class="cart-item-price">${{ number_format($line['unit_price'], 2) }} c/u</div>

                <div class="cart-item-bottom">
                    <div class="cart-qty">
                        <button type="button" class="cart-qty-btn js-cart-qty-step" data-step="-1" aria-label="Menos">−</button>
                        <input
                            type="number"
                            class="cart-qty-input js-cart-qty"
                            data-url="{{ route('cart.update', $line['row_id']) }}"
                            min="1"
                            max="50"
                            value="{{ $line['qty'] }}"
                        >
                        <button type="button" class="cart-qty-btn js-cart-qty-step" data-step="1" aria-label="Más">+</button>
                    </div>
                    <div class="cart-item-subtotal">
                        ${{ number_format($line['subtotal'], 2) }}
                    </div>
                </div>
            </div>
        </article>
    @empty
        <div class="cart-empty">
            <span class="cart-empty-icon fas fa-shopping-basket"></span>
            <p class="cart-empty-title">Tu carrito está vacío</p>
            <p class="cart-empty-text">Agrega platillos del menú para empezar tu pedido.</p>
            <a href="{{ route('shop') }}" class="cart-empty-link">Ver productos</a>
        </div>
    @endforelse
</div>
