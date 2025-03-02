<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\Table;  
use Illuminate\Http\Request;
use App\Services\Mailing;
use Illuminate\Support\Facades\DB;
use Gloudemans\Shoppingcart\Facades\Cart;

class OrderController extends Controller
{
    protected $mail;
    
    public function __construct()
    {
        $this->mail = new Mailing();
    }

    public function checkout()
{
    // Obtener las mesas disponibles
    $tables = Table::where('status', 'available')->get(); // Solo mesas disponibles

    // Pasar las mesas a la vista
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
        'table_id' => 'required_if:order_type,dine_in', 
    ]);
    
    $user = auth()->user();
    $userEmail = $request->input('email');

    // Iniciar transacción para asegurar que todos los cambios se guarden correctamente
    DB::transaction(function () use ($request, $userEmail) {
        // Crear la orden
        $order = new Order();
        $order->total = Cart::instance('shopping')->priceTotal();
        $order->notes = $request->get('notes');
        $order->status = "Pending";
        $order->fecha = date('Y-m-d');
        $order->user_id = auth()->user()->id;
        $order->order_type = $request->order_type;

        if($request->order_type === 'dine_in' ){
            $tableId = $request->input('table_id');
            $tables = Table::find($tableId); // Busca la orden por ID
            $tables->status = 'occupied'; // Cambia el estado
            $tables->save(); 
        }

        // No asignar la dirección en la orden (ya está en el usuario)

        // Asignar mesa si es una orden de tipo ""
        $order->table_id = $request->order_type === 'dine_in' ? $request->table_id : null;

        $order->save();
        $order->items = Cart::instance('shopping')->content();
        // Enviar el correo después de guardar la orden
        $this->mail->sendMessage($userEmail, $order);
        error_log(json_encode($request->all()));
        // Agregar items a la orden
        foreach (Cart::instance('shopping')->content() as $product) {
            $item = new Item();
            $item->name = $product->name;
            $item->price = $product->price;
            $item->qty = $product->qty;
            $item->image = $product->options->image;
            $item->product_id = $product->id;
            $item->fecha = date('Y-m-d');
            $item->save();
            $order->items()->attach($product->id, [
                'qty' => $product->qty,
                'fecha' => date('Y-m-d'),
            ]);
            

        }

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
        // Cambiar el estado de la orden
        if ($order->status === 'pending') {
            $order->status = 'in_progress';
            $order->update();
        } elseif ($order->status === 'in_progress') {
            $order->status = 'ready_for_delivery';
            $order->update();
        } elseif ($order->status === 'ready_for_delivery') {
            $order->status = 'completed';
            $order->update();
        }

        return redirect()->back()->with(['msg' => 'Estado de la orden actualizado']);
    }

    public function index()
    {
        $orders = Order::with('table')->orderBy('id', 'desc')->paginate(5);

        return view('orders.index', compact('orders'));
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
