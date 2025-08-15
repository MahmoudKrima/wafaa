<?php

namespace App\Services\Admin\FrontSetting\About;

use App\Traits\ImageTrait;
use App\Traits\TranslateTrait;
use App\Models\AboutItem;

class AboutItemService
{
    use ImageTrait, TranslateTrait;

    function getAll()
    {
        return AboutItem::orderBy('id')->get();
    }

    function updateAboutItem($request, $aboutItem)
    {
        $data = $request->validated();
        $aboutItem->update($data);
    }
}
