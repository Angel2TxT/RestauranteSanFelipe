@extends('layouts.admin')

@section('title', 'Crear categoría')
@section('page_title', 'Crear categoría')

@section('content')
<div class="sf-page-head">
    <div>
        <h1>Crear categoría</h1>
        <p>Nueva sección del menú</p>
    </div>
    <div class="sf-page-actions">
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>

<div class="sf-card">
    <div class="sf-card__body">
        <x-errors />
        <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="name">Nombre*</label>
                    <input class="form-control" id="name" type="text" value="{{ old('name') }}" name="name" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="image">Imagen*</label>
                    <input type="file" class="form-control-file" name="image" id="image" accept="image/*" required>
                </div>
            </div>
            <div class="form-group">
                <x-icon-picker :selected="old('icon')" />
            </div>
            <div class="text-right">
                <button type="submit" class="btn btn-primary">Crear</button>
            </div>
        </form>
    </div>
</div>
@endsection
