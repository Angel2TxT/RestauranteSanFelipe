<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockAccessMiddleware
{
    /**
     * Restrict admin routes by role.
     * 1 = admin (full access), 2/3 = staff (orders only), 0 = client (blocked).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $role = (int) auth()->user()->role;
        $path = $request->path(); // e.g. admin/orders

        // Clients cannot access admin
        if ($role === 0) {
            return redirect('/');
        }

        // Employees and delivery: only orders section
        if ($role === 2 || $role === 3) {
            $allowed = $path === 'admin/orders'
                || str_starts_with($path, 'admin/orders/')
                || $request->routeIs('orders.*');

            if (!$allowed) {
                return redirect()->route('orders.index');
            }
        }

        // Admins (role 1) and allowed staff continue
        return $next($request);
    }
}
