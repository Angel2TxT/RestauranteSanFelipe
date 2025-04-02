<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class BlockAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $role = auth()->user()->role; // Obtener el rol
        
            if ($role == 2 || $role == 3) {  
                return redirect('/admin/orders'); // Redirige a /admin/orders si el rol es 2 o 3
            }
        
            if ($role == 0) {  
                return redirect('/'); // Redirige a / si el rol es 0
            }
        
            error_log("Usuario autenticado con ID: " . Auth::id() . " y rol: " . $role);
        } else {
            error_log("Usuario no autenticado");
        }

        return $next($request); // Permite que la solicitud continúe si no hay redirección
    }
}
