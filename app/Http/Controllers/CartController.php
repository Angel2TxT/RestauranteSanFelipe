<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function add(Request $request, Product $product)
    {
        Cart::instance('shopping')->add([
            'id' => $product->id,
            'name' => $product->name,
            'qty' => 1,
            'price' => $product->price,
            'weight' => 0,
            'options' => ['image' => $product->image],
        ]);

        return $this->cartResponse($request, 'Producto agregado');
    }

    public function update(Request $request, $rowId)
    {
        $request->validate([
            'qty' => 'required|integer|min:1|max:50',
        ]);

        Cart::instance('shopping')->update($rowId, (int) $request->input('qty'));

        return $this->cartResponse($request, 'Cantidad actualizada');
    }

    public function remove(Request $request, $rowId)
    {
        Cart::instance('shopping')->remove($rowId);

        return $this->cartResponse($request, 'Producto eliminado del carrito');
    }

    private function cartResponse(Request $request, string $message)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'count' => Cart::instance('shopping')->content()->count(),
                'qty_total' => Cart::instance('shopping')->count(),
                'cart_html' => view('layouts.partials.cart-panel')->render(),
            ]);
        }

        return redirect()->back()->with(['msg' => $message]);
    }
}
