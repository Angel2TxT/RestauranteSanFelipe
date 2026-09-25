@extends('layouts.admin')

@section('title', 'Editar deslizador')
@section('page_title', 'Editar deslizador')

@section('content')
<div class="sf-page-head">
    <div>
        <h1>Editar deslizador</h1>
        <p>{{ $slider->title }}</p>
    </div>
    <div class="sf-page-actions">
        <a href="{{ route('sliders.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>

<div class="sf-card" style="max-width: 860px;">
    <div class="sf-card__body">
        <x-errors />
        <form method="POST" action="{{ route('sliders.update', $slider) }}" class="form-row" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="form-group col-md-6">
                <label for="title">Título</label>
                <input class="form-control" id="title" type="text" value="{{ old('title', $slider->title) }}" name="title">
            </div>
            <div class="form-group col-md-6">
                <label for="description">Descripción</label>
                <input class="form-control" id="description" type="text" value="{{ old('description', $slider->description) }}" name="description">
            </div>
            <div class="form-group col-md-6">
                <label for="link">Nombre del producto</label>
                <input class="form-control" id="link" type="text" value="{{ old('link', $slider->link) }}" name="link">
            </div>
            <div class="form-group col-md-6">
                <label for="text_link">Texto del botón</label>
                <input class="form-control" id="text_link" type="text" value="{{ old('text_link', $slider->text_link) }}" name="text_link">
            </div>
            <div class="col-md-6 text-center mb-3">
                <img class="sf-thumb" style="width:160px;height:160px;" src="{{ asset($slider->image ?: 'images/no-image.jpg') }}" alt="">
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
