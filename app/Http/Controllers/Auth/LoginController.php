<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Override the login validation to add custom error messages.
     */
    protected function validateLogin(Request $request)
    {
        $messages = [
            'email.exists' => 'El correo electrónico no está registrado en nuestros sistemas.',
            'password.incorrect' => 'La contraseña ingresada es incorrecta.',
        ];

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6',
        ], $messages);
    }

    /**
     * Custom authentication attempt with error messages.
     */
    protected function credentials(Request $request)
    {
        return [
            'email' => $request->email,
            'password' => $request->password,
        ];
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Attempt to authenticate the user
        if (Auth::attempt($this->credentials($request))) {
           
            if  (auth()->user()->role == 1) { 
                
                return redirect('/admin/home');
            
            }
            return redirect()->intended($this->redirectTo);
        }

        // If authentication fails, redirect back with errors
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas son incorrectas.',
        ]);
    }
}
