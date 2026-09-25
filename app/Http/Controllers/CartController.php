<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartPricing;
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
            'price' => (float) $product->price,
            'weight' => 0,
            'options' => ['image' => $product->image],
        ]);

        CartPricing::syncFromDatabase();

        return $this->cartResponse($request, 'Producto agregado');
    }

    public function update(Request $request, $rowId)
    {
        $request->validate([
            'qty' => 'required|integer|min:1|max:50',
        ]);

        Cart::instance('shopping')->update($rowId, (int) $request->input('qty'));
        CartPricing::syncFromDatabase();

        return $this->cartResponse($request, 'Cantidad actualizada');
    }

    public function remove(Request $request, $rowId)
    {
        Cart::instance('shopping')->remove($rowId);
        CartPricing::syncFromDatabase();

        return $this->cartResponse($request, 'Producto eliminado del carrito');
    }

    private function cartResponse(Request $request, string $message)
    {
        $summary = CartPricing::summarize();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'count' => $summary['items_count'],
                'qty_total' => $summary['qty_total'],
                'total' => $summary['total'],
                'total_formatted' => number_format($summary['total'], 2),
                'lines' => $summary['lines']->values(),
                'cart_html' => view('layouts.partials.cart-panel')->render(),
            ]);
        }

        return redirect()->back()->with(['msg' => $message]);
    }
}
