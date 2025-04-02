@extends('layouts.default')

@section('content')

<section class="bg-gray-7">
    <div class="breadcrumbs-custom box-transform-wrap context-dark">
      <div class="container">
        <h3 class="breadcrumbs-custom-title">Mis Ordenes</h3>
        <div class="breadcrumbs-custom-decor"></div>
      </div>
      <div class="box-transform" style="background-image: url({{asset('images/bg-1.jpg')}});"></div>
    </div>
    <div class="container">
      <ul class="breadcrumbs-custom-path">
        <li><a href="{{route('home')}}">Inicio</a></li>
        <li class="active"><a href="{{route('orders.my')}}">Ordenes</a></li>
      </ul>
    </div>
  </section>

  <div class="container mb-4">

    <!-- Dropdown Card Example -->
    <div class="card mb-4 mt-4">
      <!-- Card Header - Dropdown -->
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Ordenes</h6>

      </div>
      <!-- Card Body -->
      <div class="card-body">

        <table class="table text-center">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Total</th>
              <th scope="col">Fecha</th>
              <th scope="col">Estado</th>

            </tr>
          </thead>
          <tbody>
            @foreach ($orders as $order)
            <tr>
                <th>{{$order->id}}</th>
                <td>${{$order->total}}</td>
                <td>{{$order->fecha}}</td>
                <td>
                  <span class="d-block text-center @if ($order->status == 'pending') badge-danger @else badge-success @endif" style="padding: 10px; border-radius: 15px;">

                    @if ($order->status == 'pending') 
                    Orden pendiente 
                    @elseif ($order->status == 'in_progress')
                    Tu orden esta en preparación
                    @elseif ($order->status == 'ready_for_delivery')
                    Tu orden esta lista
                    @elseif ($order->status == 'paid') 
                    Procede a pagar
                    @elseif ($order->status == 'completed')
                    Completado
                  @endif
    
                  </span>
                </td>

              </tr>
            @endforeach
              
            
          </tbody>
        </table>
        <!-- Paginacion -->
      </div>
    </div>

  </div>
@endsection
