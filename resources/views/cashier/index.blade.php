@extends('layouts.admin')

@section('title', 'Caja')
@section('page_title', 'Caja')

@section('content')
<div class="sf-cashier" id="sf-cashier"
     data-feed-url="{{ route('cashier.feed') }}"
     data-csrf="{{ csrf_token() }}">
    <div class="sf-kitchen__head">
        <div>
            <h1>Pantalla de caja</h1>
            <p>Cobros, cambio y cierre de órdenes · se actualiza sola</p>
        </div>
        <div class="sf-kitchen__stats">
            <div class="sf-kitchen__stat sf-cashier__stat--ready">
                <span>Por cobrar</span>
                <strong id="cashier-count-ready">0</strong>
            </div>
            <div class="sf-kitchen__stat sf-cashier__stat--paid">
                <span>Pagadas</span>
                <strong id="cashier-count-paid">0</strong>
            </div>
            <div class="sf-kitchen__live">
                <span class="sf-kitchen__dot"></span>
                <span id="cashier-live-label">En vivo</span>
            </div>
        </div>
    </div>

    <div class="sf-kitchen__board">
        <section class="sf-kitchen__col">
            <header class="sf-kitchen__col-head sf-cashier__col-head--ready">
                <h2><i class="fas fa-hand-holding-usd"></i> Por cobrar</h2>
                <span id="cashier-badge-ready">0</span>
            </header>
            <div class="sf-kitchen__cards" id="cashier-ready">
                <div class="sf-kitchen__empty">Sin órdenes por cobrar</div>
            </div>
        </section>

        <section class="sf-kitchen__col">
            <header class="sf-kitchen__col-head sf-cashier__col-head--paid">
                <h2><i class="fas fa-check-circle"></i> Pagadas / cerrar</h2>
                <span id="cashier-badge-paid">0</span>
            </header>
            <div class="sf-kitchen__cards" id="cashier-paid">
                <div class="sf-kitchen__empty">Sin órdenes pagadas</div>
            </div>
        </section>
    </div>
</div>

<div id="sf-pay-modal" class="sf-pay" hidden>
    <div class="sf-pay__backdrop" data-pay-close></div>
    <div class="sf-pay__dialog" role="dialog" aria-modal="true" aria-labelledby="sf-pay-title">
        <div class="sf-pay__head">
            <div>
                <p class="sf-pay__eyebrow">Cobrar orden</p>
                <h3 id="sf-pay-title">Orden #0</h3>
            </div>
            <button type="button" class="sf-pay__x" data-pay-close aria-label="Cerrar">&times;</button>
        </div>

        <div class="sf-pay__due">
            <span>Total a cobrar</span>
            <strong id="sf-pay-due">$0.00</strong>
        </div>

        <label class="sf-pay__label" for="sf-pay-received">Monto recibido</label>
        <div class="sf-pay__input-wrap">
            <span>$</span>
            <input type="number" id="sf-pay-received" inputmode="decimal" min="0" step="0.01" placeholder="0.00" autocomplete="off">
        </div>

        <div class="sf-pay__quick" id="sf-pay-quick">
            <button type="button" data-amount="exact">Exacto</button>
            <button type="button" data-amount="50">$50</button>
            <button type="button" data-amount="100">$100</button>
            <button type="button" data-amount="200">$200</button>
            <button type="button" data-amount="500">$500</button>
            <button type="button" data-amount="1000">$1000</button>
        </div>

        <div class="sf-pay__change" id="sf-pay-change-box">
            <span id="sf-pay-change-label">Cambio</span>
            <strong id="sf-pay-change">$0.00</strong>
        </div>

        <div class="sf-pay__actions">
            <button type="button" class="sf-pay__btn sf-pay__btn--ghost" data-pay-close>Cancelar</button>
            <button type="button" class="sf-pay__btn sf-pay__btn--primary" id="sf-pay-confirm" disabled>Confirmar cobro</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/js/sf-cashier.js') }}?v={{ filemtime(public_path('admin/js/sf-cashier.js')) }}"></script>
@endpush
