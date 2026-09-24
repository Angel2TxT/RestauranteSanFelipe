@extends('layouts.admin')

@section('content')
<div class="row col-md-10 offset-md-1">
    <div class="card shadow w-100">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h3 class="m-0 font-weight-bold text-primary">Crear categoría</h3>
            <a href="{{ route('categories.index') }}" class="btn btn-primary">Volver</a>
        </div>
        <div class="card-body">
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

                <button type="submit" class="btn btn-primary mt-3">
                    Crear
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
