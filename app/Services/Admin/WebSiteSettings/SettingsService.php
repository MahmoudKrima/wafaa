<?php

namespace App\Services\Admin\WebSiteSettings;

use App\Models\Setting;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    use ImageTrait;

    function getAll()
    {
        return Setting::get();
    }

    function updateSettings($request)
    {
        $data = $request->validated();
        $data['logo'] = ImageTrait::updateImage(Setting::where('key', 'logo')->value('value'), 'setting/logo', 'logo');
        $data['fav_icon'] = ImageTrait::updateImage(Setting::where('key', 'fav_icon')->value('value'), 'setting/fav_icon', 'fav_icon');
        foreach ($data as $key => $value) {
            Setting::where('key', $key)->update(
                ['value' => $value]
            );
        }
        Cache::forget('settings');
        $settingsData =  Setting::select('key', 'value')
            ->get()
            ->map(function ($i) {
                return [
                    $i->key => $i->value
                ];
            })
            ->collapse()
            ->toArray();
        Cache::forever('settings', $settingsData);
    }
}
