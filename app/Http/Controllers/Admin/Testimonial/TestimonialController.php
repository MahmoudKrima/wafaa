<?php

namespace App\Http\Controllers\Admin\Testimonial;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Enum\ActivationStatusEnum;
use App\Http\Controllers\Controller;
use App\Services\Admin\Testimonial\TestimonialService;
use App\Http\Requests\Admin\Testimonial\StoreTestimonialRequest;
use App\Http\Requests\Admin\Testimonial\UpdateTestimonialRequest;

class TestimonialController extends Controller
{
    public function __construct(private TestimonialService $testimonialService)
    {
    }

    public function index()
    {
        $testimonials = $this->testimonialService->index();
        return view('dashboard.pages.testimonial.index', compact('testimonials'));
    }

    public function create()
    {
        $status = ActivationStatusEnum::cases();
        return view('dashboard.pages.testimonial.create', compact('status'));
    }

    public function store(StoreTestimonialRequest $request)
    {
        $this->testimonialService->store($request);
        return back()
            ->with('Success', __('admin.created_successfully'));
    }

    public function edit(Testimonial $testimonial)
    {
        $status = ActivationStatusEnum::cases();
        return view('dashboard.pages.testimonial.edit', compact('testimonial', 'status'));
    }

    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial)
    {
        $this->testimonialService->update($request, $testimonial);
        return back()
            ->with('Success', __('admin.updated_successfully'));
    }

    public function updateStatus(Testimonial $testimonial)
    {
        $this->testimonialService->updateStatus($testimonial);
        return back()
            ->with('Success', __('admin.updated_successfully'));
    }

    public function delete(Testimonial $testimonial)
    {
        $this->testimonialService->delete($testimonial);
        return back()
            ->with('Success', __('admin.deleted_successfully'));
    }
}
