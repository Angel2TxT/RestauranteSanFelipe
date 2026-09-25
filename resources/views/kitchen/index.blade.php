@extends('layouts.admin')

@section('title', 'Cocina')
@section('page_title', 'Cocina')

@section('content')
<div class="sf-kitchen" id="sf-kitchen"
     data-feed-url="{{ route('kitchen.feed') }}"
     data-csrf="{{ csrf_token() }}">
    <div class="sf-kitchen__head">
        <div>
            <h1>Pantalla de cocina</h1>
            <p>Pedidos nuevos y en preparación · se actualiza sola</p>
        </div>
        <div class="sf-kitchen__stats">
            <div class="sf-kitchen__stat sf-kitchen__stat--new">
                <span>Nuevas</span>
                <strong id="kitchen-count-pending">0</strong>
            </div>
            <div class="sf-kitchen__stat sf-kitchen__stat--cook">
                <span>Preparando</span>
                <strong id="kitchen-count-progress">0</strong>
            </div>
            <div class="sf-kitchen__live">
                <span class="sf-kitchen__dot"></span>
                <span id="kitchen-live-label">En vivo</span>
            </div>
        </div>
    </div>

    <div class="sf-kitchen__board">
        <section class="sf-kitchen__col">
            <header class="sf-kitchen__col-head sf-kitchen__col-head--new">
                <h2><i class="fas fa-bell"></i> Por comenzar</h2>
                <span id="kitchen-badge-pending">0</span>
            </header>
            <div class="sf-kitchen__cards" id="kitchen-pending">
                <div class="sf-kitchen__empty">Sin pedidos nuevos</div>
            </div>
        </section>

        <section class="sf-kitchen__col">
            <header class="sf-kitchen__col-head sf-kitchen__col-head--cook">
                <h2><i class="fas fa-fire"></i> En preparación</h2>
                <span id="kitchen-badge-progress">0</span>
            </header>
            <div class="sf-kitchen__cards" id="kitchen-progress">
                <div class="sf-kitchen__empty">Nada en cocina</div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/js/sf-kitchen.js') }}?v={{ filemtime(public_path('admin/js/sf-kitchen.js')) }}"></script>
@endpush
