<?php

namespace App\Http\Controllers\Admin\WebSiteSettings;

use App\Models\AdminSetting;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\UpdateShippingPricesSettingRequest;
use App\Services\Admin\WebSiteSettings\AdminSettingsService;

class AdminSettingsController extends Controller
{
    public function __construct(private AdminSettingsService $adminSettingsService)
    {
    }

    public function index()
    {
        $adminSetting = $this->adminSettingsService->getAll();
        return view('dashboard.pages.admin_settings.index', compact('adminSetting'));
    }

    public function update(UpdateShippingPricesSettingRequest $request, AdminSetting $adminSetting)
    {
        if (!$adminSetting || $adminSetting->admin_id != getAdminIdOrCreatedBy()) {
            return back()
                ->with('Error', __('admin.not_found_data'));
        }
        $this->adminSettingsService->updateSettings($request, $adminSetting);
        return back()
            ->with('Success', __('admin.updated_successfully'));
    }

}
