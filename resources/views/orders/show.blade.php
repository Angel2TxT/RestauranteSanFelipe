@extends('layouts.admin')

@section('content')
    @php
        $statusClass = match ($order->status) {
            'pending' => 'btn-danger',
            'in_progress' => 'btn-warning',
            'ready_for_delivery' => 'btn-primary',
            'paid' => 'btn-success',
            'completed' => 'btn-secondary',
            default => 'btn-light',
        };

        $statusLabel = match ($order->status) {
            'pending' => 'Comenzar pedido',
            'in_progress' => 'Pedido en preparación',
            'ready_for_delivery' => 'Listo para entrega',
            'paid' => 'Recibir pago',
            'completed' => 'Pedido completado',
            default => $order->status,
        };

        $badgeLabel = match ($order->status) {
            'pending' => 'Orden pendiente',
            'in_progress' => 'Orden en proceso',
            'ready_for_delivery' => 'Orden lista para entrega',
            'paid' => 'Recibir pago',
            'completed' => 'Orden completada',
            default => $order->status,
        };
    @endphp

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-column flex-sm-row align-items-center justify-content-between">
            <h3 class="m-0 font-weight-bold text-primary mb-2 mb-sm-0">
                Orden #{{ $order->id }}
                <span class="badge {{ $order->status === 'pending' ? 'badge-danger' : 'badge-success' }}"
                    style="padding: 5px; border-radius: 5px;">{{ $badgeLabel }}</span>
            </h3>

            <div class="d-flex flex-column flex-sm-row align-items-end mt-3 mt-sm-0">
                <a href="{{ route('orders.index') }}" class="btn btn-primary mb-2 mb-sm-0 mr-sm-2">Regresar</a>

                @if ($order->status === 'completed')
                    <button type="button" class="btn btn-sm {{ $statusClass }} mb-2 mb-sm-0" disabled>
                        {{ $statusLabel }}
                    </button>
                @else
                    <form action="{{ route('orders.status', $order) }}" method="POST" class="mb-2 mb-sm-0 status-form"
                        data-confirm="{{ in_array($order->status, ['ready_for_delivery', 'paid']) ? '1' : '0' }}"
                        data-message="{{ $order->status === 'ready_for_delivery' ? '¿Confirmas que está lista para entrega?' : ($order->status === 'paid' ? '¿Confirmas que recibiste el pago?' : '') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $statusClass }}">
                            {{ $statusLabel }}
                        </button>
                    </form>
                @endif

                @if (!in_array($order->status, ['pending', 'in_progress', 'completed']))
                    <form action="{{ route('orders.revert', $order) }}" method="POST" class="ml-sm-2 mb-2 mb-sm-0 status-form"
                        data-confirm="1"
                        data-message="{{ $order->status === 'ready_for_delivery' ? '¿Seguro que quieres volver a preparación?' : '¿Seguro que quieres volver a listo para entrega?' }}">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm">Revertir estado</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @if (session('msg'))
        <div class="alert alert-success">{{ session('msg') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Cliente</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-center w-100">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Email</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $order->user->name ?? 'N/D' }}</td>
                            <td>{{ $order->user->last_name ?? 'N/D' }}</td>
                            <td>{{ $order->user->email ?? 'N/D' }}</td>
                            <td>{{ $order->delivery_address ?: ($order->user->address ?? 'N/D') }}</td>
                            <td>{{ $order->user->phone ?? 'N/D' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detalles de la orden</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-center w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($order->items as $item)
                            @php $qty = $item->pivot->qty ?? $item->qty ?? 1; @endphp
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    <img src="{{ asset($item->image ?: 'images/no-image.jpg') }}" width="50"
                                        alt="{{ $item->name }}">
                                </td>
                                <td>{{ $item->name }}</td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td><span class="badge badge-pill badge-primary">{{ $qty }}</span></td>
                                <td>${{ number_format($item->price * $qty, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">Sin productos</td>
                            </tr>
                        @endforelse
                        <tr>
                            <td colspan="4"></td>
                            <td><strong>Total:</strong></td>
                            <td><strong>${{ number_format($order->total, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Especificación del cliente</h6>
        </div>
        <div class="card-body text-center">
            <p>{{ $order->notes ?: 'Sin notas' }}</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.status-form').forEach((form) => {
            form.addEventListener('submit', function (event) {
                if (form.dataset.confirm !== '1') {
                    return;
                }

                event.preventDefault();
                Swal.fire({
                    title: 'Confirmación',
                    text: form.dataset.message || '¿Confirmas esta acción?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, confirmar',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
