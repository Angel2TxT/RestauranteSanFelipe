@extends('layouts.admin')

@section('title', 'Editar producto')
@section('page_title', 'Editar producto')

@section('content')
<div class="sf-page-head">
    <div>
        <h1>Editar producto</h1>
        <p>{{ $product->name }}</p>
    </div>
    <div class="sf-page-actions">
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>

<div class="sf-card" style="max-width: 860px;">
    <div class="sf-card__body">
        <x-errors />
        <form method="POST" action="{{ route('products.update', $product) }}" class="form-row" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="form-group col-md-6">
                <label for="name">Nombre*</label>
                <input class="form-control" id="name" type="text" value="{{ old('name', $product->name) }}" name="name">
            </div>
            <div class="form-group col-md-6">
                <label for="description">Descripción*</label>
                <input class="form-control" id="description" type="text" value="{{ old('description', $product->description) }}" name="description">
            </div>
            <div class="form-group col-md-4">
                <label for="price">Precio*</label>
                <input class="form-control" id="price" type="number" step="0.01" value="{{ old('price', $product->price) }}" name="price">
            </div>
            <div class="form-group col-md-8">
                <label for="category">Categoría</label>
                <select class="form-control" name="category" id="category">
                    <option value="">Seleccionar...</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category', $product->category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3 text-center">
                <img class="sf-thumb" style="width:160px;height:160px;" src="{{ asset($product->image ?: 'images/no-image.jpg') }}" alt="">
            </div>
            <div class="form-group col-md-6">
                <label for="image">Cambiar imagen</label>
                <input type="file" class="form-control-file" name="image" id="image">
            </div>
            <div class="col-12 text-right">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
