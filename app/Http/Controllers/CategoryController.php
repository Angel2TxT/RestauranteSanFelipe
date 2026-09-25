<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Support\ImageStorage;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderByDesc('id')->paginate(6);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'icon' => 'required|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $category = new Category();
        $category->name = $request->get('name');
        $category->icon = $request->get('icon');
        $category->image = ImageStorage::store($request->file('image'), 'categories');
        $category->save();

        return redirect()->route('categories.index')->with(['msg' => 'Categoria creada correctamente']);
    }

    public function show(Category $category)
    {
        $category->load(['products' => fn ($q) => $q->latest('id')]);

        $otherCategories = Category::query()
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->take(4)
            ->get();

        return view('categories.show', compact('category', 'otherCategories'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'icon' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $category->name = $request->get('name');
        $category->icon = $request->get('icon');

        if ($request->hasFile('image')) {
            $category->image = ImageStorage::store($request->file('image'), 'categories', $category->image);
        }

        $category->save();

        return redirect()->route('categories.index')->with(['msg' => 'Categoria editada correctamente']);
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return redirect()->route('categories.index')
                ->with('error', 'No se puede eliminar esta categoría porque tiene productos asociados.');
        }

        ImageStorage::delete($category->image);
        $category->delete();

        return redirect()->route('categories.index')->with('msg', 'Categoría eliminada correctamente');
    }
}
