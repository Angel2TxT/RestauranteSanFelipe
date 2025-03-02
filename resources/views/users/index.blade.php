@extends('layouts.admin')

@section('content')
<div class="card shadow">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h3 class="m-0 font-weight-bold text-primary"> Usarios</h3>
        <a href="{{route('users.create')}}" class="btn btn-primary">Crear</a>

    </div>
    <div class="card-body">

        <table class="table text-center">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Image</th>

                    <th scope="col">Nombre</th>
                    <th scope="col">Apellido</th>
                    <th scope="col">Email</th>
                    <th scope="col">Direccion</th>
                    <th scope="col">Telefono</th>

                    <th scope="col">Rol</th>

                    <th scope="col">...</th>
                    <th scope="col">...</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <th scope="row">{{$user->id}}</th>
                    <td>
                        <img src="{{ asset($user->image ?: 'images/no-image.png') }}" width="60" />
                    </td>
                    <td>{{$user->name}}</td>
                    <td>{{$user->last_name}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->address}}</td>
                    <td>{{$user->phone}}</td>
                    <td style="text-align: center; vertical-align: middle;">
                        <div style=" background-color: {{ $user->admin ? 'blue' : 'yellow' }}; color: {{ $user->admin ? 'white' : 'black' }}; padding: 5px 10px; border-radius: 5px; display: inline-block;">
                            {{ $user->admin ? 'Admin' : 'Cliente' }}
                        </div>
                    </td>
                    

                    <td>
                        <a class="btn btn-primary btn-sm" href="{{route('users.edit',$user->id)}}">
                            <span class="fas fa-edit"></span>
                        </a>
                    </td>
                    <td>
                        <form action='{{route('users.destroy',$user->id)}}' method="POST" class="confirm-form mb-0">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger btn-sm">
                                <span class="fas fa-trash"></span>
                            </button>

                        </form>
                    </td>
                </tr>
                @endforeach


            </tbody>
        </table>
    </div>
    <div class="card-footer">

        {{$users->links()}}


    </div>
</div>
@endsection
