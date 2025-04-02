<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\Table;  
use Illuminate\Http\Request;
use App\Services\Mailing;
use Illuminate\Support\Facades\DB;
use Gloudemans\Shoppingcart\Facades\Cart;
use Carbon\Carbon;


class OrderController extends Controller
{
    protected $mail;
    
    public function __construct()
    {
        $this->mail = new Mailing();
    }

    public function checkout()
    {
        if (Cart::instance('shopping')->count() == 0) {
            return redirect()->route('home')->with('error', 'El carrito está vacío.');
        }
    
        // Obtener las mesas disponibles
        $tables = Table::where('status', 'available')->get(); // Solo mesas disponibles
    
        // Pasar las mesas a la vista correctamente
        return view('orders.checkout', compact('tables'));
    }
    

public function proccesCheckout(Request $request)
{
    // Validación de los datos de entrada
    $this->validate($request, [
        'name' => 'required',
        'last_name' => 'required',
        'email' => 'required|email',
        'phone' => 'required',
        'order_type' => 'required|in:dine_in,delivery,pickup',
        'address' => 'required_if:order_type,delivery',
        'table_id' => [
            'required_if:order_type,dine_in', 
            function ($attribute, $value, $fail) use ($request) {
                if ($request->order_type === 'dine_in' && !Table::where('id', $value)->exists()) {
                    $fail('La mesa seleccionada no es válida.');
                }
            }
        ],
    ]);
    

    $user = auth()->user();
    $userEmail = $request->input('email');

    // Iniciar transacción para asegurar que todos los cambios se guarden correctamente
    DB::transaction(function () use ($request, $userEmail, $user) {
        // Crear la orden
        $order = new Order();
        $order->total = (float) str_replace(',', '', Cart::instance('shopping')->priceTotal());

        $order->notes = $request->get('notes');
        $order->status = "pending";
        $order->fecha = Carbon::now('America/Mexico_City')->toDateString();
        $order->user_id = $user->id;
        $order->order_type = $request->order_type;

        // Manejo de mesas si es "dine_in"
        if ($request->order_type === 'dine_in') {
            $table = Table::find($request->table_id);
            if ($table && $table->status === 'available') {
                $table->status = 'occupied'; 
                $table->save();
                $order->table_id = $table->id;
            } else {
                throw new \Exception("La mesa seleccionada no está disponible.");
            }
        }

        $order->save();

        // Agregar productos a la orden
        foreach (Cart::instance('shopping')->content() as $product) {
            $item = new Item();
            $item->name = $product->name;
            $item->price = $product->price;
            $item->qty = $product->qty;
            $item->image = $product->options->image ?? null; // Evitar error si no hay imagen
            $item->product_id = $product->id;
            $item->fecha = Carbon::now('America/Mexico_City')->toDateString();
            $item->save();

            // Relacionar con la orden
            $order->items()->attach($item->id, [
                'qty' => $product->qty,
                'fecha' => Carbon::now()->toDateString(),
            ]);
        }

        // Enviar el correo después de guardar la orden
        $this->mail->sendMessage($userEmail, $order);
    });

    // Limpiar el carrito después de guardar la orden
    Cart::instance('shopping')->destroy();

    // Redirigir con mensaje de éxito
    return redirect()->route('home')->with(['msg' => 'Orden creada correctamente.']);
}


    public function myOrders()
    {
        $orders = auth()->user()->orders()->paginate(5);

        return view('orders.my-orders', compact('orders'));
    }

    public function changeStatus(Order $order)
    {
        // Definir los estados en orden
        $statusFlow = ['pending', 'in_progress', 'ready_for_delivery', 'paid', 'completed'];
    
        // Obtener el índice del estado actual
        $currentIndex = array_search($order->status, $statusFlow);
    
        // Si el estado actual está en la lista y no es el último, avanzar al siguiente estado
        if ($currentIndex !== false && $currentIndex < count($statusFlow) - 1) {
            $order->status = $statusFlow[$currentIndex + 1];
            $order->save();
    
            // Si la orden se completa, liberar la mesa si existe
            if ($order->status === 'completed' && $order->table_id) {
                $table = Table::find($order->table_id);
                if ($table) {
                    $table->status = 'available';
                    $table->save();
                }
            }
        }
    
        return redirect()->back()->with(['msg' => 'Estado de la orden actualizado']);
    }
    

public function revertStatus(Order $order)
{
    if ($order->status === 'ready_for_delivery') {
        $order->status = 'in_progress';
    } elseif ($order->status === 'paid') {
        $order->status = 'ready_for_delivery';
    }

    $order->update();

    return redirect()->back()->with(['msg' => 'Estado de la orden revertido']);
}




   

    public function report(Request $request)
{
    // Obtener las fechas de inicio y fin
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // Asegúrate de que las fechas estén en formato correcto
    // Si es necesario, puedes convertirlas a un formato que MySQL acepte (Y-m-d)
    $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
    $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();

    // Si se proporcionan las fechas, filtrar las órdenes
    if ($startDate && $endDate) {
        // Filtrar las órdenes por el rango de fechas
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'desc')
                    ->paginate(5);
    } else {
        // Si no se proporcionan fechas, mostrar todas las órdenes
        $orders = Order::orderBy('created_at', 'desc')->paginate(5);
    }

    return view('orders.report', compact('orders'));
}





// Si se proporcionan las fechas, filtramos las órdenes
        

public function index(Request $request)
{
    $search = $request->get('search');
    $searchDate = $request->get('search_date');
    $searchStatus = $request->get('search_status');
    $searchOrderType = $request->get('search_order_type');
    $searchTable = $request->get('search_table');

    // Obtener todas las mesas para el filtro
    $tables = Table::all();

    // Mapeo de términos en español a los valores internos
    $search = strtolower($search);
    $searchMap = [
        'local' => 'dine_in',
        'envio' => 'delivery',
        'recoger' => 'pickup',
        'pendiente' => 'pending',
        'proceso' => 'in_progress',
        'entregar' => 'ready_for_delivery',
        'pagado' => 'paid', // Agregado "pagado" con el nuevo estado "paid"
        'completado' => 'completed',
    ];

    // Si el término de búsqueda se encuentra en el mapa, reemplázalo con el valor en inglés
    if (array_key_exists($search, $searchMap)) {
        $search = $searchMap[$search];
    }

    // Filtrar las órdenes
    $orders = Order::with('table', 'user')
        ->when($search, function ($query, $search) {
            return $query->where('id', 'like', "%$search%")
                         ->orWhereHas('user', function ($q) use ($search) {
                             $q->where('name', 'like', "%$search%");
                         })
                         ->orWhere('order_type', 'like', "%$search%")
                         ->orWhere('status', 'like', "%$search%");
        })
        ->when($searchDate, function ($query, $searchDate) {
            return $query->whereDate('fecha', '=', $searchDate);
        })
        ->when($searchStatus, function ($query, $searchStatus) {
            return $query->where('status', '=', $searchStatus);
        })
        ->when($searchOrderType, function ($query, $searchOrderType) {
            return $query->where('order_type', '=', $searchOrderType);
        })
        ->when($searchTable, function ($query, $searchTable) {
            return $query->where('table_id', '=', $searchTable);
        })
        ->paginate(5);

    return view('orders.index', compact('orders', 'tables'));
}






    public function show(Order $order)
    {
        
        


        return view('orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        // Eliminar los items relacionados con la orden
        foreach ($order->items as $item) {
            $item->delete();
        }
        $order->delete();

        return redirect()->route('orders.index')->with(['msg' => 'Orden eliminada.']);
    }
}
