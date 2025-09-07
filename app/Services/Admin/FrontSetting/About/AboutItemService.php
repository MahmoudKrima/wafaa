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

    function updateAboutItem($request)
    {
        $data = $request->validated();
        foreach ($data['items'] as $item) {
            AboutItem::where('id', $item['id'])
                ->update($item);
        }
    }
}
