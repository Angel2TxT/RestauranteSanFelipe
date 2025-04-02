@extends('layouts.admin')

@section('content')
    <!-- Begin Page Content -->


        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h3 class="m-0 font-weight-bold text-primary"> Categorias</h3>
                <a href="{{route('categories.create')}}" class="btn btn-primary">Crear</a>

            </div>
            <div class="card-body">
                <div class="table-responsive"> 
                <table class="table text-center">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Imagen</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Icono</th>
                            <th scope="col">...</th>
                            <th scope="col">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                        <tr>
                            <th scope="row">{{$category->id}}</th>
                            <td>
                                <img src="{{asset($category->image)}}" width="80">

                            </td>
                            <td>{{$category->name}}</td>
                            <td>{{$category->icon}}</td>

                            <td>
                                <a class="btn btn-primary btn-sm" href="{{route('categories.edit',$category->id)}}">
                                    <span class="fas fa-edit"></span>
                                </a>
                            </td>


                            <td>
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="confirm-form mb-0" 
                                    onsubmit="return confirmDelete(this, event);" data-has-products="{{ $category->products()->count() > 0 ? 'true' : 'false' }}">
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
                                    event.preventDefault(); // Evita que el formulario se envíe inmediatamente
                            
                                    // Verifica si la categoría tiene productos asociados
                                    var hasProducts = form.getAttribute('data-has-products') === 'true';
                            
                                    if (hasProducts) {
                                        Swal.fire({
                                            title: "¡No puedes eliminar esta categoría!",
                                            text: "La categoría tiene productos asociados y no se puede eliminar.",
                                            icon: "error",
                                            confirmButtonColor: "#3085d6",
                                            confirmButtonText: "Cerrar"
                                        });
                                        return false; // No permite la eliminación
                                    }
                            
                                    // Si no tiene productos, procede con la confirmación de eliminación
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
                                            form.submit(); // Si el usuario confirma, se envía el formulario
                                        }
                                    });
                            
                                    return false; // Evita que el formulario se envíe automáticamente
                                }
                            </script>
                            
                            


                        </tr>
 
                        @empty

                        <tr>
                            <td colspan="10">Sin registros</td>
                        </tr>
                            
                        @endforelse


                    </tbody>
                </table>
                </div>
                
            </div>
            <div class="card-footer">

                {{$categories->links()}}


            </div>
        </div>


 
    <!-- /.container-fluid -->
@endsection
