<?php

namespace App\Http\Controllers\Admin\FrontSetting\About;

use App\Http\Controllers\Controller;
use App\Services\Admin\FrontSetting\About\AboutService;
use App\Http\Requests\Admin\FrontSetting\About\UpdateAboutRequest;
use App\Models\About;

class AboutController extends Controller
{
    public function __construct(
        private AboutService $aboutService,
    ) {}

    public function index()
    {
        $about = $this->aboutService->getAbout();
        $aboutItems = $this->aboutService->getAll();

        return view('dashboard.pages.website_setting.about.index', compact('about', 'aboutItems'));
    }

    public function edit()
    {
        $about = $this->aboutService->getAbout();
        $aboutItems = $this->aboutService->getAll();

        return view('dashboard.pages.website_setting.about.edit', compact('about', 'aboutItems'));
    }

    public function updateAbout(UpdateAboutRequest $request, About $about)
    {
        $this->aboutService->updateAbout($request, $about);
        return back()->with("Success", __('admin.updated_successfully'));
    }
}
