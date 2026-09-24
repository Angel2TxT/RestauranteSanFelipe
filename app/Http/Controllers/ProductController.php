<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Support\ImageStorage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->orderByDesc('id')->paginate(8);
        return view('products.index', compact('products'));
    }

    public function display(Product $product)
    {
        $product->load('category');
        return view('products.show', compact('product'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'description' => 'nullable|max:255',
            'price' => 'required|numeric|min:0',
            'label' => 'nullable|max:255',
            'category' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $product = new Product();
        $product->name = $request->get('name');
        $product->description = $request->get('description');
        $product->price = $request->get('price');
        $product->label = $request->get('label');
        $product->category_id = $request->get('category');
        $product->image = ImageStorage::store($request->file('image'), 'products');
        $product->save();

        return redirect()->route('products.index')->with(['msg' => 'Producto creado correctamente']);
    }

    public function show(Product $product)
    {
        return redirect()->route('products.display', $product);
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('categories', 'product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'description' => 'nullable|max:255',
            'price' => 'required|numeric|min:0',
            'label' => 'nullable|max:255',
            'category' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $product->name = $request->get('name');
        $product->description = $request->get('description');
        $product->price = $request->get('price');
        $product->label = $request->get('label');
        $product->category_id = $request->get('category');

        if ($request->hasFile('image')) {
            $product->image = ImageStorage::store($request->file('image'), 'products', $product->image);
        }

        $product->save();

        return redirect()->route('products.index')->with(['msg' => 'Producto editado correctamente']);
    }

    public function destroy(Product $product)
    {
        ImageStorage::delete($product->image);
        $product->delete();

        return redirect()->route('products.index')->with(['msg' => 'Producto eliminado correctamente']);
    }
}
