<div id="cart-panel-inner" class="cart-panel">
    <div class="cart-panel-list">
        <x-cart />
    </div>

    @php
        $summary = \App\Services\CartPricing::summarize();
        $editingOrderId = session('editing_order_id');
    @endphp

    @if ($summary['items_count'] > 0)
        <div class="cart-panel-footer">
            <div class="cart-total">
                <span class="cart-total-label">Total</span>
                <strong class="cart-total-value">${{ number_format($summary['total'], 2) }}</strong>
            </div>
            @if ($editingOrderId)
                <form action="{{ route('orders.items.merge', $editingOrderId) }}" method="POST" class="js-order-merge-form">
                    @csrf
                    <button type="submit" class="cart-checkout-btn">
                        Agregar a orden #{{ $editingOrderId }}
                    </button>
                </form>
                <form action="{{ route('orders.edit.stop') }}" method="POST">
                    @csrf
                    <button type="submit" class="cart-edit-cancel">Cancelar edición</button>
                </form>
            @else
                <a href="{{ route('orders.checkout') }}" class="cart-checkout-btn">
                    Ir a pagar
                </a>
            @endif
        </div>
    @endif
</div>
