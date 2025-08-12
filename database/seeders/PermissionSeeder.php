<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Banks;
use App\Models\Setting;
use App\Models\WalletLog;
use App\Models\UserWallet;
use App\Models\Transaction;
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
            ...UserWallet::$permissions,
            ...Banks::$permissions,
            ...Transaction::$permissions,
            ...Setting::$permissions,
            ...WalletLog::$permissions,
            ...$rolePermission,
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'admin']);
        }
    }
}
