<?php

namespace App\Services\Admin\WebSiteSettings;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    function getAll()
    {
        $admin_id = getAdminIdOrCreatedBy();
        return Role::where('id', '!=', 1)
            ->where('admin_id', $admin_id)
            ->orderBy('id', 'desc')
            ->paginate();
    }

    function filterRole($request)
    {
        $request->validated();
        return Role::where('name', 'LIKE', '%' . $request->input('name') . '%')
            ->where('id', '!=', 1)
            ->where('admin_id', getAdminIdOrCreatedBy())
            ->orderBy('id', 'desc')
            ->paginate()
            ->withQueryString();
    }

    function getPermissions()
    {
        return Permission::get();
    }

    function storeRole($request)
    {
        $data = $request->validated();
        $data['admin_id'] = getAdminIdOrCreatedBy();
        $role = Role::create($data);
        $role->permissions()->sync($data['permission_id']);
    }

    function updateRole($request, $role)
    {
        $data = $request->validated();
        $role->update(['name' => $data['name'], 'guard_name' => 'admin']);
        $role->permissions()->sync($data['permission_id']);
    }

    public function delete($role)
    {
        if ($role->users->count() > 0) {
            return false;
        }
        $role->delete();
        return true;
    }
}
