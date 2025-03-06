<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    // Método para mostrar el formulario de edición del perfil
    public function edit()
    {
        // Se pueden pasar datos al formulario, como el usuario autenticado
        $user = auth()->user();

        return view('profile.edit', compact('user'));
    }
}
