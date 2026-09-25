@extends('layouts.admin')

@section('title', 'Inicio')
@section('page_title', 'Inicio')

@section('content')
<div class="sf-page-head">
    <div>
        <h1>Hola, {{ auth()->user()->name }}</h1>
        <p>Resumen operativo del restaurante</p>
    </div>
</div>

<div class="sf-kpi-grid">
    <div class="sf-kpi">
        <span class="sf-kpi__label">Pendientes</span>
        <span class="sf-kpi__value">{{ $stats['pending'] }}</span>
    </div>
    <div class="sf-kpi">
        <span class="sf-kpi__label">En preparación</span>
        <span class="sf-kpi__value">{{ $stats['in_progress'] }}</span>
    </div>
    <div class="sf-kpi">
        <span class="sf-kpi__label">Listas</span>
        <span class="sf-kpi__value">{{ $stats['ready_for_delivery'] }}</span>
    </div>
    <div class="sf-kpi">
        <span class="sf-kpi__label">Por cobrar</span>
        <span class="sf-kpi__value">{{ $stats['paid'] }}</span>
    </div>
    <div class="sf-kpi sf-kpi--purple">
        <span class="sf-kpi__label">Ventas de hoy</span>
        <span class="sf-kpi__value">${{ number_format($stats['sales_today'], 2) }}</span>
    </div>
    <div class="sf-kpi">
        <span class="sf-kpi__label">Órdenes hoy</span>
        <span class="sf-kpi__value">{{ $stats['orders_today'] }}</span>
    </div>
    <div class="sf-kpi">
        <span class="sf-kpi__label">Productos</span>
        <span class="sf-kpi__value">{{ $stats['products'] }}</span>
    </div>
    <div class="sf-kpi">
        <span class="sf-kpi__label">Usuarios</span>
        <span class="sf-kpi__value">{{ $stats['users'] }}</span>
    </div>
</div>

<div class="sf-card">
    <div class="sf-card__head">
        <h2>Accesos rápidos</h2>
    </div>
    <div class="sf-card__body">
        <div class="sf-shortcut-grid">
            <a class="sf-shortcut" href="{{ route('kitchen.index') }}">
                <span class="sf-shortcut__icon"><i class="fas fa-fire"></i></span>
                <span>
                    <strong>Cocina</strong>
                    <span>Pantalla en tiempo real</span>
                </span>
            </a>
            <a class="sf-shortcut" href="{{ route('cashier.index') }}">
                <span class="sf-shortcut__icon"><i class="fas fa-cash-register"></i></span>
                <span>
                    <strong>Caja</strong>
                    <span>Cobros y cierre</span>
                </span>
            </a>
            <a class="sf-shortcut" href="{{ route('orders.index') }}">
                <span class="sf-shortcut__icon"><i class="fas fa-clipboard-list"></i></span>
                <span>
                    <strong>Órdenes</strong>
                    <span>Cola y estados del día</span>
                </span>
            </a>
            <a class="sf-shortcut" href="{{ route('products.index') }}">
                <span class="sf-shortcut__icon"><i class="fas fa-utensils"></i></span>
                <span>
                    <strong>Productos</strong>
                    <span>Menú y precios</span>
                </span>
            </a>
            <a class="sf-shortcut" href="{{ route('categories.index') }}">
                <span class="sf-shortcut__icon"><i class="fas fa-tags"></i></span>
                <span>
                    <strong>Categorías</strong>
                    <span>Organizar el catálogo</span>
                </span>
            </a>
            <a class="sf-shortcut" href="{{ route('users.index') }}">
                <span class="sf-shortcut__icon"><i class="fas fa-users"></i></span>
                <span>
                    <strong>Usuarios</strong>
                    <span>Equipo y clientes</span>
                </span>
            </a>
            <a class="sf-shortcut" href="{{ route('sliders.index') }}">
                <span class="sf-shortcut__icon"><i class="fas fa-images"></i></span>
                <span>
                    <strong>Deslizadores</strong>
                    <span>Banners del inicio</span>
                </span>
            </a>
            <a class="sf-shortcut" href="{{ route('products.create') }}">
                <span class="sf-shortcut__icon"><i class="fas fa-plus"></i></span>
                <span>
                    <strong>Nuevo producto</strong>
                    <span>Agregar platillo</span>
                </span>
            </a>
        </div>
    </div>
</div>
@endsection
