<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolePermission = [
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
        ];

        $permissions = [
            ...Admin::$permissions,
            ...User::$permissions,
            ...Setting::$permissions,
            ...$rolePermission,
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'admin']);
        }
    }
}
