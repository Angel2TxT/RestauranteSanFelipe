@extends('layouts.admin')

@section('title', 'Orden #' . $order->id)
@section('page_title', 'Órdenes')

@section('content')
@php
    $flow = ['pending', 'in_progress', 'ready_for_delivery', 'paid', 'completed'];
    $stepIndex = array_search($order->status, $flow, true);
    $cancelled = in_array($order->status, ['cancelled_by_user', 'cancelled_by_store'], true);

    $badgeClass = match ($order->status) {
        'pending' => 'sf-badge--pending',
        'in_progress' => 'sf-badge--progress',
        'ready_for_delivery' => 'sf-badge--ready',
        'paid' => 'sf-badge--paid',
        'completed' => 'sf-badge--done',
        default => 'sf-badge--cancelled',
    };

    $badgeLabel = match ($order->status) {
        'pending' => 'Pendiente',
        'in_progress' => 'En preparación',
        'ready_for_delivery' => 'Lista para entrega',
        'paid' => 'Por cobrar',
        'completed' => 'Completada',
        'cancelled_by_user' => 'Cancelada por el cliente',
        'cancelled_by_store' => 'Cancelada por el restaurante',
        default => $order->status,
    };

    $statusAction = match ($order->status) {
        'pending' => ['label' => 'Comenzar pedido', 'class' => 'sf-btn-cta sf-btn-cta--start'],
        'in_progress' => ['label' => 'Marcar como lista', 'class' => 'sf-btn-cta sf-btn-cta--progress'],
        'ready_for_delivery' => ['label' => 'Entregar / cobrar', 'class' => 'sf-btn-cta sf-btn-cta--ready'],
        'paid' => ['label' => 'Completar orden', 'class' => 'sf-btn-cta sf-btn-cta--paid'],
        default => null,
    };

    $tipo = match ($order->order_type) {
        'dine_in' => 'En mesa' . ($order->table ? ' · ' . $order->table->name : ''),
        'delivery' => 'A domicilio',
        'pickup' => 'Para llevar',
        default => $order->order_type,
    };

    $stepLabels = [
        'pending' => 'Pendiente',
        'in_progress' => 'Preparación',
        'ready_for_delivery' => 'Lista',
        'paid' => 'Cobro',
        'completed' => 'Hecha',
    ];

    $customerName = trim(($order->user->name ?? '') . ' ' . ($order->user->last_name ?? '')) ?: 'N/D';
@endphp

<div class="sf-order-detail">
    <div class="sf-page-head sf-order-detail__head">
        <div>
            <a href="{{ route('orders.index') }}" class="sf-back-link">
                <i class="fas fa-arrow-left"></i> Volver a órdenes
            </a>
            <h1>Orden #{{ $order->id }}</h1>
            <div class="sf-order-detail__meta">
                <span class="sf-badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                <span>{{ $order->fecha }}</span>
                <span class="sf-dot">·</span>
                <span>{{ $tipo }}</span>
            </div>
        </div>
        <div class="sf-page-actions">
            <a href="{{ route('orders.report', $order) }}" class="sf-btn-soft">
                <i class="fas fa-print"></i> Ticket
            </a>
            @if (!$cancelled && $statusAction)
                <form action="{{ route('orders.status', $order) }}" method="POST" class="d-inline status-form"
                      data-confirm="{{ in_array($order->status, ['ready_for_delivery', 'paid'], true) ? '1' : '0' }}"
                      data-message="{{ $order->status === 'ready_for_delivery' ? '¿Confirmas que está lista para entrega?' : ($order->status === 'paid' ? '¿Confirmas el cobro y cierre?' : '') }}">
                    @csrf
                    <button type="submit" class="{{ $statusAction['class'] }}">{{ $statusAction['label'] }}</button>
                </form>
            @endif
            @if (!$cancelled && !in_array($order->status, ['pending', 'in_progress', 'completed'], true))
                <form action="{{ route('orders.revert', $order) }}" method="POST" class="d-inline status-form"
                      data-confirm="1"
                      data-message="{{ $order->status === 'ready_for_delivery' ? '¿Volver a preparación?' : '¿Volver a listo para entrega?' }}">
                    @csrf
                    <button type="submit" class="sf-btn-soft">Revertir</button>
                </form>
            @endif
            @if (auth()->user()->isAdmin())
                <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline delete-form"
                      data-message="Se eliminará la orden #{{ $order->id }} permanentemente.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="sf-btn-soft sf-btn-soft--danger">Eliminar</button>
                </form>
            @endif
        </div>
    </div>

    @if (!$cancelled)
        <div class="sf-card sf-order-detail__timeline-card">
            <ul class="sf-timeline">
                @foreach ($flow as $i => $step)
                    <li class="{{ $stepIndex !== false && $i < $stepIndex ? 'is-done' : '' }} {{ $stepIndex === $i ? 'is-current' : '' }}">
                        <span>{{ $stepLabels[$step] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="sf-detail-grid">
        <div class="sf-card sf-order-detail__client">
            <div class="sf-card__head"><h2>Cliente</h2></div>
            <div class="sf-card__body">
                <div class="sf-client">
                    <div class="sf-client__avatar" aria-hidden="true">
                        {{ mb_strtoupper(mb_substr($customerName, 0, 1)) }}
                    </div>
                    <div>
                        <strong class="sf-client__name">{{ $customerName }}</strong>
                        <ul class="sf-client__list">
                            <li><i class="fas fa-envelope"></i><span>{{ $order->user->email ?? 'N/D' }}</span></li>
                            <li><i class="fas fa-phone"></i><span>{{ $order->user->phone ?? 'N/D' }}</span></li>
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $order->delivery_address ?: ($order->user->address ?? 'N/D') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                @if ($order->notes)
                    <div class="sf-client__notes">
                        <strong>Notas</strong>
                        <p>{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="sf-card sf-order-detail__products">
            <div class="sf-card__head">
                <h2>Productos</h2>
                <span class="sf-order-detail__count">{{ $order->items->count() }} {{ $order->items->count() === 1 ? 'ítem' : 'ítems' }}</span>
            </div>
            <div class="sf-card__body p-0">
                <div class="sf-item-list">
                    @forelse ($order->items as $item)
                        @php $qty = (int) ($item->pivot->qty ?? $item->qty ?? 1); @endphp
                        <div class="sf-item-row">
                            <img class="sf-thumb" src="{{ asset($item->image ?: 'images/no-image.jpg') }}" alt="{{ $item->name }}">
                            <div class="sf-item-row__info">
                                <strong>{{ $item->name }}</strong>
                                <span>${{ number_format((float) $item->price, 2) }} c/u</span>
                            </div>
                            <span class="sf-qty">×{{ $qty }}</span>
                            <strong class="sf-item-row__sub">${{ number_format((float) $item->price * $qty, 2) }}</strong>
                        </div>
                    @empty
                        <div class="sf-empty">Sin productos</div>
                    @endforelse
                </div>
            </div>
            <div class="sf-order-detail__total">
                <span>Total</span>
                <strong>${{ number_format((float) $order->total, 2) }}</strong>
            </div>
        </div>
    </div>
</div>
@endsection
