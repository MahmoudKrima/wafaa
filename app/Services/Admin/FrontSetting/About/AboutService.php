<?php

namespace App\Services\Admin\FrontSetting\About;

use App\Traits\ImageTrait;
use App\Traits\TranslateTrait;
use App\Models\About;

class AboutService
{
    use ImageTrait, TranslateTrait;

    function getAbout()
    {
        return About::first();
    }


    function updateAbout($request, $about)
    {
        $data = $request->validated();
        $data['image'] = ImageTrait::updateImage($about->image, 'about', 'image');
        $about->update($data);
    }
}
