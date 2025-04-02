@extends('layouts.admin')

@section('content')
    <div class="card shadow">


        <div class="card-header py-3 d-flex flex-column flex-md-row align-items-center justify-content-between">
            <h3 class="m-0 font-weight-bold text-primary mb-3 mr-3 mb-md-0">Órdenes</h3>

            <!-- Formulario de búsqueda -->
            <form action="{{ route('orders.index') }}" method="GET" class="d-flex flex-column flex-md-row w-100">
                <input type="text" name="search" class="form-control mb-2 mb-md-0 col-12 col-md-3 mr-md-2"
                    placeholder="Busca nombre, usuario o ID" value="{{ request()->get('search') }}">
                <input type="date" name="search_date" class="form-control mb-2 mb-md-0 mr-md-2"
                    placeholder="Buscar por fecha" value="{{ request()->get('search_date') }}">

                <!-- Select para Estado -->
                <select name="search_status" class="form-control col-md-2 mb-2 mb-md-0 mr-md-2">
                    <option value="">Estado</option>
                    <option value="pending" {{ request()->get('search_status') == 'pending' ? 'selected' : '' }}>Pendiente
                    </option>
                    <option value="in_progress" {{ request()->get('search_status') == 'in_progress' ? 'selected' : '' }}>En
                        proceso</option>
                    <option value="ready_for_delivery"
                        {{ request()->get('search_status') == 'ready_for_delivery' ? 'selected' : '' }}>Entregar</option>
                    <option value="completed" {{ request()->get('search_status') == 'completed' ? 'selected' : '' }}>Pagado
                    </option>
                </select>

                <!-- Select para Tipo de Orden -->
                <select name="search_order_type" class="form-control mb-2 mb-md-0 col-12 col-md-2 mr-md-2">
                    <option value="">Tipo de Orden</option>
                    <option value="dine_in" {{ request()->get('search_order_type') == 'dine_in' ? 'selected' : '' }}>Local
                    </option>
                    <option value="delivery" {{ request()->get('search_order_type') == 'delivery' ? 'selected' : '' }}>Envío
                    </option>
                    <option value="pickup" {{ request()->get('search_order_type') == 'pickup' ? 'selected' : '' }}>Recoger
                    </option>
                </select>

                <!-- Select para Mesa -->
                <select name="search_table" class="form-control mb-2 mb-md-0 col-12 col-md-1 mr-md-2">
                    <option value="">Mesa</option>
                    @foreach ($tables as $table)
                        <option value="{{ $table->id }}"
                            {{ request()->get('search_table') == $table->id ? 'selected' : '' }}>
                            {{ $table->name }}
                        </option>
                    @endforeach
                </select>

                <div class="d-flex flex-column flex-md-row align-items-start mt-2 mt-md-0">
                    <button type="submit" class="btn btn-primary mb-2 mb-md-0 mr-md-2">Buscar</button>
                    <button type="button" class="btn btn-secondary" onclick="clearSearch()">Limpiar</button>
                </div>
            </form>

            <script>
                // Función para limpiar los campos de búsqueda
                function clearSearch() {
                    document.querySelector('input[name="search"]').value = '';
                    document.querySelector('input[name="search_date"]').value = '';
                    document.querySelector('select[name="search_status"]').value = '';
                    document.querySelector('select[name="search_order_type"]').value = '';
                    document.querySelector('select[name="search_table"]').value = '';

                    // Volver a cargar la página sin los parámetros de búsqueda
                    window.location.href = "{{ route('orders.index') }}";
                }
            </script>
        </div>


        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-center">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Total</th>
                            <th scope="col">Usuario</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Productos</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Estatus</th>
                            
                            <th scope="col">Empezar orden</th>
                            <th scope="col">Eliminar</th>
                            <th scope="col"></th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <th scope="row">{{ $order->id }}</th>
                                <td>${{ number_format($order->total, 2) }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td>{{ $order->fecha }}</td>

                                <!-- Productos (nombre y cantidad) -->
                                <td>
                                    @php
                                        $productos = $order->items
                                            ->map(function ($item) {
                                                return $item->name . ' (' . $item->pivot->qty . ')';
                                            })
                                            ->join(', ');
                                    @endphp
                                    <span class="d-block text-center"
                                        style="padding: 10px; border-radius: 15px;">{{ $productos }}</span>


                                </td>



                                



                                <!-- Tipo de Orden -->
                                <td>
                                    <span
                                        class="d-block text-center "
                                        style="padding: 10px; border-radius: 15px;">
                                        @if ($order->order_type == 'dine_in')
                                            Local, {{ $order->table->name }}
                                        @elseif ($order->order_type == 'delivery')
                                            Envío, No aplica
                                        @elseif ($order->order_type == 'pickup')
                                            Recoger, No aplica
                                        @endif
                                    </span>
                                </td>

                                <!-- Estado -->
                                <td>
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

                                </td>

                                <script>
                                    function confirmarCambio(event, url, status) {
                                        event.preventDefault(); // Evita la redirección automática

                                        // Si el estado es "completed", no hacer nada
                                        if (status === 'completed') {
                                            return;
                                        }

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
                                                    window.location.href = url; // Redirige a la ruta
                                                }
                                            });
                                        } else {
                                            window.location.href = url;
                                        }
                                    }
                                </script>


                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                                <!-- Botón de ver detalles -->
                                <td>
                                    <a class="btn btn-primary btn-sm" href="{{ route('orders.show', $order->id) }}">
                                        <span class="fas fa-eye"></span>
                                    </a>
                                </td>

                                <!-- Botón de eliminar -->
                                <td>
                                    <form action="{{ route('orders.destroy', $order->id) }}" method="POST"
                                        class="confirm-form mb-0" onsubmit="return confirmDelete(this, event);">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <span class="fas fa-trash"></span>
                                        </button>
                                    </form>
                                </td>
                                
                                <script>
                                    function confirmDelete(form, event) {
                                        event.preventDefault(); // Evita que el formulario se envíe automáticamente
                                
                                        Swal.fire({
                                            title: "¿Estás seguro?",
                                            text: "Esta acción eliminará la orden permanentemente.",
                                            icon: "warning",
                                            showCancelButton: true,
                                            confirmButtonColor: "#d33",
                                            cancelButtonColor: "#3085d6",
                                            confirmButtonText: "Sí, eliminar",
                                            cancelButtonText: "Cancelar"
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                form.submit(); // Envía el formulario si el usuario confirma
                                            }
                                        });
                                    }
                                </script>

                                <!-- Botón de imprimir -->
                                <td>
                                    <a href="{{ route('orders.report', $order->id) }}"
                                        class="btn btn-info btn-sm">Imprimir</a>
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
@endsection
