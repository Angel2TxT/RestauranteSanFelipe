@extends('layouts.admin')

@section('title', 'Mi perfil')
@section('page_title', 'Mi perfil')

@section('content')
<div class="sf-page-head">
    <div>
        <h1>Editar perfil</h1>
        <p>Tus datos de acceso</p>
    </div>
</div>

<div class="sf-card" style="max-width: 900px;">
    <div class="sf-card__head"><h2>Mis datos</h2></div>
    <div class="sf-card__body">
        @if ($errors->any())
            <div class="alert alert-danger sf-alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="name">Nombre</label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Apellido</label>
                        <input type="text" class="form-control" id="last_name" name="last_name"
                               value="{{ old('last_name', $user->last_name) }}">
                    </div>
                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Teléfono</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                               value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div class="form-group">
                        <label for="address">Dirección</label>
                        <input type="text" class="form-control" id="address" name="address"
                               value="{{ old('address', $user->address) }}">
                    </div>
                    <div class="form-group">
                        <label for="password">Nueva contraseña (opcional)</label>
                        <input type="password" class="form-control" id="password" name="password" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirmar contraseña</label>
                        <input type="password" class="form-control" id="password_confirmation"
                               name="password_confirmation" autocomplete="new-password">
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <label class="d-block mb-2">Foto de perfil</label>
                    <img src="{{ asset($user->image ?: 'images/no-image.jpg') }}" alt="Avatar"
                         class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                    <input type="file" class="form-control-file" name="image" id="image" accept="image/*">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="{{ auth()->user()->isAdmin() ? route('admin.home') : route('orders.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
