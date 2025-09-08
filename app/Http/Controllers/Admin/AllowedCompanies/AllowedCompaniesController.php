<?php

namespace App\Http\Controllers\Admin\AllowedCompanies;

use App\Models\AllowedCompany;
use App\Http\Controllers\Controller;
use App\Services\Admin\AllowedCompanies\AllowedCompaniesService;

class AllowedCompaniesController extends Controller
{
    public function __construct(private AllowedCompaniesService $allowedCompanyService)
    {
    }

    public function index()
    {
        $allowedCompanies = $this->allowedCompanyService->getAll();
        return view('dashboard.pages.allowed_companies.index', compact('allowedCompanies'));
    }


    public function updateStatus(AllowedCompany $allowedCompany)
    {
        if (!$allowedCompany) {
            return back()
                ->with('Error', __('admin.not_found_data'));
        } elseif ($allowedCompany->admin_id != getAdminIdOrCreatedBy()) {
            return back()
                ->with('Error', __('admin.not_found_data'));
        }
        $this->allowedCompanyService->updateAllowedCompanyStatus($allowedCompany);
        return back()
            ->with("Success", __('admin.updated_successfully'));
    }
}
