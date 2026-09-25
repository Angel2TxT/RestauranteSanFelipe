@extends('layouts.admin')

@section('title', 'Categorías')
@section('page_title', 'Categorías')

@section('content')
<div class="sf-page-head">
    <div>
        <h1>Categorías</h1>
        <p>Organización del menú</p>
    </div>
    <div class="sf-page-actions">
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Crear categoría
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
                        <th>Icono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>
                                <img class="sf-thumb" src="{{ asset($category->image ?: 'images/no-image.jpg') }}" alt="{{ $category->name }}">
                            </td>
                            <td class="font-weight-bold">{{ $category->name }}</td>
                            <td>
                                <span class="linearicons-{{ $category->icon }}" style="font-size: 1.4rem;"></span>
                                <small class="d-block text-muted">{{ $category->icon }}</small>
                            </td>
                            <td>
                                <a class="btn btn-primary btn-sm" href="{{ route('categories.edit', $category) }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                      class="d-inline delete-form"
                                      data-has-products="{{ ($category->products_count ?? $category->products()->count()) > 0 ? 'true' : 'false' }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="sf-empty">Sin categorías</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($categories->hasPages())
        <div class="sf-card__foot">{{ $categories->links() }}</div>
    @endif
</div>
@endsection
