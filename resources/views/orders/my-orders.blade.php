@extends('layouts.default')

@section('content')
@php
  $statusMeta = [
    'pending' => ['label' => 'Pendiente', 'hint' => 'Aún puedes editar o cancelar', 'class' => 'is-pending'],
    'in_progress' => ['label' => 'En preparación', 'hint' => 'Estamos cocinando', 'class' => 'is-progress'],
    'ready_for_delivery' => ['label' => 'Lista', 'hint' => 'Tu orden está lista', 'class' => 'is-ready'],
    'paid' => ['label' => 'Por pagar', 'hint' => 'Procede a pagar', 'class' => 'is-paid'],
    'completed' => ['label' => 'Completada', 'hint' => 'Pedido finalizado', 'class' => 'is-done'],
    'cancelled_by_user' => ['label' => 'Cancelada', 'hint' => 'Cancelaste este pedido', 'class' => 'is-cancelled'],
    'cancelled_by_store' => ['label' => 'Cancelada', 'hint' => 'El restaurante canceló el pedido', 'class' => 'is-cancelled'],
  ];

  $orderTypeLabel = [
    'dine_in' => 'En mesa',
    'delivery' => 'A domicilio',
    'pickup' => 'Para llevar',
  ];
@endphp

<section class="orders-page">
  <div class="orders-hero" style="background-image: linear-gradient(120deg, rgba(70, 50, 138, 0.92), rgba(21, 21, 21, 0.55)), url({{ asset('images/bg-1.jpg') }});">
    <div class="container">
      <nav class="orders-breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('home') }}">Inicio</a>
        <span>/</span>
        <strong>Mis órdenes</strong>
      </nav>

      <div class="orders-hero-content">
        <div class="orders-hero-text">
          <h1 class="orders-hero-title">Mis órdenes</h1>
          <p class="orders-hero-meta">
            {{ $orders->total() }}
            {{ $orders->total() === 1 ? 'pedido registrado' : 'pedidos registrados' }}
          </p>
        </div>
        <a class="orders-hero-cta" href="{{ route('shop') }}">Ordenar más</a>
      </div>
    </div>
  </div>

  <div class="orders-body">
    <div class="container">
      @if ($orders->count())
        <div class="orders-list">
          @foreach ($orders as $order)
            @php
              $meta = $statusMeta[$order->status] ?? ['label' => $order->status, 'hint' => '', 'class' => 'is-pending'];
              $flow = ['pending', 'in_progress', 'ready_for_delivery', 'paid', 'completed'];
              $stepIndex = array_search($order->status, $flow, true);
              if ($stepIndex === false) {
                $stepIndex = 0;
              }
              $itemsCount = $order->items->sum(function ($item) {
                return (int) ($item->pivot->qty ?? 1);
              });
              $type = $orderTypeLabel[$order->order_type] ?? null;
            @endphp

            <article class="order-card">
              <header class="order-card__head">
                <div class="order-card__id">
                  <span class="order-card__hash">#{{ $order->id }}</span>
                  <div class="order-card__meta">
                    <time datetime="{{ $order->fecha }}">{{ $order->fecha }}</time>
                    @if ($type)
                      <span>{{ $type }}</span>
                    @endif
                    <span>{{ $itemsCount }} {{ $itemsCount === 1 ? 'producto' : 'productos' }}</span>
                  </div>
                </div>

                <div class="order-card__side">
                  <span class="order-status {{ $meta['class'] }}" title="{{ $meta['hint'] }}">
                    {{ $meta['label'] }}
                  </span>
                  <strong class="order-card__total">${{ number_format((float) $order->total, 2) }}</strong>
                </div>
              </header>

              @if ($order->delivery_address)
                <p class="order-card__address">
                  <i class="mdi mdi-map-marker"></i>
                  {{ $order->delivery_address }}
                </p>
              @endif

              <div class="order-card__items">
                @foreach ($order->items as $item)
                  @php $qty = (int) ($item->pivot->qty ?? 1); @endphp
                  <div class="order-item" data-order-item="{{ $item->id }}">
                    <img src="{{ asset($item->image ?: 'images/no-image.jpg') }}" alt="{{ $item->name }}" width="48" height="48">
                    <div class="order-item__info">
                      <strong>{{ $item->name }}</strong>
                      <span class="order-item__price-line">x<span class="js-order-qty-label">{{ $qty }}</span> · ${{ number_format((float) $item->price, 2) }}</span>
                      @if ($order->status === 'pending')
                        <div class="order-item__controls">
                          <div class="cart-qty order-qty">
                            <button type="button" class="cart-qty-btn js-order-qty-step" data-step="-1" aria-label="Menos">−</button>
                            <input
                              type="number"
                              class="cart-qty-input js-order-qty"
                              min="1"
                              max="50"
                              value="{{ $qty }}"
                              data-url="{{ route('orders.items.update', [$order, $item]) }}"
                            >
                            <button type="button" class="cart-qty-btn js-order-qty-step" data-step="1" aria-label="Más">+</button>
                          </div>
                          <button
                            type="button"
                            class="order-item__remove js-order-item-remove"
                            data-url="{{ route('orders.items.remove', [$order, $item]) }}"
                            aria-label="Quitar {{ $item->name }}"
                          >
                            <i class="fas fa-trash-alt"></i>
                          </button>
                        </div>
                      @endif
                    </div>
                    <span class="order-item__sub js-order-item-sub">${{ number_format((float) $item->price * $qty, 2) }}</span>
                  </div>
                @endforeach
              </div>

              @if ($order->notes)
                <p class="order-card__notes">
                  <i class="mdi mdi-note-text"></i>
                  {{ $order->notes }}
                </p>
              @endif

              <footer class="order-card__foot">
                <span class="order-card__hint">{{ $meta['hint'] }}</span>
                @if (!in_array($order->status, ['cancelled_by_user', 'cancelled_by_store'], true))
                  <div class="order-card__steps" aria-hidden="true">
                    @foreach ($flow as $i => $step)
                      <span class="order-step {{ $i <= $stepIndex ? 'is-on' : '' }}"></span>
                    @endforeach
                  </div>
                @endif
              </footer>

              @if ($order->status === 'pending')
                <div class="order-card__actions">
                  <a class="order-card__btn order-card__btn--primary" href="{{ route('orders.edit.shop', $order) }}">
                    Agregar productos
                  </a>
                  <form action="{{ route('orders.cancel', $order) }}" method="POST" class="js-order-cancel-form">
                    @csrf
                    <button type="submit" class="order-card__btn order-card__btn--danger">Cancelar pedido</button>
                  </form>
                </div>
              @elseif ($order->status === 'completed')
                <div class="order-card__actions">
                  <a class="order-card__btn order-card__btn--primary" href="{{ route('orders.ticket', $order) }}" target="_blank" rel="noopener">
                    Descargar ticket
                  </a>
                </div>
              @endif
            </article>
          @endforeach
        </div>

        @if ($orders->hasPages())
          <div class="orders-pagination">
            {{ $orders->links() }}
          </div>
        @endif
      @else
        <div class="orders-empty">
          <div class="orders-empty__icon" aria-hidden="true">
            <i class="fas fa-shopping-bag"></i>
          </div>
          <h2>Aún no tienes órdenes</h2>
          <p>Cuando hagas un pedido, aquí verás el estado y el detalle de cada uno.</p>
          <a class="orders-empty__cta" href="{{ route('shop') }}">Ver productos</a>
        </div>
      @endif
    </div>
  </div>
