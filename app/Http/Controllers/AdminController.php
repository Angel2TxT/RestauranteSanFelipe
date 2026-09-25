<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $today = Carbon::now('America/Mexico_City')->toDateString();

        $statusCounts = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'pending' => (int) ($statusCounts['pending'] ?? 0),
            'in_progress' => (int) ($statusCounts['in_progress'] ?? 0),
            'ready_for_delivery' => (int) ($statusCounts['ready_for_delivery'] ?? 0),
            'paid' => (int) ($statusCounts['paid'] ?? 0),
            'completed_today' => Order::query()
                ->where('status', 'completed')
                ->whereDate('fecha', $today)
                ->count(),
            'sales_today' => (float) Order::query()
                ->whereDate('fecha', $today)
                ->whereIn('status', ['paid', 'completed'])
                ->sum('total'),
            'products' => Product::count(),
            'users' => User::count(),
            'orders_today' => Order::query()->whereDate('fecha', $today)->count(),
        ];

        return view('admin.index', compact('stats'));
    }
}
