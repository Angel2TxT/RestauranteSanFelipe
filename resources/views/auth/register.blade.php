@extends('layouts.default')

@section('content')
    <div class="container mt-4 mb-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <h4 class="card-header">Registrar</h4>
                    <x-errors />

                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}" class="rd-form">
                            @csrf

                            <div class="row row-20 gutters-20">

                                <div class="col-md-6">
                                    <div class="form-wrap">
                                        <label class="form-label" for="name">Nombre*</label>
                                        <input class="form-input" id="name" type="text" value="{{ old('name') }}"
                                            name="name">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-wrap">
                                        <label for="last_name" class="form-label">Apellido*</label>
                                        <input id="last_name" type="text" class="form-input" name="last_name"
                                            value="{{ old('last_name') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-wrap">
                                        <label for="email" class="form-label">E-mail*</label>
                                        <input id="email" type="email" class="form-input" name="email"
                                            value="{{ old('email') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-wrap">
                                        <label for="address" class="form-label">Dirección*</label>
                                        <input id="address" type="text" class="form-input" name="address"
                                            value="{{ old('address') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-wrap">
                                        <label for="password" class="form-label">Contraseña</label>
                                        <input id="password" type="password" class="form-input" name="password">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-wrap">
                                        <label for="password-confirm" class="form-label">Confirma contraseña</label>
                                        <input id="password-confirm" type="password" class="form-input"
                                            name="password_confirmation">
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-wrap">
                                        <label for="phone" class="form-label">Teléfono*</label>
                                        <input id="phone" type="text" class="form-input" name="phone"
                                            value="{{ old('phone') }}">
                                    </div>
                                </div>



                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Registrarse
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
