<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('id','desc')->paginate(3);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        App::setLocale('es');
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed|max:255',
            'image' => 'image|mimes:jpeg,png|max:1024|nullable'
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debes ingresar un correo válido.',
            'email.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.max' => 'La contraseña no puede tener más de 255 caracteres.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe estar en formato JPEG o PNG.',
            'image.max' => 'La imagen no puede pesar más de 1MB.'
        ]);

        $user = new User();
        $user->name = $request->get('name');
        $user->last_name = $request->get('last_name');
        $user->email = $request->get('email');
        $user->address = $request->get('address');
        $user->phone = $request->get('phone');
        $user->password = Hash::make($request->get('password'));

        
        $user->role = (int) $request->get('role', 0);

        if ($request->hasFile('image')) {
            $user->image = ImageStorage::store($request->file('image'), 'users');
        } else {
            $user->image = 'images/no-image.jpg';
        }

        $user->save();

        return redirect()->route('users.index')->with(['msg' => 'Usuario creado exitosamente.']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        App::setLocale('es');
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'password' => 'nullable|string|min:8|confirmed|max:255',
            'image' => 'image|mimes:jpeg,png|max:1024|nullable'
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debes ingresar un correo válido.',
            'email.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.max' => 'La contraseña no puede tener más de 255 caracteres.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe estar en formato JPEG o PNG.',
            'image.max' => 'La imagen no puede pesar más de 1MB.'
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->get('name');
        $user->last_name = $request->get('last_name');
        $user->email = $request->get('email');
        $user->address = $request->get('address');
        $user->phone = $request->get('phone');

        if ($request->get('password')) {
            $user->password = Hash::make($request->get('password'));
        }

        $user->role = (int) $request->get('role', $user->role);

        if ($request->hasFile('image')) {
            $user->image = ImageStorage::store($request->file('image'), 'users', $user->image);
        }

        $user->save();

        return redirect()->route('users.index')->with(['msg' => 'Usuario actualizado exitosamente.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        ImageStorage::delete($user->image);
        $user->delete();
        return redirect()->route('users.index')->with('msg', 'Usuario eliminado exitosamente.');
    }
}
