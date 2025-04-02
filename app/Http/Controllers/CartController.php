<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function add(Product $product)
    {
        Cart::instance('shopping')->add([
            'id' => $product->id,
            'name' => $product->name,
            'qty' => 1,
            'price' => $product->price,
            'weight' => 0,
            'options' => ['image' => $product->image]
        ]);

        return redirect()->back()->with(['msg'=>"Producto agregado"]);
    }

    public function update(Request $request, $rowId)
    {
        $qty = $request->input('qty');
        
        // Actualiza la cantidad del producto en el carrito
        Cart::instance('shopping')->update($rowId, $qty);
        
        return redirect()->back()->with(['msg' => 'Cantidad actualizada']);
    }

    public function remove($rowId){
        Cart::instance('shopping')->remove($rowId);
        return redirect()->back();
    }
}
