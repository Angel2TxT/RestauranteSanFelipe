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
            $imagen = $request->file('image');
            $nameImage = "images/sliders/" . uniqid() . '.' . $imagen->guessExtension();
            $ruta = public_path("images/sliders/");
            $imagen->move($ruta, $nameImage);
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
            // Eliminar imagen anterior si existe
            if ($slider->image && file_exists(public_path($slider->image))) {
                unlink(public_path($slider->image));
            }

            $imagen = $request->file('image');
            $nameImage = "images/sliders/" . uniqid() . '.' . $imagen->guessExtension();
            $ruta = public_path("images/sliders/");
            $imagen->move($ruta, $nameImage);
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
