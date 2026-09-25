@extends('layouts.admin')

@section('title', 'Deslizadores')
@section('page_title', 'Deslizadores')

@section('content')
<div class="sf-page-head">
    <div>
        <h1>Deslizadores</h1>
        <p>Banners del inicio</p>
    </div>
    <div class="sf-page-actions">
        <a href="{{ route('sliders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Crear deslizador
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
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Producto</th>
                        <th>Botón</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sliders as $slider)
                        <tr>
                            <td>{{ $slider->id }}</td>
                            <td>
                                <img class="sf-thumb" src="{{ asset($slider->image ?: 'images/no-image.jpg') }}" alt="{{ $slider->title }}">
                            </td>
                            <td class="font-weight-bold">{{ $slider->title }}</td>
                            <td class="text-left">{{ \Illuminate\Support\Str::limit($slider->description, 50) }}</td>
                            <td>{{ $slider->link }}</td>
                            <td>{{ $slider->text_link }}</td>
                            <td>
                                <a class="btn btn-primary btn-sm" href="{{ route('sliders.edit', $slider) }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('sliders.destroy', $slider) }}" method="POST" class="d-inline delete-form">
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
                            <td colspan="7" class="sf-empty">Sin deslizadores</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($sliders->hasPages())
        <div class="sf-card__foot">{{ $sliders->links() }}</div>
    @endif
</div>
@endsection
