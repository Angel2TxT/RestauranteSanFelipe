@extends('layouts.admin')

@section('content')
    <!-- Dropdown Card Example -->
    <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-column flex-sm-row align-items-center justify-content-between">
            <h3 class="m-0 font-weight-bold text-primary mb-2 mb-sm-0">
                Order#{{ $order->id }}
                <span class=" @if ($order->status == 'pending') badge-danger @else badge-success @endif"
                    style="padding: 5px; border-radius: 5px;">
                    @if ($order->status == 'pending')
                        Orden pendiente
                    @elseif ($order->status == 'in_progress')
                        Orden en proceso
                    @elseif ($order->status == 'ready_for_delivery')
                        Orden lista para entrega
                    @elseif ($order->status == 'paid')
                        <!-- Nuevo estado agregado -->
                        Recibir pago
                    @elseif ($order->status == 'completed')
                        Orden completada
                    @endif
                </span>
            </h3>

            <div class="d-flex flex-column flex-sm-row align-items-end mt-3 mt-sm-0">
                <a href="{{ route('orders.index') }}" class="btn btn-primary mb-2 mb-sm-0 mr-sm-2">Regresar</a>

                <!-- BOTÓN PARA AVANZAR ESTADO -->
                <a href="#"
                    class="btn btn-sm d-block text-center 
                                        @php echo $order->status == 'pending' ? 'badge-danger' : 
                                                   ($order->status == 'in_progress' ? 'badge-warning' : 
                                                   ($order->status == 'ready_for_delivery' ? 'badge-primary' : 
                                                   ($order->status == 'paid' ? 'badge-success' : 
                                                   ($order->status == 'completed' ? 'badge-secondary' : '')))); @endphp"
                    style="padding: 10px; border-radius: 15px;
                                                 @if ($order->status == 'completed') pointer-events: none; opacity: 0.6; @endif"
                    onclick="confirmarCambio(event, '{{ route('orders.status', $order) }}', '{{ $order->status }}');">

                    @if ($order->status == 'pending')
                        Comenzar pedido
                    @elseif ($order->status == 'in_progress')
                        Pedido en preparación
                    @elseif ($order->status == 'ready_for_delivery')
                        Listo para entrega
                    @elseif ($order->status == 'paid')
                        Recibir pago
                    @elseif ($order->status == 'completed')
                        Pedido completado
                    @endif
                </a>

                <!-- BOTÓN PARA REGRESAR ESTADO -->
                <!-- BOTÓN PARA REGRESAR ESTADO -->
                @if (!in_array($order->status, ['pending', 'in_progress', 'completed']))
                    <a href="#" class="btn btn-warning btn-sm d-block text-center ml-2"
                        style="padding: 10px; border-radius: 15px;"
                        onclick="revertirCambio(event, '{{ route('orders.revert', $order) }}', '{{ $order->status }}');">
                        Revertir estado
                    </a>
                @endif

            </div>

            <!-- SCRIPTS PARA CONFIRMACIONES -->
            <script>
                function confirmarCambio(event, url, status) {
                    event.preventDefault();

                    if (status === 'completed') return;

                    let mensaje = "";
                    if (status === 'ready_for_delivery') {
                        mensaje = "¿Estás seguro de que la orden está lista para entrega?";
                    } else if (status === 'paid') {
                        mensaje = "¿Confirmas que has recibido el pago?";
                    }

                    if (mensaje) {
                        Swal.fire({
                            title: "Confirmación",
                            text: mensaje,
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonText: "Sí, confirmar",
                            cancelButtonText: "Cancelar",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = url;
                            }
                        });
                    } else {
                        window.location.href = url;
                    }
                }

                function revertirCambio(event, url, status) {
                    event.preventDefault();

                    let mensaje = "";
                    if (status === 'ready_for_delivery') {
                        mensaje = "¿Seguro que quieres volver a 'Pedido en preparación'?";
                    } else if (status === 'paid') {
                        mensaje = "¿Seguro que quieres volver a 'Listo para entrega'?";
                    }

                    Swal.fire({
                        title: "Confirmación",
                        text: mensaje,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Sí, revertir",
                        cancelButtonText: "Cancelar",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    });
                }
            </script>

            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        </div>
    </div>

    <!-- Card Body -->
    <div class="card-body">

        <!-- Card Cliente -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Cliente</h6>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <div class="table-responsive">
                        <table class="table text-center w-100">
                            <thead>
                                <tr>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Apellido</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Direccion</th>
                                    <th scope="col">Telefono</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ $order->user->last_name }}</td>
                                    <td>{{ $order->user->email }}</td>
                                    <td>{{ $order->user->address }}</td>
                                    <td>{{ $order->user->phone }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- Card Detalles de la Orden -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Detalles de la orden</h6>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <div class="table-responsive">
                        <table class="table text-center w-100">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Imagen</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Precio</th>
                                    <th scope="col">Cantidad</th>
                                    <th scope="col">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td><img src="{{ asset($item->image) }}" width="50" alt="{{ $item->name }}">
                                        </td>
                                        <td>{{ $item->name }}</td>
                                        <td>${{ number_format($item->price, 2) }}</td>
                                        <td><span class="badge badge-pill badge-primary">{{ $item->pivot->qty }}</span>
                                        </td>
                                        <td>${{ number_format($item->price * $item->pivot->qty, 2) }}</td>
                                    </tr>
                                @endforeach
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

            <!-- Card Especificación del Cliente -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Especificación del cliente</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <p>{{ $order->notes }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
