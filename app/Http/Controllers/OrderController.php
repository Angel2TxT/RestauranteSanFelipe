<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use App\Notifications\OrderCreated;
use App\Notifications\OrderStatusChanged;
use App\Services\Mailing;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

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

        $tables = Table::where('status', 'available')->orderBy('name')->get();

        return view('orders.checkout', compact('tables'));
    }

    public function proccesCheckout(Request $request)
    {
        if (Cart::instance('shopping')->count() == 0) {
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
            $order = DB::transaction(function () use ($request, $user) {
                $order = new Order();
                $order->total = (float) str_replace(',', '', Cart::instance('shopping')->priceTotal());
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

                foreach (Cart::instance('shopping')->content() as $product) {
                    $item = new Item();
                    $item->name = $product->name;
                    $item->price = $product->price;
                    $item->qty = $product->qty;
                    $item->image = $product->options->image ?? null;
                    $item->product_id = $product->id;
                    $item->fecha = Carbon::now('America/Mexico_City')->toDateString();
                    $item->save();

                    $order->items()->attach($item->id, [
                        'qty' => $product->qty,
                        'fecha' => Carbon::now('America/Mexico_City')->toDateString(),
                    ]);
                }

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
        ];

        if ($search !== '' && array_key_exists($search, $searchMap)) {
            $search = $searchMap[$search];
        }

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
            ->when($searchStatus, fn ($query) => $query->where('status', $searchStatus))
            ->when($searchOrderType, fn ($query) => $query->where('order_type', $searchOrderType))
            ->when($searchTable, fn ($query) => $query->where('table_id', $searchTable))
            ->orderByDesc('id')
            ->paginate(5)
            ->withQueryString();

        return view('orders.index', compact('orders', 'tables'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items', 'table']);

        return view('orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
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
