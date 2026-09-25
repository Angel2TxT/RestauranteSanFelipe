<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $sliders = Slider::orderBy('id')->get();
        $categories = Category::orderBy('name')->get();
        $banner = Slider::inRandomOrder()->first();
        $products = Product::with('category')->latest('id')->take(4)->get();

        return view('home', compact('sliders', 'categories', 'banner', 'products'));
    }

    public function shop(Request $request)
    {
        $categoryId = $request->input('category');
        $search = $request->input('search');
        $sort = in_array($request->input('sort'), ['asc', 'desc'], true)
            ? $request->input('sort')
            : null;

        $categories = Category::orderBy('name')->get();

        $products = Product::with('category')
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->when($search, fn ($query) => $query->where('name', 'like', '%' . $search . '%'))
            ->when($sort, fn ($query) => $query->orderBy('price', $sort))
            ->orderByDesc('id')
            ->paginate(50)
            ->withQueryString();

        if ($request->ajax() || $request->boolean('ajax')) {
            return view('shop._products', compact('products'));
        }

        return view('shop', compact('products', 'categories'));
    }
}
