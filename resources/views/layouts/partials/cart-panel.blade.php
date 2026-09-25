<div id="cart-panel-inner" class="cart-panel">
    <div class="cart-panel-list">
        <x-cart />
    </div>

    @if (Cart::instance('shopping')->count() > 0)
        <div class="cart-panel-footer">
            <div class="cart-total">
                <span class="cart-total-label">Total</span>
                <strong class="cart-total-value">${{ Cart::instance('shopping')->priceTotal() }}</strong>
            </div>
            <a href="{{ route('orders.checkout') }}" class="cart-checkout-btn">
                Ir a pagar
            </a>
        </div>
    @endif
</div>
