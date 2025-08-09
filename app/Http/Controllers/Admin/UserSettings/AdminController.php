<?php

namespace App\Http\Controllers\Admin\UserSettings;

use App\Enum\ActivationStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Admin\SearchAdminRequest;
use App\Http\Requests\Admin\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\Admin\UpdateAdminRequest;
use App\Models\Admin;
use App\Services\Admin\UserSettings\AdminService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(private AdminService $adminService) {}

    public function index()
    {
        $admins = $this->adminService->getAll();
        $status = ActivationStatusEnum::cases();
        $roles = $this->adminService->getRoles();
        return view('dashboard.pages.admins.index', compact('admins', 'status', 'roles'));
    }

    public function search(SearchAdminRequest $request)
    {
        $admins = $this->adminService->filterAdmin($request);
        $status = ActivationStatusEnum::cases();
        $roles = $this->adminService->getRoles();
        return view('dashboard.pages.admins.index', compact('admins', 'status', 'roles'));
    }

    public function edit(Admin $admin)
    {
        $roles = $this->adminService->getRoles();
        $status = ActivationStatusEnum::cases();
        return view('dashboard.pages.admins.edit', compact('roles', 'admin', 'status'));
    }

    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        try {
            $this->adminService->udpateAdmin($request, $admin);
            return back()
                ->with("Success", __('admin.updated_successfully'));
        } catch (\Exception $e) {
            return back()
                ->with("Error", __('admin.try_agian_later'));
        }
    }

    public function updateStatus(Admin $admin)
    {
        $this->adminService->updateAdminStatus($admin);
        return back()
            ->with("Success", __('admin.updated_successfully'));
    }

    public function create()
    {
        $roles = $this->adminService->getRoles();
        return view('dashboard.pages.admins.create', compact('roles'));
    }

    public function store(StoreAdminRequest $request)
    {
        try {
            $this->adminService->storeAdmin($request);
            return back()
                ->with("Success", __('admin.created_successfully'));
        } catch (\Exception $e) {
            return back()
                ->with("Error", __('admin.try_agian_later'));
        }
    }

    public function delete(Admin $admin)
    {
        $this->adminService->deleteAdmin($admin);
        return back()
            ->with("Success", __('admin.deleted_successfully'));
    }
}
