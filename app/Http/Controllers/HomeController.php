<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
         $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $sliders = Slider::all();
        $categories = Category::all();
        $banner = Slider::inRandomOrder()->first();
        $products = Product::take(4)->get();
        
        return view('home',compact('sliders','categories','banner','products'));
    }

    public function shop(Request $request)
    {
        // Obtener el parámetro de la categoría si está presente
        $categoryId = $request->input('category');
        $search = $request->input('search');
        $sort = $request->input('sort');
    
        // Obtener las categorías para el filtro
        $categories = Category::all();
    
        // Obtener los productos con filtrado y ordenamiento
        $products = Product::when($categoryId, function ($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->when($sort, function ($query) use ($sort) {
                return $query->orderBy('price', $sort);
            })
            ->orderBy('id', 'desc') // Aseguramos que se ordenen por ID si no se ha seleccionado otro criterio
            ->paginate(4);
    
        // Pasar los productos y categorías a la vista
        return view('shop', compact('products', 'categories'));
    }
    


}
