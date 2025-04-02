@extends('layouts.default')

@section('content')

<section class="bg-gray-7">
    <div class="breadcrumbs-custom box-transform-wrap context-dark">
        <div class="container">
            <h3 class="breadcrumbs-custom-title">Productos</h3>
            <div class="breadcrumbs-custom-decor"></div>
        </div>
        <div class="box-transform" style="background-image: url(images/bg-1.jpg);"></div>
    </div>
    <div class="container">
        <ul class="breadcrumbs-custom-path">
            <li><a href="#">Inicio</a></li>
            <li class="active"><a href="#">Productos</a></li>
        </ul>
    </div>
</section>


<!-- Formulario de búsqueda, filtro y ordenar -->
<section class="section section-lg bg-default">
    <div class="container">
        <form method="GET" action="{{ route('shop') }}" id="shop-form">
            <div class="row justify-content-center">

              <!-- Ordenar por precio -->
              <div class="col-md-2 mr-5">
                <select name="sort" class="form-control custom-select" onblur="submitForm()">
                    <option value="">Ordenar por precio</option>
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Menor precio</option>
                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Mayor precio</option>
                </select>
            </div>
              
                <!-- Filtro por categoría -->
                <div class="col-md-2 ml-3 mr-5">
                    <select name="category" class="form-control custom-select" >
                        <option value="">Filtrar por categoría</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buscador de productos -->
                <div class="col-md-6 ml-3 ">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Buscar producto..." value="{{ request('search') }}" onblur="submitForm()" />
                        <div class="input-group-append">
                            <button class="btn btn-primary ml-2 btn-lg" type="submit">
                              
                                <i class="fas fa-search"></i>
                            </button>
                            <button  class="btn btn-primary btn-md" onclick="clearSearch()">Limpiar</button>
                        </div>
                    </div>
                </div>

                

                
            </div>
        </form>
    </div>
</section>

<!-- Productos -->
<section class="section section-lg bg-default">
    <div class="container">
        <div class="row row-lg row-30">
            @foreach ($products as $product)
                <x-product :$product />
            @endforeach
        </div>
        <div class="mt-4">
            {{$products->links()}} <!-- Paginación -->
        </div>
    </div>
</section>

@endsection


<script>
    // Función para limpiar los campos de búsqueda
    function clearSearch() {
        // Limpiar el campo de búsqueda y el filtro de categoría
        document.querySelector('input[name="search"]').value = '';
        document.querySelector('select[name="category"]').value = '';
        document.querySelector('select[name="sort"]').value = '';

        // Volver a cargar la página sin parámetros de búsqueda
        document.getElementById('shop-form').submit();
    }
</script>
