@extends('layouts.admin')

@section('title', 'Crear usuario')
@section('page_title', 'Crear usuario')

@section('content')
<div class="sf-page-head">
    <div>
        <h1>Crear usuario</h1>
        <p>Alta de cliente o personal</p>
    </div>
    <div class="sf-page-actions">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>

<div class="sf-card" style="max-width: 860px;">
    <div class="sf-card__body">
        <x-errors />
        <form method="POST" action="{{ route('users.store') }}" class="form-row" enctype="multipart/form-data">
            @csrf
            <div class="form-group col-md-6">
                <label for="name">Nombre*</label>
                <input class="form-control" id="name" type="text" value="{{ old('name') }}" name="name">
            </div>
            <div class="form-group col-md-6">
                <label for="last_name">Apellido</label>
                <input class="form-control" id="last_name" type="text" value="{{ old('last_name') }}" name="last_name">
            </div>
            <div class="form-group col-md-6">
                <label for="email">Correo*</label>
                <input class="form-control" id="email" type="email" value="{{ old('email') }}" name="email">
            </div>
            <div class="form-group col-md-6">
                <label for="address">Dirección</label>
                <input class="form-control" id="address" type="text" value="{{ old('address') }}" name="address">
            </div>
            <div class="form-group col-md-6">
                <label for="password">Contraseña*</label>
                <input class="form-control" id="password" type="password" name="password">
            </div>
            <div class="form-group col-md-6">
                <label for="password_confirmation">Confirmar contraseña*</label>
                <input class="form-control" id="password_confirmation" type="password" name="password_confirmation">
            </div>
            <div class="form-group col-md-6">
                <label for="phone">Teléfono</label>
                <input class="form-control" id="phone" type="text" value="{{ old('phone') }}" name="phone">
            </div>
            <div class="form-group col-md-6">
                <label for="role">Rol</label>
                <select name="role" id="role" class="form-control">
                    <option value="0" @selected(old('role') == '0')>Cliente</option>
                    <option value="1" @selected(old('role') == '1')>Administrador</option>
                    <option value="2" @selected(old('role') == '2')>Empleado</option>
                    <option value="3" @selected(old('role') == '3')>Repartidor</option>
                </select>
            </div>
            <div class="form-group col-12">
                <label for="image">Foto</label>
                <input type="file" class="form-control-file" name="image" id="image">
            </div>
            <div class="col-12 text-right">
                <button type="submit" class="btn btn-primary">Crear</button>
            </div>
        </form>
    </div>
</div>
@endsection
