<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use App\Support\ImageStorage;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::orderByDesc('id')->paginate(5);
        $banner = Slider::orderByDesc('id')->first();

        return view('sliders.index', compact('sliders', 'banner'));
    }

    public function create()
    {
        return view('sliders.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'link' => 'nullable|max:255',
            'text_link' => 'nullable|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $slider = new Slider();
        $slider->title = $request->title;
        $slider->description = $request->description;
        $slider->link = $request->link;
        $slider->text_link = $request->text_link;
        $slider->image = ImageStorage::store($request->file('image'), 'sliders');
        $slider->save();

        return redirect()->route('sliders.index')->with(['msg' => 'Slider creado correctamente']);
    }

    public function edit(Slider $slider)
    {
        return view('sliders.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'link' => 'nullable|max:255',
            'text_link' => 'nullable|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $slider->title = $request->title;
        $slider->description = $request->description;
        $slider->link = $request->link;
        $slider->text_link = $request->text_link;

        if ($request->hasFile('image')) {
            $slider->image = ImageStorage::store($request->file('image'), 'sliders', $slider->image);
        }

        $slider->save();

        return redirect()->route('sliders.index')->with(['msg' => 'Slider editado correctamente']);
    }

    public function destroy(Slider $slider)
    {
        ImageStorage::delete($slider->image);
        $slider->delete();

        return redirect()->route('sliders.index')->with(['msg' => 'Slider eliminado correctamente']);
    }
}
