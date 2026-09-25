@extends('layouts.admin')

@section('title', 'Crear deslizador')
@section('page_title', 'Crear deslizador')

@section('content')
<div class="sf-page-head">
    <div>
        <h1>Crear deslizador</h1>
        <p>Banner del inicio</p>
    </div>
    <div class="sf-page-actions">
        <a href="{{ route('sliders.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>

<div class="sf-card" style="max-width: 860px;">
    <div class="sf-card__body">
        <x-errors />
        <form method="POST" action="{{ route('sliders.store') }}" class="form-row" enctype="multipart/form-data">
            @csrf
            <div class="form-group col-md-6">
                <label for="title">Título*</label>
                <input class="form-control" id="title" type="text" value="{{ old('title') }}" name="title">
            </div>
            <div class="form-group col-md-6">
                <label for="description">Descripción*</label>
                <input class="form-control" id="description" type="text" value="{{ old('description') }}" name="description">
            </div>
            <div class="form-group col-md-6">
                <label for="link">Nombre del producto*</label>
                <input class="form-control" id="link" type="text" value="{{ old('link') }}" name="link">
            </div>
            <div class="form-group col-md-6">
                <label for="text_link">Texto del botón*</label>
                <input class="form-control" id="text_link" type="text" value="{{ old('text_link') }}" name="text_link">
            </div>
            <div class="form-group col-12">
                <label for="image">Imagen</label>
                <input type="file" class="form-control-file" name="image" id="image">
            </div>
            <div class="col-12 text-right">
                <button type="submit" class="btn btn-primary">Crear</button>
            </div>
        </form>
    </div>
</div>
@endsection
