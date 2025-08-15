<?php

namespace App\Http\Controllers\Admin\FrontSettings\Slider;

use App\Enum\ActivationStatusEnum;
use App\Http\Controllers\Controller;
use App\Services\Admin\FrontSetting\Slider\SliderService;
use App\Http\Requests\Admin\Slider\StoreSliderRequest;
use App\Http\Requests\Admin\Slider\UpdateSliderRequest;
use App\Models\Slider;

class SliderController extends Controller
{
    public function __construct(private SliderService $sliderService) {}

    public function index()
    {
        $sliders = $this->sliderService->getAll();
        $status = ActivationStatusEnum::cases();
        return view('dashboard.pages.website_setting.slider.index', compact('sliders', 'status'));
    }

    public function create()
    {
        $status = ActivationStatusEnum::cases();
        return view('dashboard.pages.website_setting.slider.create', compact('status'));
    }

    public function store(StoreSliderRequest $request)
    {
        $this->sliderService->storeSlider($request);
        return back()
            ->with("Success", __('admin.created_successfully'));
    }

    public function edit(Slider $slider)
    {
        $status = ActivationStatusEnum::cases();
        return view('dashboard.pages.website_setting.slider.edit', compact('status', 'slider'));
    }

    public function update(UpdateSliderRequest $request, Slider $slider)
    {
        $this->sliderService->updateSlider($request, $slider);
        return back()
            ->with("Success", __('admin.updated_successfully'));
    }

    public function updateStatus(Slider $slider)
    {
        $this->sliderService->updateSliderStatus($slider);
        return back()
            ->with("Success", __('admin.updated_successfully'));
    }

    public function delete(Slider $slider)
    {
        $this->sliderService->deleteSlider($slider);
        return back()
            ->with("Success", __('admin.deleted_successfully'));
    }
}
