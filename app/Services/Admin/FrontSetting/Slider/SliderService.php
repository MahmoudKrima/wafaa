<?php

namespace App\Services\Admin\FrontSetting\Slider;

use App\Traits\ImageTrait;
use App\Traits\TranslateTrait;
use App\Models\Slider;
use Illuminate\Support\Facades\Storage;

class SliderService
{
    use ImageTrait, TranslateTrait;

    function getAll()
    {
        return Slider::orderBy('id', 'desc')->paginate();
    }

    function storeSlider($request)
    {
        $data = $request->validated();
        $data['image'] = ImageTrait::uploadImage($request->file('image'), 'sliders');
        Slider::create($data);
    }

    function updateSlider($request, $slider)
    {
        $data = $request->validated();
        $data['image'] = ImageTrait::updateImage($slider->image, 'sliders', 'image');
        $slider->update($data);
    }

    function updateSliderStatus($slider)
    {
        if ($slider->status->value == 'active') {
            $slider->update([
                'status' => 'deactive',
            ]);
        } else {
            $slider->update([
                'status' => 'active',
            ]);
        }
    }

    function deleteSlider($slider)
    {
        if ($slider->image) {
            Storage::disk('public')->delete($slider->image);
        }
        $slider->delete();
    }
}
