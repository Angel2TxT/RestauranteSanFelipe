@extends('layouts.admin')

@section('title', 'Órdenes')
@section('page_title', 'Órdenes')

@section('content')
@php
    $currentStatus = request('search_status');
    $chipQuery = request()->except('search_status', 'page');

    $statusAction = function ($status) {
        return match ($status) {
            'pending' => ['label' => 'Comenzar', 'class' => 'btn-danger'],
            'in_progress' => ['label' => 'Marcar lista', 'class' => 'btn-warning'],
            'ready_for_delivery' => ['label' => 'Entregar / cobrar', 'class' => 'btn-primary'],
            'paid' => ['label' => 'Completar', 'class' => 'btn-success'],
            'completed' => ['label' => 'Completada', 'class' => 'btn-secondary'],
            'cancelled_by_user', 'cancelled_by_store' => ['label' => 'Cancelada', 'class' => 'btn-light'],
            default => ['label' => $status, 'class' => 'btn-light'],
        };
    };

    $badgeClass = function ($status) {
        return match ($status) {
            'pending' => 'sf-badge--pending',
            'in_progress' => 'sf-badge--progress',
            'ready_for_delivery' => 'sf-badge--ready',
            'paid' => 'sf-badge--paid',
            'completed' => 'sf-badge--done',
            'cancelled_by_user', 'cancelled_by_store' => 'sf-badge--cancelled',
            default => 'sf-badge--done',
        };
    };

    $badgeLabel = function ($status) {
        return match ($status) {
            'pending' => 'Pendiente',
            'in_progress' => 'En preparación',
            'ready_for_delivery' => 'Lista',
            'paid' => 'Por cobrar',
            'completed' => 'Completada',
            'cancelled_by_user' => 'Cancelada (cliente)',
            'cancelled_by_store' => 'Cancelada (tienda)',
            default => $status,
        };
    };
@endphp

<div class="sf-page-head">
    <div>
        <h1>Cola de órdenes</h1>
        <p>Gestiona el flujo del día: cocina, entrega y cobro</p>
    </div>
</div>

<div class="sf-chips">
    <a class="sf-chip {{ $currentStatus === null || $currentStatus === '' ? 'is-active' : '' }}"
       href="{{ route('orders.index', $chipQuery) }}">
        Todas <span class="sf-chip__count">{{ $counts['all'] }}</span>
    </a>
    <a class="sf-chip {{ $currentStatus === 'pending' ? 'is-active' : '' }}"
       href="{{ route('orders.index', array_merge($chipQuery, ['search_status' => 'pending'])) }}">
        Pendientes <span class="sf-chip__count">{{ $counts['pending'] }}</span>
    </a>
    <a class="sf-chip {{ $currentStatus === 'in_progress' ? 'is-active' : '' }}"
       href="{{ route('orders.index', array_merge($chipQuery, ['search_status' => 'in_progress'])) }}">
        En preparación <span class="sf-chip__count">{{ $counts['in_progress'] }}</span>
    </a>
    <a class="sf-chip {{ $currentStatus === 'ready_for_delivery' ? 'is-active' : '' }}"
       href="{{ route('orders.index', array_merge($chipQuery, ['search_status' => 'ready_for_delivery'])) }}">
        Listas <span class="sf-chip__count">{{ $counts['ready_for_delivery'] }}</span>
    </a>
    <a class="sf-chip {{ $currentStatus === 'paid' ? 'is-active' : '' }}"
       href="{{ route('orders.index', array_merge($chipQuery, ['search_status' => 'paid'])) }}">
        Por cobrar <span class="sf-chip__count">{{ $counts['paid'] }}</span>
    </a>
    <a class="sf-chip {{ $currentStatus === 'completed' ? 'is-active' : '' }}"
       href="{{ route('orders.index', array_merge($chipQuery, ['search_status' => 'completed'])) }}">
        Completadas <span class="sf-chip__count">{{ $counts['completed'] }}</span>
    </a>
    <a class="sf-chip {{ $currentStatus === 'cancelled' ? 'is-active' : '' }}"
       href="{{ route('orders.index', array_merge($chipQuery, ['search_status' => 'cancelled'])) }}">
        Canceladas <span class="sf-chip__count">{{ $counts['cancelled'] }}</span>
    </a>
</div>