</section>

@if (session('order_placed'))
  @php
    $placed = session('order_placed');
    $placedType = match ($placed['type'] ?? '') {
      'dine_in' => 'en mesa',
      'delivery' => 'a domicilio',
      'pickup' => 'para llevar',
      default => '',
    };
    $placedCount = (int) ($placed['count'] ?? 0);
    $placedCountLabel = $placedCount === 1 ? '1 producto' : $placedCount . ' productos';
  @endphp

  <div class="order-celebrate" id="order-celebrate" role="dialog" aria-modal="true" aria-labelledby="order-celebrate-title">
    <canvas class="order-celebrate__canvas" id="order-celebrate-canvas" aria-hidden="true"></canvas>
    <div class="order-celebrate__card">
      <div class="order-celebrate__badge" aria-hidden="true">
        <i class="fas fa-check"></i>
      </div>
      <p class="order-celebrate__eyebrow">¡Pedido recibido!</p>
      <h2 class="order-celebrate__title" id="order-celebrate-title">Tu orden #{{ $placed['id'] }} ya está en camino a cocina</h2>
      <p class="order-celebrate__text">
        Confirmamos {{ $placedCountLabel }}
        @if ($placedType)
          · {{ $placedType }}
        @endif
        · total <strong>${{ $placed['total'] }}</strong>
      </p>
      <p class="order-celebrate__hint">Te avisaremos cuando cambie el estado. ¡Buen provecho!</p>
      <button type="button" class="order-celebrate__btn" id="order-celebrate-close">Ver mi pedido</button>
    </div>
  </div>

  <script>
    (function () {
      var root = document.getElementById('order-celebrate');
      var canvas = document.getElementById('order-celebrate-canvas');
      var closeBtn = document.getElementById('order-celebrate-close');
      if (!root || !canvas) return;

      var ctx = canvas.getContext('2d');
      var pieces = [];
      var colors = ['#6046b6', '#ffe745', '#ff6b6b', '#4ecdc4', '#ffffff', '#f9a826'];
      var running = true;
      var start = performance.now();

      function resize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
      }

      function spawn(amount) {
        for (var i = 0; i < amount; i++) {
          pieces.push({
            x: Math.random() * canvas.width,
            y: -20 - Math.random() * canvas.height * 0.35,
            w: 6 + Math.random() * 8,
            h: 8 + Math.random() * 10,
            color: colors[(Math.random() * colors.length) | 0],
            vx: -3 + Math.random() * 6,
            vy: 2 + Math.random() * 4,
            rot: Math.random() * Math.PI,
            vr: -0.2 + Math.random() * 0.4,
            opacity: 1
          });
        }
      }

      function frame(now) {
        if (!running) return;
        var elapsed = now - start;
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (elapsed < 1800 && pieces.length < 180) {
          spawn(8);
        }

        for (var i = pieces.length - 1; i >= 0; i--) {
          var p = pieces[i];
          p.x += p.vx;
          p.y += p.vy;
          p.vy += 0.05;
          p.rot += p.vr;
          if (elapsed > 2200) p.opacity -= 0.012;

          ctx.save();
          ctx.globalAlpha = Math.max(0, p.opacity);
          ctx.translate(p.x, p.y);
          ctx.rotate(p.rot);
          ctx.fillStyle = p.color;
          ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
          ctx.restore();

          if (p.opacity <= 0 || p.y > canvas.height + 40) {
            pieces.splice(i, 1);
          }
        }

        if (elapsed < 4200 || pieces.length) {
          requestAnimationFrame(frame);
        }
      }

      function closeCelebrate() {
        running = false;
        document.body.classList.remove('order-celebrate-open');
        root.classList.add('is-closing');
        setTimeout(function () {
          root.remove();
        }, 280);
      }

      resize();
      window.addEventListener('resize', resize);
      spawn(60);
      requestAnimationFrame(frame);
      document.body.classList.add('order-celebrate-open');

      if (closeBtn) closeBtn.addEventListener('click', closeCelebrate);
      root.addEventListener('click', function (event) {
        if (event.target === root) closeCelebrate();
      });
      document.addEventListener('keydown', function onKey(event) {
        if (event.key === 'Escape') {
          document.removeEventListener('keydown', onKey);
          closeCelebrate();
        }
      });

      setTimeout(closeCelebrate, 6500);
    })();
  </script>
@endif
@endsection
