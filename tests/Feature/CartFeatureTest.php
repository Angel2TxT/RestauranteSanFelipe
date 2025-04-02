<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Gloudemans\Shoppingcart\Facades\Cart; 

class CartFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_adds_a_product_to_the_cart()
{

    $product = \App\Models\Product::create([
        'name' => 'Producto de prueba',
        'description' => 'Descripción del producto',
        'price' => 10.99,
        'label' => 'Etiqueta del producto',
        'category_id' => 1,
        'image' => 'imagen.jpg', 
    ]);


    $response = $this->get(route('cart.add', ['product' => $product->id]));

    $response->assertRedirect();

    $this->assertCount(1, Cart::instance('shopping')->content());
}
    
}

