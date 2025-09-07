<?php

namespace App\Http\Controllers\Admin\WebSiteSettings;

use Illuminate\Http\Request;
use App\Enum\ActivationStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Faq\StoreFaqRequest;
use App\Http\Requests\Admin\Faq\UpdateFaqRequest;
use App\Services\Admin\WebSiteSettings\FaqService;
use App\Models\Faq;

class FaqController extends Controller
{
    public function __construct(private FaqService $faqService)
    {
    }

    public function index()
    {
        $faqs = $this->faqService->index();
        return view('dashboard.pages.website_setting.faq.index', compact('faqs'));
    }

    public function create()
    {
        $status = ActivationStatusEnum::cases();
        return view('dashboard.pages.website_setting.faq.create', compact('status'));
    }

    public function store(StoreFaqRequest $request)
    {
        $this->faqService->store($request);
        return back()
            ->with("Success", __('admin.created_successfully'));
    }

    public function edit(Faq $faq)
    {
        $status = ActivationStatusEnum::cases();
        return view('dashboard.pages.website_setting.faq.edit', compact('faq', 'status'));
    }

    public function update(UpdateFaqRequest $request, Faq $faq)
    {
        $this->faqService->update($request, $faq);
        return back()
            ->with("Success", __('admin.updated_successfully'));
    }

    public function updateStatus(Faq $faq)
    {
        $this->faqService->updateStatus($faq);
        return back()
            ->with("Success", __('admin.updated_successfully'));
    }

    public function delete(Faq $faq)
    {
        $this->faqService->delete($faq);
        return back()
            ->with("Success", __('admin.deleted_successfully'));
    }
}
