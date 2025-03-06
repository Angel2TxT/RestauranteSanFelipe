@extends('layouts.admin')

@section('content')
<div class="card shadow">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h3 class="m-0 font-weight-bold text-primary">Órdenes</h3>
        <!-- Botón de PDF alineado a la derecha -->
        <div class="ml-auto">
            <a href="{{ route('reports.pdf', 'orders') }}" class="btn btn-danger">Generar PDF</a>
        </div>
    </div>
    
    <div class="card-body">
        
        <div class="table-responsive"> 
            <table class="table text-center">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Total</th>
                        <th scope="col">Usuario</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Estatus</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Mesa</th>
                        <th scope="col">...</th>
                        <th scope="col">...</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                    <tr>
                        <th scope="row">{{$order->id}}</th>
                        <td>${{$order->total}}</td>
                        <td>{{$order->user->name}}</td>
                        <td>{{$order->fecha}}</td>
                        <td>
                            <span class="d-block text-center @if ($order->status == 'pending') badge-danger @else badge-success @endif" style="padding: 10px; border-radius: 15px; ">
                                @if ($order->status == 'pending') 
                                    Orden pendiente 
                                @elseif ($order->status == 'in_progress')
                                    Orden en proceso
                                @elseif ($order->status == 'ready_for_delivery')
                                    Enviado
                                @elseif ($order->status == 'completed')
                                    Completado
                                @endif
                            </span>
                        </td>

                        <td>
                            <span class="d-block text-center @if ($order->order_type == 'pending') badge-danger @else badge-success @endif" style="padding: 10px; border-radius: 15px; ">
                                @if ($order->order_type == 'dine_in') 
                                    Local
                                @elseif ($order->order_type == 'delivery')
                                    Envio
                                @elseif ($order->order_type == 'pickup')
                                    Recoger
                    
                                @endif
                            </span>
                        </td>

                        <td>
                            <span class="d-block text-center @if ($order->order_type == 'pending') badge-danger @else badge-success @endif" style="padding: 10px; border-radius: 15px; ">
                                @if ($order->order_type == 'dine_in') 
                                    {{
                                        $order->table->name
                                    }}
                                @else 
                                    No aplica
                               
                    
                                @endif
                            </span>
                        </td>

                        <td>
                            <a class="btn btn-primary btn-sm" href="{{route('orders.show',$order->id)}}">
                                <span class="fas fa-eye"></span>
                            </a>
                        </td>
                        <td>
                            <form action="{{route('orders.destroy',$order->id)}}" method="POST" class="confirm-form mb-0">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger btn-sm">
                                    <span class="fas fa-trash"></span>
                                </button>
                            </form>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="10">Sin registros</td>
                    </tr>

                    @endforelse
                </tbody>
            </table>
        </div> <!-- Cierra el div de table-responsive -->
    </div>
    <div class="card-footer">
        {{$orders->links()}}
    </div>
</div>
@endsection
