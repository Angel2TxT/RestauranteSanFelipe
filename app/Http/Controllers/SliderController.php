<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sliders = Slider::orderBy('id', 'desc')->paginate(5);
        $banner = Slider::orderBy('id', 'desc')->first(); // Obtener el último slider como banner

        return view('sliders.index', compact('sliders', 'banner'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sliders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'link' => 'nullable|max:255',
            'text_link' => 'nullable|max:255',
            'image' => 'required|image|mimes:jpeg,png|max:1024'
        ]);

        $slider = new Slider();
        $slider->title = $request->title;
        $slider->description = $request->description;
        $slider->link = $request->link;
        $slider->text_link = $request->text_link;

        if ($request->hasFile('image')) {
            // Eliminar la imagen anterior si existe
            $path = public_path('images/sliders/') . $slider->image;
            
            // Verificamos si la imagen existe y no es nula antes de eliminarla
            if (file_exists($path) && $slider->image !== null) {
                unlink($path); // Elimina la imagen anterior
            }
        
            // Subir la nueva imagen
            $imagen = $request->file('image');
            $nameImage = 'images/sliders/' . uniqid() . '.' . $imagen->guessExtension();
        
            // Asegurarse de que la carpeta de destino exista
            $ruta = public_path('images/sliders/');
            if (!file_exists($ruta)) {
                mkdir($ruta, 0777, true); // Crea el directorio si no existe
            }
        
            // Mover la nueva imagen a la carpeta correspondiente
            $imagen->move($ruta, $nameImage);
        
            // Actualizar el nombre de la imagen en el modelo
            $slider->image = $nameImage;
        }
        $slider->save();

        return redirect()->route('sliders.index')->with(["msg" => "Slider creado correctamente"]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Slider $slider)
    {
        return view('sliders.edit', compact('slider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Slider $slider)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'link' => 'nullable|max:255',
            'text_link' => 'nullable|max:255',
            'image' => 'nullable|image|mimes:jpeg,png|max:1024'
        ]);

        $slider->title = $request->title;
        $slider->description = $request->description;
        $slider->link = $request->link;
        $slider->text_link = $request->text_link;

        if ($request->hasFile('image')) {
            // Eliminar la imagen anterior si existe y si no es la imagen predeterminada
            if ($slider->image && file_exists(public_path($slider->image))) {
                unlink(public_path($slider->image)); // Elimina la imagen anterior
            }
        
            // Subir la nueva imagen
            $imagen = $request->file('image');
            $nameImage = 'images/sliders/' . uniqid() . '.' . $imagen->guessExtension();
        
            // Asegurarse de que la carpeta de destino exista
            $ruta = public_path('images/sliders/');
            if (!file_exists($ruta)) {
                mkdir($ruta, 0777, true); // Crea el directorio si no existe
            }
        
            // Mover la nueva imagen a la carpeta correspondiente
            $imagen->move($ruta, $nameImage);
        
            // Actualizar el nombre de la imagen en el modelo
            $slider->image = $nameImage;
        }
        

        $slider->save();

        return redirect()->route('sliders.index')->with(["msg" => "Slider editado correctamente"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slider $slider)
    {
        if ($slider->image && file_exists(public_path($slider->image))) {
            unlink(public_path($slider->image));
        }

        $slider->delete();

        return redirect()->route('sliders.index')->with(["msg" => "Slider eliminado correctamente"]);
    }
}
