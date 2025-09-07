<?php

namespace App\Http\Controllers\Admin\WebSiteSettings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\WebSiteSettings\WhyChooseUsService;
use App\Http\Requests\Admin\WhyChooseUs\UpdateWhyChooseUsRequest;

class WhyChooseUsController extends Controller
{
    public function __construct(private WhyChooseUsService $whyChooseUsService)
    {
    }

    public function index()
    {
        $whyChooseUs = $this->whyChooseUsService->index();
        return view('dashboard.pages.website_setting.why_choose_us.index', compact('whyChooseUs'));
    }

    public function update(UpdateWhyChooseUsRequest $request)
    {
        $this->whyChooseUsService->update($request);
        return back()
            ->with('Success', __('admin.updated_successfully'));
    }
}
