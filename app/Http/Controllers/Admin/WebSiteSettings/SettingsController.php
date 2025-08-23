<?php

namespace App\Http\Controllers\Admin\WebSiteSettings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\WebSiteSettings\SettingsService;
use App\Http\Requests\Admin\Setting\UpdateSettingRequest;

class SettingsController extends Controller
{
    public function __construct(private SettingsService $settingsService) {}

    public function index()
    {
        $settings = $this->settingsService->getAll();
        return view('dashboard.pages.settings.index', compact('settings'));
    }

    public function update(UpdateSettingRequest $request)
    {
        $this->settingsService->updateSettings($request);
        return back()
            ->with('Success', __('admin.updated_successfully'));
    }

}