<div class="sf-card">
    <div class="sf-card__head">
        <h2>Filtros</h2>
    </div>
    <div class="sf-card__body">
        <form action="{{ route('orders.index') }}" method="GET" class="sf-filters">
            <div>
                <label class="small text-muted mb-1 d-block">Buscar</label>
                <input type="text" name="search" class="form-control" placeholder="ID o cliente"
                       value="{{ request('search') }}">
            </div>
            <div>
                <label class="small text-muted mb-1 d-block">Fecha</label>
                <input type="date" name="search_date" class="form-control" value="{{ request('search_date') }}">
            </div>
            <div>
                <label class="small text-muted mb-1 d-block">Estado</label>
                <select name="search_status" class="form-control">
                    <option value="">Todos</option>
                    <option value="pending" @selected(request('search_status') == 'pending')>Pendiente</option>
                    <option value="in_progress" @selected(request('search_status') == 'in_progress')>En preparación</option>
                    <option value="ready_for_delivery" @selected(request('search_status') == 'ready_for_delivery')>Lista</option>
                    <option value="paid" @selected(request('search_status') == 'paid')>Por cobrar</option>
                    <option value="completed" @selected(request('search_status') == 'completed')>Completada</option>
                    <option value="cancelled" @selected(request('search_status') == 'cancelled')>Canceladas</option>
                </select>
            </div>
            <div>
                <label class="small text-muted mb-1 d-block">Tipo</label>
                <select name="search_order_type" class="form-control">
                    <option value="">Todos</option>
                    <option value="dine_in" @selected(request('search_order_type') == 'dine_in')>Local</option>
                    <option value="delivery" @selected(request('search_order_type') == 'delivery')>Envío</option>
                    <option value="pickup" @selected(request('search_order_type') == 'pickup')>Recoger</option>
                </select>
            </div>
            <div>
                <label class="small text-muted mb-1 d-block">Mesa</label>
                <select name="search_table" class="form-control">
                    <option value="">Todas</option>
                    @foreach ($tables as $table)
                        <option value="{{ $table->id }}" @selected(request('search_table') == $table->id)>{{ $table->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex flex-wrap" style="gap:8px;">
                <button type="submit" class="btn btn-primary">Buscar</button>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="sf-card">
    <div class="sf-card__head">
        <h2>{{ $orders->total() }} {{ $orders->total() === 1 ? 'orden' : 'órdenes' }}</h2>
    </div>
    <div class="sf-card__body">
        @if ($orders->count())
            <div class="sf-order-list">
                @foreach ($orders as $order)
                    @php
                        $action = $statusAction($order->status);
                        $productos = $order->items
                            ->map(fn ($item) => $item->name . ' ×' . ($item->pivot->qty ?? $item->qty ?? 1))
                            ->filter()
                            ->take(4)
                            ->join(', ') ?: 'Sin productos';
                        $tipo = match ($order->order_type) {
                            'dine_in' => 'Local' . ($order->table ? ' · ' . $order->table->name : ''),
                            'delivery' => 'Envío',
                            'pickup' => 'Para llevar',
                            default => $order->order_type,
                        };
                        $cancelled = in_array($order->status, ['cancelled_by_user', 'cancelled_by_store'], true);
                    @endphp
                    <article class="sf-order-row">
                        <div class="sf-order-row__main">
                            <div class="sf-order-row__top">
                                <span class="sf-order-row__id">#{{ $order->id }}</span>
                                <span class="sf-badge {{ $badgeClass($order->status) }}">{{ $badgeLabel($order->status) }}</span>
                            </div>
                            <strong class="sf-order-row__customer">{{ $order->user->name ?? 'Usuario eliminado' }}</strong>
                            <div class="sf-order-row__meta">{{ $order->fecha }} · {{ $tipo }}</div>
                        </div>
                        <div class="sf-order-row__products" title="{{ $productos }}">{{ $productos }}</div>
                        <div class="sf-order-row__side">
                            <div class="sf-order-row__total">${{ number_format((float) $order->total, 2) }}</div>
                            <div class="sf-order-row__actions">
                                @if (!$cancelled && $order->status !== 'completed')
                                    <form action="{{ route('orders.status', $order) }}" method="POST" class="sf-order-row__status-form status-form"
                                          data-confirm="{{ in_array($order->status, ['ready_for_delivery', 'paid'], true) ? '1' : '0' }}"
                                          data-message="{{ $order->status === 'ready_for_delivery' ? '¿Confirmas que está lista para entrega?' : ($order->status === 'paid' ? '¿Confirmas el cobro y cierre?' : '') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $action['class'] }} sf-order-row__cta">{{ $action['label'] }}</button>
                                    </form>
                                @endif
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('orders.show', $order) }}" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a class="btn btn-sm btn-outline-info" href="{{ route('orders.report', $order) }}" title="Ticket">
                                    <i class="fas fa-print"></i>
                                </a>
                                @if (auth()->user()->isAdmin())
                                    <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline delete-form"
                                          data-message="Se eliminará la orden #{{ $order->id }} permanentemente.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="sf-empty">No hay órdenes con estos filtros.</div>
        @endif
    </div>
    @if ($orders->hasPages())
        <div class="sf-card__foot">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
