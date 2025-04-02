@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-center align-items-center mb-4" style="height: 70vh;">
        <!-- Card for Welcome Message -->
        <div class="card shadow-lg border-0" style="width: 600px; border-radius: 20px;">
            <div class="card-body text-center">
                <!-- Icon and Title -->
                <div class="mb-4">
                    <i class="fas fa-user-circle fa-4x text-primary"></i>
                </div>
                <h2 class="font-weight-bold text-gray-800 mb-3">¡Bienvenido al Panel de Administración!</h2>
                <p class="text-gray-600 mb-4">Gestiona y controla todos los aspectos del sistema de manera eficiente y sencilla.</p>

                <!-- Optional Button for Redirection (If needed) -->
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                    Ir a Productos
                </a>
                <a href="{{ route('orders.index') }}" class="btn btn-primary btn-lg">
                    Ir a Ordenes
                </a>
                <a href="{{ route('users.index') }}" class="btn btn-primary btn-lg">
                    Ir a Usuarios
                </a>
                
            </div>
        </div>
    </div>
@endsection
