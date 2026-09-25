@extends('layouts.admin')

@section('title', 'Editar usuario')
@section('page_title', 'Editar usuario')

@section('content')
<div class="sf-page-head">
    <div>
        <h1>Editar usuario</h1>
        <p>{{ $user->name }}</p>
    </div>
    <div class="sf-page-actions">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>

<div class="sf-card" style="max-width: 860px;">
    <div class="sf-card__body">
        <x-errors />
        <form method="POST" action="{{ route('users.update', $user) }}" class="form-row" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="form-group col-md-6">
                <label for="name">Nombre*</label>
                <input class="form-control" id="name" type="text" value="{{ old('name', $user->name) }}" name="name">
            </div>
            <div class="form-group col-md-6">
                <label for="last_name">Apellido</label>
                <input class="form-control" id="last_name" type="text" value="{{ old('last_name', $user->last_name) }}" name="last_name">
            </div>
            <div class="form-group col-md-6">
                <label for="email">Correo*</label>
                <input class="form-control" id="email" type="email" value="{{ old('email', $user->email) }}" name="email">
            </div>
            <div class="form-group col-md-6">
                <label for="address">Dirección</label>
                <input class="form-control" id="address" type="text" value="{{ old('address', $user->address) }}" name="address">
            </div>
            <div class="form-group col-md-6">
                <label for="password">Nueva contraseña (opcional)</label>
                <input class="form-control" id="password" type="password" name="password" autocomplete="new-password">
            </div>
            <div class="form-group col-md-6">
                <label for="password_confirmation">Confirmar contraseña</label>
                <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password">
            </div>
            <div class="form-group col-md-6">
                <label for="phone">Teléfono</label>
                <input class="form-control" id="phone" type="text" value="{{ old('phone', $user->phone) }}" name="phone">
            </div>
            <div class="form-group col-md-6">
                <label for="role">Rol</label>
                <select name="role" id="role" class="form-control">
                    <option value="0" @selected(old('role', $user->role) == 0)>Cliente</option>
                    <option value="1" @selected(old('role', $user->role) == 1)>Administrador</option>
                    <option value="2" @selected(old('role', $user->role) == 2)>Empleado</option>
                    <option value="3" @selected(old('role', $user->role) == 3)>Repartidor</option>
                </select>
            </div>
            <div class="col-md-6 text-center mb-3">
                <img class="sf-thumb" style="width:100px;height:100px;" src="{{ asset($user->image ?: 'images/no-image.jpg') }}" alt="">
            </div>
            <div class="form-group col-md-6">
                <label for="image">Foto</label>
                <input type="file" class="form-control-file" name="image" id="image">
            </div>
            <div class="col-12 text-right">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
