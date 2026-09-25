<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function index()
    {
        return view('kitchen.index');
    }

    public function feed()
    {
        $pending = Order::with(['user', 'table', 'items'])
            ->where('status', 'pending')
            ->orderBy('id')
            ->get();

        $inProgress = Order::with(['user', 'table', 'items'])
            ->where('status', 'in_progress')
            ->orderBy('id')
            ->get();

        return response()->json([
            'ok' => true,
            'server_time' => Carbon::now('America/Mexico_City')->toIso8601String(),
            'counts' => [
                'pending' => $pending->count(),
                'in_progress' => $inProgress->count(),
            ],
            'pending' => $pending->map(fn (Order $order) => $this->serializeOrder($order))->values(),
            'in_progress' => $inProgress->map(fn (Order $order) => $this->serializeOrder($order))->values(),
            'latest_pending_id' => (int) ($pending->max('id') ?? 0),
        ]);
    }

    protected function serializeOrder(Order $order): array
    {
        $tipo = match ($order->order_type) {
            'dine_in' => 'Local' . ($order->table ? ' · ' . $order->table->name : ''),
            'delivery' => 'Envío',
            'pickup' => 'Para llevar',
            default => (string) $order->order_type,
        };

        $created = $order->created_at
            ? $order->created_at->timezone('America/Mexico_City')
            : null;

        $ageMinutes = $created ? $created->diffInMinutes(Carbon::now('America/Mexico_City')) : 0;

        return [
            'id' => $order->id,
            'status' => $order->status,
            'type' => $tipo,
            'customer' => $order->user->name ?? 'Cliente',
            'total' => number_format((float) $order->total, 2),
            'notes' => $order->notes ?: null,
            'fecha' => $order->fecha,
            'created_at' => $created?->format('H:i'),
            'age_minutes' => (int) $ageMinutes,
            'items' => $order->items->map(function ($item) {
                return [
                    'name' => $item->name,
                    'qty' => (int) ($item->pivot->qty ?? $item->qty ?? 1),
                ];
            })->values(),
            'status_url' => route('orders.status', $order),
        ];
    }
}
