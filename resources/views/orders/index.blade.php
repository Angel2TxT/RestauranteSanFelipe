@extends('layouts.admin')

@section('content')
    <div class="card shadow">
        <div class="card-header py-3 d-flex flex-column flex-md-row align-items-center justify-content-between">
            <h3 class="m-0 font-weight-bold text-primary mb-3 mr-3 mb-md-0">Órdenes</h3>

            <form action="{{ route('orders.index') }}" method="GET" class="d-flex flex-column flex-md-row w-100">
                <input type="text" name="search" class="form-control mb-2 mb-md-0 col-12 col-md-3 mr-md-2"
                    placeholder="Busca nombre, usuario o ID" value="{{ request()->get('search') }}">
                <input type="date" name="search_date" class="form-control mb-2 mb-md-0 mr-md-2"
                    value="{{ request()->get('search_date') }}">

                <select name="search_status" class="form-control col-md-2 mb-2 mb-md-0 mr-md-2">
                    <option value="">Estado</option>
                    <option value="pending" @selected(request('search_status') == 'pending')>Pendiente</option>
                    <option value="in_progress" @selected(request('search_status') == 'in_progress')>En proceso</option>
                    <option value="ready_for_delivery" @selected(request('search_status') == 'ready_for_delivery')>Entregar</option>
                    <option value="paid" @selected(request('search_status') == 'paid')>Pagado</option>
                    <option value="completed" @selected(request('search_status') == 'completed')>Completado</option>
                </select>

                <select name="search_order_type" class="form-control mb-2 mb-md-0 col-12 col-md-2 mr-md-2">
                    <option value="">Tipo de Orden</option>
                    <option value="dine_in" @selected(request('search_order_type') == 'dine_in')>Local</option>
                    <option value="delivery" @selected(request('search_order_type') == 'delivery')>Envío</option>
                    <option value="pickup" @selected(request('search_order_type') == 'pickup')>Recoger</option>
                </select>

                <select name="search_table" class="form-control mb-2 mb-md-0 col-12 col-md-1 mr-md-2">
                    <option value="">Mesa</option>
                    @foreach ($tables as $table)
                        <option value="{{ $table->id }}" @selected(request('search_table') == $table->id)>
                            {{ $table->name }}
                        </option>
                    @endforeach
                </select>

                <div class="d-flex flex-column flex-md-row align-items-start mt-2 mt-md-0">
                    <button type="submit" class="btn btn-primary mb-2 mb-md-0 mr-md-2">Buscar</button>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Limpiar</a>
                </div>
            </form>
        </div>

        <div class="card-body">
            @if (session('msg'))
                <div class="alert alert-success">{{ session('msg') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table text-center align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Total</th>
                            <th>Usuario</th>
                            <th>Fecha</th>
                            <th>Productos</th>
                            <th>Tipo</th>
                            <th>Avanzar estado</th>
                            <th>Ver</th>
                            <th>Eliminar</th>
                            <th>Imprimir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
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

                                $productos = $order->items
                                    ->map(fn ($item) => $item->name . ' (' . ($item->pivot->qty ?? $item->qty ?? 1) . ')')
                                    ->filter()
                                    ->join(', ') ?: 'Sin productos';

                                $tipo = match ($order->order_type) {
                                    'dine_in' => 'Local' . ($order->table ? ', ' . $order->table->name : ''),
                                    'delivery' => 'Envío',
                                    'pickup' => 'Recoger',
                                    default => $order->order_type,
                                };
                            @endphp
                            <tr>
                                <th>{{ $order->id }}</th>
                                <td>${{ number_format($order->total, 2) }}</td>
                                <td>{{ $order->user->name ?? 'Usuario eliminado' }}</td>
                                <td>{{ $order->fecha }}</td>
                                <td>{{ $productos }}</td>
                                <td>{{ $tipo }}</td>
                                <td>
                                    @if ($order->status === 'completed')
                                        <button type="button" class="btn btn-sm {{ $statusClass }}" disabled>
                                            {{ $statusLabel }}
                                        </button>
                                    @else
                                        <form action="{{ route('orders.status', $order) }}" method="POST" class="d-inline status-form"
                                            data-confirm="{{ in_array($order->status, ['ready_for_delivery', 'paid']) ? '1' : '0' }}"
                                            data-message="{{ $order->status === 'ready_for_delivery' ? '¿Confirmas que está lista para entrega?' : ($order->status === 'paid' ? '¿Confirmas que recibiste el pago?' : '') }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-primary btn-sm" href="{{ route('orders.show', $order) }}" title="Ver detalle">
                                        <span class="fas fa-eye"></span>
                                    </a>
                                </td>
                                <td>
                                    <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                            <span class="fas fa-trash"></span>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ route('orders.report', $order) }}" class="btn btn-info btn-sm">Imprimir</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">Sin registros</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $orders->links() }}
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

        document.querySelectorAll('.delete-form').forEach((form) => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta acción eliminará la orden permanentemente.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
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
