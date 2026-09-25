@extends('layouts.admin')

@section('title', 'Usuarios')
@section('page_title', 'Usuarios')

@section('content')
<div class="sf-page-head">
    <div>
        <h1>Usuarios</h1>
        <p>Equipo y clientes</p>
    </div>
    <div class="sf-page-actions">
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Crear usuario
        </a>
    </div>
</div>

<div class="sf-card">
    <div class="sf-card__body p-0">
        <div class="table-responsive">
            <table class="table sf-table text-center mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        @php
                            $roleClass = match ((int) $user->role) {
                                1 => 'sf-role--admin',
                                2 => 'sf-role--employee',
                                3 => 'sf-role--delivery',
                                default => 'sf-role--client',
                            };
                            $roleLabel = match ((int) $user->role) {
                                1 => 'Administrador',
                                2 => 'Empleado',
                                3 => 'Repartidor',
                                default => 'Cliente',
                            };
                        @endphp
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <img class="sf-thumb" src="{{ asset($user->image ?: 'images/no-image.jpg') }}" alt="">
                            </td>
                            <td class="text-left">
                                <strong>{{ $user->name }} {{ $user->last_name }}</strong>
                                <div class="small text-muted">{{ $user->address }}</div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td><span class="sf-role {{ $roleClass }}">{{ $roleLabel }}</span></td>
                            <td>
                                <a href="{{ route('reports.userReport', $user->id) }}" class="btn btn-info btn-sm" title="Reporte">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <a class="btn btn-primary btn-sm" href="{{ route('users.edit', $user) }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline delete-form">
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
                            <td colspan="7" class="sf-empty">Sin usuarios</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if (method_exists($users, 'hasPages') && $users->hasPages())
        <div class="sf-card__foot">{{ $users->links() }}</div>
    @endif
</div>
@endsection
