<?php

namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Controller;
use App\Services\Admin\Service\ServiceService;
use App\Enum\ActivationStatusEnum;
use App\Models\Service;
use App\Http\Requests\Admin\Service\StoreServiceRequest;
use App\Http\Requests\Admin\Service\UpdateServiceRequest;

class ServiceController extends Controller
{
    public function __construct(private ServiceService $serviceService)
    {
    }

    public function index()
    {
        $services = $this->serviceService->index();
        return view('dashboard.pages.service.index', compact('services'));
    }

    public function create()
    {
        $status = ActivationStatusEnum::cases();
        return view('dashboard.pages.service.create', compact('status'));
    }

    public function store(StoreServiceRequest $request)
    {
        $this->serviceService->store($request);
        return back()
            ->with("Success", __('admin.created_successfully'));
    }

    public function edit(Service $service)
    {
        $status = ActivationStatusEnum::cases();
        return view('dashboard.pages.service.edit', compact('service', 'status'));
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $this->serviceService->update($request, $service);
        return back()
            ->with("Success", __('admin.updated_successfully'));
    }

    public function updateStatus(Service $service)
    {
        $this->serviceService->updateStatus($service);
        return back()
            ->with("Success", __('admin.updated_successfully'));
    }

    public function delete(Service $service)
    {
        $this->serviceService->delete($service);
        return back()
            ->with("Success", __('admin.deleted_successfully'));
    }
}
