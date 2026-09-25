<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockAccessMiddleware
{
    /**
     * 1 = admin (todo), 2/3 = staff (órdenes + perfil), 0 = cliente (bloqueado).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $role = (int) auth()->user()->role;
        $routeName = $request->route()?->getName();

        if ($role === 0) {
            return redirect('/');
        }

        if (in_array($role, [2, 3], true)) {
            $allowedPrefixes = ['orders.', 'profile.', 'admin.home', 'kitchen.', 'cashier.'];
            $allowed = collect($allowedPrefixes)->contains(
                fn ($prefix) => $routeName && str_starts_with($routeName, $prefix)
            );

            // También permitir el dashboard de admin redirigido a órdenes
            if ($routeName === 'admin.home') {
                return redirect()->route('orders.index');
            }

            if (!$allowed) {
                return redirect()->route('orders.index');
            }
        }

        return $next($request);
    }
}
