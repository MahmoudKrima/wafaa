<?php

namespace App\Services\Admin\WebSiteSettings;

use App\Models\AdminSetting;

class AdminSettingsService
{


    function getAll()
    {
        return AdminSetting::where('admin_id', getAdminIdOrCreatedBy())
        ->first();
    }

    function updateSettings($request, $adminSetting)
    {
        $data = $request->validated();
        $adminSetting->update($data);
        return $adminSetting;
    }
}
