<?php

namespace App\Http\Controllers\Admin\WebSiteSettings;

use App\Models\Term;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\UpdateTermRequest;
use App\Services\Admin\WebSiteSettings\TermsService;

class TermsController extends Controller
{
    public function __construct(private TermsService $termsService) {}

    public function index()
    {
        $term = $this->termsService->getAll();
        return view('dashboard.pages.terms.index', compact('term'));
    }

    public function update(UpdateTermRequest $request, Term $term)
    {
        $this->termsService->updateSettings($request, $term);
        return back()
            ->with('Success', __('admin.updated_successfully'));
    }
}
