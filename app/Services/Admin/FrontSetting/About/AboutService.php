<?php

namespace App\Services\Admin\FrontSetting\About;

use App\Models\About;
use App\Models\AboutItem;
use App\Traits\ImageTrait;
use App\Traits\TranslateTrait;

class AboutService
{
    use ImageTrait, TranslateTrait;

    function getAbout()
    {
        return About::first();
    }

    function getAll()
    {
        return AboutItem::orderBy('id')->get();
    }

    function updateAbout($request, $about)
    {
        $data = $request->validated();
        $data['image'] = ImageTrait::updateImage($about->image, 'about', 'image');
        $about->update($data);
        foreach ($data['items'] as $item) {
            AboutItem::where('id', $item['id'])
                ->update($item);
        }
    }
}
