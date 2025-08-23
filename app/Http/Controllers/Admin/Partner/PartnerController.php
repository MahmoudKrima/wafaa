<?php

namespace App\Http\Controllers\Admin\Partner;

use App\Models\Partner;
use Illuminate\Http\Request;
use App\Enum\ActivationStatusEnum;
use App\Http\Controllers\Controller;
use App\Services\Admin\Partner\PartnerService;
use App\Http\Requests\Admin\Partner\StorePartnerRequest;
use App\Http\Requests\Admin\Partner\UpdatePartnerRequest;

class PartnerController extends Controller
{
    public function __construct(private PartnerService $partnerService)
    {
    }

    public function index()
    {
        $partners = $this->partnerService->index();
        return view('dashboard.pages.partner.index', compact('partners'));
    }

    public function create()
    {
        $status = ActivationStatusEnum::cases();
        return view('dashboard.pages.partner.create', compact('status'));
    }

    public function store(StorePartnerRequest $request)
    {
        $this->partnerService->store($request);
        return back()
            ->with("Success", __('admin.created_successfully'));
    }

    public function edit(Partner $partner)
    {
        $status = ActivationStatusEnum::cases();
        return view('dashboard.pages.partner.edit', compact('partner', 'status'));
    }

    public function update(UpdatePartnerRequest $request, Partner $partner)
    {
        $this->partnerService->update($request, $partner);
        return back()
            ->with("Success", __('admin.updated_successfully'));
    }

    public function updateStatus(Partner $partner)
    {
        $this->partnerService->updateStatus($partner);
        return back()
            ->with("Success", __('admin.updated_successfully'));
    }

    public function delete(Partner $partner)
    {
        $this->partnerService->delete($partner);
        return back()
            ->with("Success", __('admin.deleted_successfully'));
    }
}