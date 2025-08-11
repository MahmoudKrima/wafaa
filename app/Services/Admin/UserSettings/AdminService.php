<?php

namespace App\Services\Admin\UserSettings;

use App\Models\Admin;
use App\Traits\ImageTrait;
use App\Filters\NameFilter;
use App\Filters\RoleFilter;
use App\Filters\EmailFilter;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Filters\ActivationStatusFilter;
use App\Filters\PhoneFilter;
use Illuminate\Support\Facades\Storage;

class AdminService
{
    use ImageTrait;

    function getAll()
    {
        return Admin::withAllRelations()
            ->where('id', '!=', auth('admin')->id())
            ->where('created_by', getAdminIdOrCreatedBy())
            ->orderBy('id', 'desc')
            ->paginate();
    }

    function getRoles()
    {
        $admin_id = getAdminIdOrCreatedBy();
        return Role::where('id', '!=', 1)
            ->where('admin_id', $admin_id)
            ->get();
    }


    function filterAdmin($request)
    {
        $request->validated();
        return app(Pipeline::class)
            ->send(Admin::query())
            ->through([
                NameFilter::class,
                EmailFilter::class,
                RoleFilter::class,
                PhoneFilter::class,
                ActivationStatusFilter::class,
            ])
            ->thenReturn()
            ->withAllRelations()
            ->where('id', '!=', auth('admin')->id())
            ->where('created_by', getAdminIdOrCreatedBy())
            ->orderBy('id', 'desc')
            ->paginate()
            ->withQueryString();
    }

    function storeAdmin($request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            if (!isset($data['image'])) {
                $data['image'] = null;
            } else {
                $data['image'] = ImageTrait::uploadImage($request->file('image'), 'admin/images');
            }
            $role = Role::where('id', $data['role'])
                ->first();
            unset($data['role']);
            $data['added_by'] = auth('admin')->id();
            $data['created_by'] = getAdminIdOrCreatedBy();
            $admin = Admin::create($data);
            $admin->assignRole($role->name);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    function udpateAdmin($request, $admin)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            if (!isset($data['password'])) {
                unset($data['password']);
            }
            $data['image'] = ImageTrait::updateImage($admin->image, 'admin/images', 'image');
            $admin->update($data);
            $role = Role::where('id', $data['role'])
                ->first();
            if ($role) {
                $admin->syncRoles([]);
                $admin->assignRole($role->name);
            }
            unset($data['role']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    function updateAdminStatus($admin)
    {
        if ($admin->status->value == 'active') {
            $admin->update([
                'status' => 'deactive',
            ]);
        } else {
            $admin->update([
                'status' => 'active',
            ]);
        }
    }

    function deleteAdmin($admin)
    {
        Storage::disk('public')->delete($admin->image);
        $admin->delete();
    }
}
