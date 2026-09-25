<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use App\Notifications\OrderCancelled;
use App\Notifications\OrderCreated;
use App\Notifications\OrderStatusChanged;
use App\Notifications\OrderUpdated;
use App\Services\CartPricing;
use App\Services\Mailing;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Models\Product;

class OrderController extends Controller
{
    protected Mailing $mail;

    public function __construct(Mailing $mail)
    {
        $this->mail = $mail;
    }

    public function checkout()
    {
        if (Cart::instance('shopping')->count() == 0) {
            return redirect()->route('home')->with('error', 'El carrito está vacío.');
        }

        $summary = CartPricing::summarize();
        if ($summary['items_count'] === 0) {
            return redirect()->route('home')->with('error', 'El carrito está vacío.');
        }

        $tables = Table::where('status', 'available')->orderBy('name')->get();

        return view('orders.checkout', compact('tables', 'summary'));
    }

    public function proccesCheckout(Request $request)
    {
        $summary = CartPricing::summarize();
        if ($summary['items_count'] === 0) {
            return redirect()->route('home')->with('error', 'El carrito está vacío.');
        }

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'order_type' => 'required|in:dine_in,delivery,pickup',
            'address' => 'required_if:order_type,delivery|nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
            'table_id' => [
                'nullable',
                'required_if:order_type,dine_in',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->order_type === 'dine_in') {
                        $table = Table::find($value);
                        if (!$table || $table->status !== 'available') {
                            $fail('La mesa seleccionada no está disponible.');
                        }
                    }
                },
            ],
        ]);

        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $userEmail = $request->input('email');
        $order = null;

        try {
            $order = DB::transaction(function () use ($request, $user, $summary) {
                $order = new Order();
                $order->total = $summary['total'];
                $order->notes = $request->get('notes');
                $order->status = 'pending';
                $order->fecha = Carbon::now('America/Mexico_City')->toDateString();
                $order->user_id = $user->id;
                $order->order_type = $request->order_type;
                $order->delivery_address = $request->order_type === 'delivery'
                    ? $request->get('address')
                    : null;

                if ($request->order_type === 'dine_in') {
                    $table = Table::lockForUpdate()->findOrFail($request->table_id);
                    $table->status = 'occupied';
                    $table->save();
                    $order->table_id = $table->id;
                }

                $order->save();

                foreach ($summary['lines'] as $line) {
                    $product = Product::query()->find($line['product_id']);
                    if (!$product) {
                        continue;
                    }

                    $unitPrice = round((float) $product->price, 2);
                    $qty = (int) $line['qty'];

                    $item = new Item();
                    $item->name = $product->name;
                    $item->price = $unitPrice;
                    $item->qty = $qty;
                    $item->image = $product->image;
                    $item->product_id = $product->id;
                    $item->fecha = Carbon::now('America/Mexico_City')->toDateString();
                    $item->save();

                    $order->items()->attach($item->id, [
                        'qty' => $qty,
                        'fecha' => Carbon::now('America/Mexico_City')->toDateString(),
                    ]);
                }

                $this->recalculateTotal($order);

                return $order->load(['user', 'items']);
            });
        } catch (\Throwable $e) {
            Log::error('Error al crear orden: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'No se pudo crear la orden. Intenta de nuevo.');
        }

        try {
            $this->mail->sendMessage($userEmail, $order);
        } catch (\Throwable $e) {
            Log::warning('Orden creada pero el correo falló: ' . $e->getMessage());
        }

        Cart::instance('shopping')->destroy();

        try {
            $staff = User::query()
                ->whereIn('role', [1, 2, 3])
                ->get();

            if ($staff->isNotEmpty()) {
                Notification::send($staff, new OrderCreated($order));
            }
        } catch (\Throwable $e) {
            Log::warning('Orden creada pero la notificación interna falló: ' . $e->getMessage());
        }

        $itemsCount = $order->items->sum(fn ($item) => (int) ($item->pivot->qty ?? 1));

        return redirect()->route('orders.my')->with('order_placed', [
            'id' => $order->id,
            'total' => number_format((float) $order->total, 2),
            'count' => $itemsCount,
            'type' => $order->order_type,
        ]);
    }

    public function myOrders()
    {
        $orders = auth()->user()
            ->orders()
            ->with('items')
            ->latest('id')
            ->paginate(5);

        return view('orders.my-orders', compact('orders'));
    }

    public function startEditing(Order $order)
    {
        $this->assertOwnedPending($order);
        session(['editing_order_id' => $order->id]);

        return redirect()->route('shop', ['order' => $order->id]);
    }

    public function stopEditing()
    {
        session()->forget('editing_order_id');

        return redirect()->route('orders.my');
    }

    public function mergeCartItems(Request $request, Order $order)
    {
        $this->assertOwnedPending($order);

        $summary = CartPricing::summarize();
        if ($summary['items_count'] === 0) {
            return $this->clientResponse($request, 'El carrito está vacío.', false);
        }

        try {
            DB::transaction(function () use ($order, $summary) {
                foreach ($summary['lines'] as $line) {
                    $product = Product::query()->find($line['product_id']);
                    if (!$product) {
                        continue;
                    }

                    $unitPrice = round((float) $product->price, 2);
                    $qtyToAdd = (int) $line['qty'];
                    $existing = $order->items()->where('product_id', $product->id)->first();

                    if ($existing) {
                        $newQty = (int) ($existing->pivot->qty ?? 1) + $qtyToAdd;
                        $existing->qty = $newQty;
                        $existing->price = $unitPrice;
                        $existing->name = $product->name;
                        $existing->save();
                        $order->items()->updateExistingPivot($existing->id, [
                            'qty' => $newQty,
                            'fecha' => Carbon::now('America/Mexico_City')->toDateString(),
                        ]);
                    } else {
                        $item = new Item();
                        $item->name = $product->name;
                        $item->price = $unitPrice;
                        $item->qty = $qtyToAdd;
                        $item->image = $product->image;
                        $item->product_id = $product->id;
                        $item->fecha = Carbon::now('America/Mexico_City')->toDateString();
                        $item->save();

                        $order->items()->attach($item->id, [
                            'qty' => $qtyToAdd,
                            'fecha' => Carbon::now('America/Mexico_City')->toDateString(),
                        ]);
                    }
                }

                $this->recalculateTotal($order);
            });
        } catch (\Throwable $e) {
            Log::error('Error al agregar productos a la orden: ' . $e->getMessage());
            return $this->clientResponse($request, 'No se pudieron agregar los productos.', false);
        }

        Cart::instance('shopping')->destroy();
        session()->forget('editing_order_id');
        $order->refresh()->load(['user', 'items']);
        $this->notifyStaff(new OrderUpdated($order));

        return $this->clientResponse(
            $request,
            'Productos agregados a la orden #' . $order->id,
            true,
            route('orders.my')
        );
    }

    public function updateItem(Request $request, Order $order, Item $item)
    {
        $this->assertOwnedPending($order);
        $this->assertOrderHasItem($order, $item);

        $data = $request->validate([
            'qty' => 'required|integer|min:1|max:50',
        ]);

        $qty = (int) $data['qty'];
        $item->qty = $qty;
        $item->save();
        $order->items()->updateExistingPivot($item->id, [
            'qty' => $qty,
            'fecha' => Carbon::now('America/Mexico_City')->toDateString(),
        ]);

        $this->recalculateTotal($order);
        $order->refresh()->load(['user', 'items']);
        $this->notifyStaff(new OrderUpdated($order));

        return $this->clientResponse($request, 'Cantidad actualizada', true);
    }

    public function removeItem(Request $request, Order $order, Item $item)
    {
        $this->assertOwnedPending($order);
        $this->assertOrderHasItem($order, $item);

        $order->items()->detach($item->id);
        $item->delete();

        $remaining = $order->items()->count();
        if ($remaining === 0) {
            $this->cancelPendingOrder($order);
            session()->forget('editing_order_id');
            $this->notifyStaff(new OrderCancelled($order->fresh(['user', 'items'])));

            return $this->clientResponse($request, 'La orden quedó vacía y se canceló.', true, route('orders.my'));
        }

        $this->recalculateTotal($order);
        $order->refresh()->load(['user', 'items']);
        $this->notifyStaff(new OrderUpdated($order));

        return $this->clientResponse($request, 'Producto eliminado de la orden', true);
    }

    public function cancel(Request $request, Order $order)
    {
        $this->assertOwnedPending($order);
        $this->cancelPendingOrder($order);
        session()->forget('editing_order_id');
        $order->refresh()->load(['user', 'items']);
        $this->notifyStaff(new OrderCancelled($order));

        return $this->clientResponse($request, 'Pedido cancelado', true, route('orders.my'));
    }

    public function ticket(Order $order)
    {
        $this->assertOwned($order);

        if ($order->status !== 'completed') {
            abort(403, 'El ticket solo está disponible cuando la orden está completada.');
        }

        return app(ReportController::class)->streamOrderPdf($order, 'ticket_orden_' . $order->id . '.pdf');
    }

    protected function assertOwned(Order $order): void
    {
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403);
        }
    }

    protected function assertOwnedPending(Order $order): void
    {
        $this->assertOwned($order);

        if ($order->status !== 'pending') {
            abort(403, 'Solo puedes modificar órdenes pendientes.');
        }
    }

    protected function assertOrderHasItem(Order $order, Item $item): void
    {
        if (!$order->items()->where('items.id', $item->id)->exists()) {
            abort(404);
        }
    }

    protected function recalculateTotal(Order $order): void
    {
        $order->load('items');
        $total = $order->items->sum(function ($item) {
            return (float) $item->price * (int) ($item->pivot->qty ?? 1);
        });
        $order->total = round($total, 2);
        $order->save();
    }

    protected function cancelPendingOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            if ($order->table_id) {
                $table = Table::find($order->table_id);
                if ($table) {
                    $table->status = 'available';
                    $table->save();
                }
            }

            $order->status = 'cancelled_by_user';
            $order->save();
        });
    }

    protected function notifyStaff($notification): void
    {
        try {
            $staff = User::query()->whereIn('role', [1, 2, 3])->get();
            if ($staff->isNotEmpty()) {
                Notification::send($staff, $notification);
            }
        } catch (\Throwable $e) {
            Log::warning('Notificación de orden falló: ' . $e->getMessage());
        }
    }

    protected function clientResponse(Request $request, string $message, bool $ok = true, ?string $redirect = null)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => $ok,
                'message' => $message,
                'redirect' => $redirect,
            ], $ok ? 200 : 422);
        }

        if (!$ok) {
            return redirect()->back()->with('error', $message);
        }

        return redirect($redirect ?: route('orders.my'))->with('msg', $message);
    }

    public function changeStatus(Order $order)
    {
        $statusFlow = ['pending', 'in_progress', 'ready_for_delivery', 'paid', 'completed'];
        $currentIndex = array_search($order->status, $statusFlow, true);
        $previousStatus = $order->status;

        if ($currentIndex !== false && $currentIndex < count($statusFlow) - 1) {
            $order->status = $statusFlow[$currentIndex + 1];
            $order->save();

            if ($order->status === 'completed' && $order->table_id) {
                $table = Table::find($order->table_id);
                if ($table) {
                    $table->status = 'available';
                    $table->save();
                }
            }

            try {
                if ($order->user) {
                    $order->user->notify(new OrderStatusChanged($order, $previousStatus));
                }
            } catch (\Throwable $e) {
                Log::warning('Estado actualizado pero la notificación falló: ' . $e->getMessage());
            }
        }

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => 'Estado de la orden actualizado',
                'order_id' => $order->id,
                'status' => $order->status,
            ]);
        }

        return redirect()->back()->with(['msg' => 'Estado de la orden actualizado']);
    }

    public function revertStatus(Order $order)
    {
        $previousStatus = $order->status;
        $changed = false;

        if ($order->status === 'ready_for_delivery') {
            $order->status = 'in_progress';
            $order->save();
            $changed = true;
        } elseif ($order->status === 'paid') {
            $order->status = 'ready_for_delivery';
            $order->save();
            $changed = true;
        }

        if ($changed) {
            try {
                if ($order->user) {
                    $order->user->notify(new OrderStatusChanged($order, $previousStatus));
                }
            } catch (\Throwable $e) {
                Log::warning('Estado revertido pero la notificación falló: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with(['msg' => 'Estado de la orden revertido']);
    }

    public function index(Request $request)
    {
        $search = strtolower(trim((string) $request->get('search', '')));
        $searchDate = $request->get('search_date');
        $searchStatus = $request->get('search_status');
        $searchOrderType = $request->get('search_order_type');
        $searchTable = $request->get('search_table');

        $tables = Table::orderBy('name')->get();

        $searchMap = [
            'local' => 'dine_in',
            'envio' => 'delivery',
            'recoger' => 'pickup',
            'pendiente' => 'pending',
            'proceso' => 'in_progress',
            'entregar' => 'ready_for_delivery',
            'pagado' => 'paid',
            'completado' => 'completed',
            'cancelada' => 'cancelled',
        ];

        if ($search !== '' && array_key_exists($search, $searchMap)) {
            $search = $searchMap[$search];
        }

        $statusCounts = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'all' => (int) $statusCounts->sum(),
            'pending' => (int) ($statusCounts['pending'] ?? 0),
            'in_progress' => (int) ($statusCounts['in_progress'] ?? 0),
            'ready_for_delivery' => (int) ($statusCounts['ready_for_delivery'] ?? 0),
            'paid' => (int) ($statusCounts['paid'] ?? 0),
            'completed' => (int) ($statusCounts['completed'] ?? 0),
            'cancelled' => (int) (($statusCounts['cancelled_by_user'] ?? 0) + ($statusCounts['cancelled_by_store'] ?? 0)),
        ];

        $orders = Order::with(['table', 'user', 'items'])
            ->when($search !== '', function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        })
                        ->orWhere('order_type', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->when($searchDate, fn ($query) => $query->whereDate('fecha', $searchDate))
            ->when($searchStatus === 'cancelled', function ($query) {
                return $query->whereIn('status', ['cancelled_by_user', 'cancelled_by_store']);
            })
            ->when($searchStatus && $searchStatus !== 'cancelled', fn ($query) => $query->where('status', $searchStatus))
            ->when($searchOrderType, fn ($query) => $query->where('order_type', $searchOrderType))
            ->when($searchTable, fn ($query) => $query->where('table_id', $searchTable))
            ->orderByDesc('id')
            ->paginate(8)
            ->withQueryString();

        return view('orders.index', compact('orders', 'tables', 'counts'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items', 'table']);

        return view('orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        if (!auth()->user()?->isAdmin()) {
            abort(403, 'Solo el administrador puede eliminar órdenes.');
        }

        DB::transaction(function () use ($order) {
            if ($order->table_id) {
                $table = Table::find($order->table_id);
                if ($table) {
                    $table->status = 'available';
                    $table->save();
                }
            }

            $items = $order->items()->get();
            $order->items()->detach();
            foreach ($items as $item) {
                $item->delete();
            }
            $order->delete();
        });

        return redirect()->route('orders.index')->with(['msg' => 'Orden eliminada.']);
    }
}
