@extends('layouts.admin')

@section('title', 'Productos')
@section('page_title', 'Productos')

@section('content')
<div class="sf-page-head">
    <div>
        <h1>Productos</h1>
        <p>Catálogo del menú</p>
    </div>
    <div class="sf-page-actions">
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Crear producto
        </a>
    </div>
</div>

<div class="sf-card">
    <div class="sf-card__body p-0">
        <div class="table-responsive">
            <table class="table sf-table text-center mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Categoría</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>
                                <img class="sf-thumb" src="{{ asset($product->image ?: 'images/no-image.jpg') }}" alt="{{ $product->name }}">
                            </td>
                            <td class="text-left font-weight-bold">{{ $product->name }}</td>
                            <td class="text-left">{{ \Illuminate\Support\Str::limit($product->description, 60) }}</td>
                            <td>${{ number_format((float) $product->price, 2) }}</td>
                            <td>{{ $product->category->name ?? 'Sin categoría' }}</td>
                            <td>
                                <a class="btn btn-primary btn-sm" href="{{ route('products.edit', $product) }}" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="sf-empty">Sin productos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($products->hasPages())
        <div class="sf-card__foot">{{ $products->links() }}</div>
    @endif
</div>
@endsection
