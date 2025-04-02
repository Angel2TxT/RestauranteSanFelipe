@extends('layouts.admin')

@section('content')
    <div class="card shadow">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h3 class="m-0 font-weight-bold text-primary"> Usuarios</h3>

            <a href="{{ route('users.create') }}" class="btn btn-primary">Crear</a>

        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-center">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Image</th>

                            <th scope="col">Nombre</th>
                            <th scope="col">Apellido</th>
                            <th scope="col">Email</th>
                            <th scope="col">Direccion</th>
                            <th scope="col">Telefono</th>

                            <th scope="col">Rol</th>
                            <th scope="col">Reporte</th>
                            <th scope="col">Editar</th>
                            <th scope="col">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <th scope="row">{{ $user->id }}</th>
                                <td>
                                    <img src="{{ asset($user->image ?: 'images/no-image.png') }}" width="60" />
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->address }}</td>
                                <td>{{ $user->phone }}</td>
                                <td style="text-align: center; vertical-align: middle;" class="btn btn-sm">
                                    <div
                                        style="
                                        background-color: 
                                            @if ($user->role == 1) blue 
                                            @elseif($user->role == 2) green 
                                            @elseif($user->role == 3) orange 
                                            @else purple @endif; 
                                        color: white; 
                                        padding: 5px 10px; 
                                        border-radius: 5px; 
                                        display: inline-block;">
                                        @switch($user->role)
                                            @case(1)
                                                Administrador
                                            @break

                                            @case(2)
                                                Empleado
                                            @break

                                            @case(3)
                                                Repartidor
                                            @break

                                            @default
                                                Cliente
                                        @endswitch
                                    </div>

                                </td>
                                <td>
                                    <a href="{{ route('reports.userReport', $user->id) }}"
                                        class="btn btn-info btn-sm">Generar Reporte</a>


                                </td>

                                <td>
                                    <a class="btn btn-primary btn-sm" href="{{ route('users.edit', $user->id) }}">
                                        <span class="fas fa-edit"></span>
                                    </a>
                                </td>
                                <td>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                        class="confirm-form mb-0" onsubmit="return confirmDelete(this, event);">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <span class="fas fa-trash"></span>
                                        </button>
                                    </form>
                                </td>

                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                <script>
                                    function confirmDelete(form, event) {
                                        event.preventDefault(); // Evita que el formulario se envíe de inmediato

                                        Swal.fire({
                                            title: "¿Estás seguro?",
                                            text: "Esta acción no se puede deshacer.",
                                            icon: "warning",
                                            showCancelButton: true,
                                            confirmButtonColor: "#d33",
                                            cancelButtonColor: "#3085d6",
                                            confirmButtonText: "Sí, eliminar",
                                            cancelButtonText: "Cancelar"
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                form.submit(); // Si el usuario confirma, enviamos el formulario
                                            }
                                        });

                                        return false; // Evita el envío automático del formulario
                                    }
                                </script>





                            </tr>
                        @endforeach


                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">

            {{ $users->links() }}


        </div>
    </div>
@endsection
