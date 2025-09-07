<?php

namespace App\Http\Controllers\Admin\FrontSetting\About;

use App\Http\Controllers\Controller;
use App\Services\Admin\FrontSetting\About\AboutService;
use App\Services\Admin\FrontSetting\About\AboutItemService;
use App\Http\Requests\Admin\FrontSetting\About\UpdateAboutRequest;
use App\Http\Requests\Admin\FrontSetting\About\UpdateAboutItemRequest;
use App\Models\About;
use App\Models\AboutItem;

class AboutController extends Controller
{
    public function __construct(
        private AboutService $aboutService,
        private AboutItemService $aboutItemService
    ) {
    }

    public function index()
    {
        $about = $this->aboutService->getAbout();
        $aboutItems = $this->aboutItemService->getAll();

        return view('dashboard.pages.website_setting.about.index', compact('about', 'aboutItems'));
    }

    public function edit()
    {
        $about = $this->aboutService->getAbout();
        $aboutItems = $this->aboutItemService->getAll();

        return view('dashboard.pages.website_setting.about.edit', compact('about', 'aboutItems'));
    }

    public function updateAbout(UpdateAboutRequest $request, About $about)
    {
        $this->aboutService->updateAbout($request, $about);
        return back()->with("Success", __('admin.updated_successfully'));
    }

    public function updateAboutItem(UpdateAboutItemRequest $request)
    {
        $this->aboutItemService->updateAboutItem($request);
        return back()
            ->with("Success", __('admin.updated_successfully'));
    }
}
