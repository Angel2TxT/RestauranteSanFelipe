<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Notifications\OrderStatusChanged;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashierController extends Controller
{
    public function index()
    {
        return view('cashier.index');
    }

    public function feed()
    {
        $ready = Order::with(['user', 'table', 'items'])
            ->where('status', 'ready_for_delivery')
            ->orderBy('id')
            ->get();

        $paid = Order::with(['user', 'table', 'items', 'latestPayment'])
            ->where('status', 'paid')
            ->orderBy('id')
            ->get();

        return response()->json([
            'ok' => true,
            'server_time' => Carbon::now('America/Mexico_City')->toIso8601String(),
            'counts' => [
                'ready' => $ready->count(),
                'paid' => $paid->count(),
            ],
            'ready' => $ready->map(fn (Order $order) => $this->serializeOrder($order))->values(),
            'paid' => $paid->map(fn (Order $order) => $this->serializeOrder($order))->values(),
            'latest_ready_id' => (int) ($ready->max('id') ?? 0),
        ]);
    }

    public function previewChange(Request $request, Order $order)
    {
        if ($order->status !== 'ready_for_delivery') {
            return response()->json([
                'ok' => false,
                'message' => 'Esta orden no está lista para cobro.',
            ], 422);
        }

        $data = $request->validate([
            'amount_received' => 'required|numeric|min:0',
        ]);

        return response()->json($this->buildChangePreview($order, (float) $data['amount_received']));
    }

    public function charge(Request $request, Order $order)
    {
        if ($order->status !== 'ready_for_delivery') {
            return response()->json([
                'ok' => false,
                'message' => 'Esta orden no está lista para cobro.',
            ], 422);
        }

        $data = $request->validate([
            'amount_received' => 'required|numeric|min:0.01',
            'method' => 'nullable|string|in:cash,card,transfer',
        ]);

        $preview = $this->buildChangePreview($order, (float) $data['amount_received']);
        $amountDue = $preview['amount_due'];
        $amountReceived = $preview['amount_received'];
        $change = $preview['change_given'];

        if (!$preview['can_charge']) {
            return response()->json([
                'ok' => false,
                'message' => 'El monto recibido no cubre el total ($' . number_format($amountDue, 2) . ').',
            ], 422);
        }

        $method = $data['method'] ?? 'cash';
        $previousStatus = $order->status;

        try {
            DB::transaction(function () use ($order, $amountDue, $amountReceived, $change, $method) {
                Payment::create([
                    'order_id' => $order->id,
                    'cashier_id' => auth()->id(),
                    'amount_due' => $amountDue,
                    'amount_received' => $amountReceived,
                    'change_given' => max(0, $change),
                    'method' => $method,
                ]);

                $order->status = 'paid';
                $order->save();
            });
        } catch (\Throwable $e) {
            Log::error('Error al registrar cobro: ' . $e->getMessage());

            return response()->json([
                'ok' => false,
                'message' => 'No se pudo registrar el cobro.',
            ], 500);
        }

        $order->refresh();

        try {
            if ($order->user) {
                $order->user->notify(new OrderStatusChanged($order, $previousStatus));
            }
        } catch (\Throwable $e) {
            Log::warning('Cobro registrado pero la notificación falló: ' . $e->getMessage());
        }

        return response()->json([
            'ok' => true,
            'message' => $change > 0
                ? 'Cobro registrado · cambio $' . number_format($change, 2)
                : 'Cobro exacto registrado',
            'order_id' => $order->id,
            'status' => $order->status,
            'change_given' => $change,
            'amount_received' => $amountReceived,
            'amount_due' => $amountDue,
        ]);
    }

    /**
     * @return array{
     *   ok: bool,
     *   amount_due: float,
     *   amount_received: float,
     *   change_given: float,
     *   shortfall: float,
     *   can_charge: bool,
     *   label: string,
     *   display_amount: string,
     *   amount_due_formatted: string,
     *   state: string
     * }
     */
    protected function buildChangePreview(Order $order, float $amountReceived): array
    {
        $amountDue = round((float) $order->total, 2);
        $received = round($amountReceived, 2);
        $diff = round($received - $amountDue, 2);
        $canCharge = $received > 0 && ($received + 0.001) >= $amountDue;

        if ($received <= 0) {
            $label = 'Cambio';
            $state = 'empty';
            $display = 0.0;
        } elseif ($diff < 0) {
            $label = 'Faltan';
            $state = 'short';
            $display = abs($diff);
        } elseif (abs($diff) < 0.001) {
            $label = 'Pago exacto';
            $state = 'exact';
            $display = 0.0;
        } else {
            $label = 'Cambio a entregar';
            $state = 'ok';
            $display = $diff;
        }

        return [
            'ok' => true,
            'amount_due' => $amountDue,
            'amount_received' => $received,
            'change_given' => max(0, $diff),
            'shortfall' => $diff < 0 ? abs($diff) : 0.0,
            'can_charge' => $canCharge,
            'label' => $label,
            'display_amount' => number_format($display, 2),
            'amount_due_formatted' => number_format($amountDue, 2),
            'state' => $state,
        ];
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
        $payment = $order->relationLoaded('latestPayment') ? $order->latestPayment : null;

        return [
            'id' => $order->id,
            'status' => $order->status,
            'type' => $tipo,
            'customer' => $order->user->name ?? 'Cliente',
            'phone' => $order->user->phone ?? null,
            'total' => number_format((float) $order->total, 2),
            'total_raw' => round((float) $order->total, 2),
            'notes' => $order->notes ?: null,
            'fecha' => $order->fecha,
            'created_at' => $created?->format('H:i'),
            'age_minutes' => (int) $ageMinutes,
            'items' => $order->items->map(function ($item) {
                return [
                    'name' => $item->name,
                    'qty' => (int) ($item->pivot->qty ?? $item->qty ?? 1),
                    'price' => number_format((float) $item->price, 2),
                ];
            })->values(),
            'status_url' => route('orders.status', $order),
            'charge_url' => route('cashier.charge', $order),
            'preview_url' => route('cashier.preview', $order),
            'ticket_url' => route('orders.report', $order),
            'payment' => $payment ? [
                'amount_received' => number_format((float) $payment->amount_received, 2),
                'change_given' => number_format((float) $payment->change_given, 2),
                'method' => $payment->method,
            ] : null,
        ];
    }
}
