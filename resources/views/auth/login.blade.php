@extends('layouts.default')

@section('content')
<div class="container mt-4 mb-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <h4 class="card-header">Inicio de sesion</h4>

                <x-errors />

                <div class="card-body">
                    <form class="rd-form" method="POST" action="{{ route('login') }}">
                        @csrf
                       
                        <div class="row row-20 gutters-20">


                            <div class="col-md-6">
                                <div class="form-wrap">
                                    <input class="form-input"  type="email" name="email" value="{{ old('email') }}" id="email" autocomplete="email" autofocus>
                                    <label class="form-label" for="email">Tu E-mail*</label>
  
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-wrap">
                                    <input class="form-input" type="password"  name="password" id="password">
                                    <label class="form-label" for="password">Tu Contraseña*</label>
   
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-wrap">
                                    <div class="form-check ml-4">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
    
                                        <label class="form-check-label" for="remember">
                                            Recuerdame
                                        </label>
                                    </div>
                            
                                </div>
                            </div>


                        </div>
                        <button class="button button-secondary button-winona" type="submit">Iniciar sesion</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.addEventListener("keydown", function (event) {
            if (event.getModifierState("CapsLock")) {
                showCapsLockWarning(true);
            } else {
                showCapsLockWarning(false);
            }
        });

        document.addEventListener("keyup", function (event) {
            if (!event.getModifierState("CapsLock")) {
                showCapsLockWarning(false);
            }
        });

        function showCapsLockWarning(show) {
            let warning = document.getElementById("caps-lock-warning");

            if (!warning) {
                warning = document.createElement("div");
                warning.id = "caps-lock-warning";
                warning.style.position = "fixed";
                warning.style.bottom = "20px";
                warning.style.right = "20px";
                warning.style.backgroundColor = "red";
                warning.style.color = "white";
                warning.style.padding = "10px";
                warning.style.borderRadius = "5px";
                warning.style.boxShadow = "0 0 10px rgba(0,0,0,0.5)";
                warning.style.display = "none";
                warning.innerText = "⚠️ Bloq Mayús está activado";
                document.body.appendChild(warning);
            }

            warning.style.display = show ? "block" : "none";
        }
    });
</script>
