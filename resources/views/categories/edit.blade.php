@extends('layouts.admin')

@section('content')
<div class="row col-md-10 offset-md-1">
    <div class="card shadow w-100">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h3 class="m-0 font-weight-bold text-primary">Editar categoría</h3>
            <a href="{{ route('categories.index') }}" class="btn btn-primary">Volver</a>
        </div>
        <div class="card-body">
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
                        <img src="{{ asset($category->image) }}" width="180" alt="{{ $category->name }}">
                    </div>
                @endif

                <button type="submit" class="btn btn-primary mt-3">
                    Guardar cambios
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
