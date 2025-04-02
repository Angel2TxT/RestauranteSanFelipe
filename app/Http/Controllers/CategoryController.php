<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('id','desc')->paginate(3);
        return view('categories.index',compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|max:255',
            'icon' => 'required|max:255',
            'image' => 'image|mimes:jpeg,png|max:1024|required'

        ]);

        $category = new Category();
        $category->name = $request->get('name');
        $category->icon = $request->get('icon');

        if($request->hasFile('image')) {

            $imagen = $request->file('image');
            $nameImage = "images/categories/".uniqid().'.'.$imagen->guessExtension();
            
            // Usa public_path() para obtener la ruta correcta
            $ruta = public_path("images/categories/");
        
            // Asegúrate de que el directorio exista
            if (!file_exists($ruta)) {
                mkdir($ruta, 0777, true); // Crea el directorio si no existe
            }
        
            // Mover la imagen a la carpeta
            $imagen->move($ruta, $nameImage);
        
            // Guarda el nombre de la imagen en el modelo
            $category->image = $nameImage;
        }
        

        $category->save();

        return redirect()->route('categories.index')->with(["msg"=>"Categoria creada correctamente"]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return view('categories.show',compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit',compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $this->validate($request,[
            'name' => 'required|max:255',
            'icon' => 'required|max:255',
            'image' => 'image|mimes:jpeg,png|max:1024|nullable'

        ]);

        // $slider = new Slider();
        $category->name = $request->get('name');
        $category->icon = $request->get('icon');

        if($request->hasFile('image')) {

            // Ruta de la imagen actual (si existe)
            $path = public_path("images/categories/").$category->image;
        
            // Si la imagen existe y no es null, la eliminamos
            if (file_exists($path) && $category->image !== null) {
                unlink($path); // Elimina la imagen anterior
            }
        
            // Subir la nueva imagen
            $imagen = $request->file('image');
            $nameImage = "images/categories/".uniqid().'.'.$imagen->guessExtension();
            
            // Asegúrate de que el directorio de categorías exista
            $ruta = public_path("images/categories/");
            if (!file_exists($ruta)) {
                mkdir($ruta, 0777, true); // Crea el directorio si no existe
            }
        
            // Mover la nueva imagen a la carpeta
            $imagen->move($ruta, $nameImage);
        
            // Actualizar el nombre de la imagen en el modelo
            $category->image = $nameImage;
        }
        

        $category->update();

        return redirect()->route('categories.index')->with(["msg"=>"Categoria editada correctamente"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Verificar si la categoría tiene productos asociados
        if ($category->products()->count() > 0) {
            // Si tiene productos, redirige de nuevo con un mensaje de error
            return redirect()->route('categories.index')->with('error', 'No se puede eliminar esta categoría porque tiene productos asociados.');
        }
    
        // Si no tiene productos, procede a eliminar la imagen y la categoría
        $path = public_path() . '/' . $category->image;
    
        if (file_exists($path) && $category->image !== null) {
            unlink($path);
        }
    
        // Eliminar la categoría
        $category->delete();
    
        // Redirigir a la lista de categorías con un mensaje de éxito
        return redirect()->route('categories.index')->with('msg', 'Categoría eliminada correctamente');
    }
    
}
