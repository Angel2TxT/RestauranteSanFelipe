@extends('layouts.admin')

@section('title', 'Editar categoría')
@section('page_title', 'Editar categoría')

@section('content')
<div class="sf-page-head">
    <div>
        <h1>Editar categoría</h1>
        <p>{{ $category->name }}</p>
    </div>
    <div class="sf-page-actions">
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>

<div class="sf-card">
    <div class="sf-card__body">
        <x-errors />
        <form method="POST" action="{{ route('categories.update', $category) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="name">Nombre*</label>
                    <input class="form-control" id="name" type="text" value="{{ old('name', $category->name) }}" name="name" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="image">Imagen</label>
                    <input type="file" class="form-control-file" name="image" id="image" accept="image/*">
                </div>
            </div>
            <div class="form-group">
                <x-icon-picker :selected="old('icon', $category->icon)" />
            </div>
            @if ($category->image)
                <div class="mb-3">
                    <label class="d-block">Imagen actual</label>
                    <img class="sf-thumb" style="width:160px;height:160px;" src="{{ asset($category->image) }}" alt="{{ $category->name }}">
                </div>
            @endif
            <div class="text-right">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
