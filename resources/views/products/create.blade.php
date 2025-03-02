@extends('layouts.admin')

@section('content')
<div class="row col-md-8 offset-md-2">
    <div class="card shadow">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h3 class="m-0 font-weight-bold text-primary"> Crear</h3>
            <a href="{{route('products.index')}}" class="btn btn-primary">Volver</a>
        </div>
        <div class="card-body">

            <x-errors />

            <form method="POST" action="{{route('products.store')}}" class="form-row" enctype="multipart/form-data">

                @csrf

                <div class="form-group col-md-6">
                    <label for="name">Nombre*</label>
                    <input class="form-control" id="name" type="text" value="{{old('name')}}" name="name">

                </div>

                <div class="form-group col-md-6">

                    <label for="description">Descripcion*</label>
                    <input class="form-control" id="description" type="text" value="{{old('description')}}" name="description">

                </div>

                <div class="form-group col-md-4">

                    <label for="price">Precio*</label>
                    <input class="form-control" id="price" type="number" value="{{old('price')}}" name="price">

                </div>
                
                <div class="form-group col-md-5">
                    <label for="category">Categoria</label>
                    <select class="form-control" name="category" id="category">
                        <option value="">Seleccionar...</option>
                        @foreach ($categories as $category)
                            <option value="{{$category->id}}">
                                {{$category->name}}
                            </option>
                            
                        @endforeach

                    </select>

                </div>
                

                <div class="col-12">
                    <div class="form-wrap">
                        <label for="image">Imagen</label>
                        <input type="file" class="form-control-file" name="image" id="image">
                    </div>
                </div>


                <button type="submit" class="btn btn-primary mt-4 float-right">
                    Crear
                </button>

            </form>
       

        </div>


    </div>
</div>

@endsection