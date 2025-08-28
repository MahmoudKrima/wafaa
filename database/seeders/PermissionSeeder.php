<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\User;
use App\Models\Admin;
use App\Models\Banks;
use App\Models\Setting;
use App\Models\WalletLog;
use App\Models\UserWallet;
use App\Models\Transaction;
use App\Models\Slider;
use App\Models\About;
use App\Models\AboutItem;
use App\Models\Reciever;
use App\Models\UserShippingPrice;
use App\Models\AdminSetting;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\Contact;
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
            ...Slider::$permissions,
            ...About::$permissions,
            ...AboutItem::$permissions,
            ...Reciever::$permissions,
            ...UserShippingPrice::$permissions,
            ...Partner::$permissions,
            ...AdminSetting::$permissions,
            ...Service::$permissions,
            ...Testimonial::$permissions,
            ...Contact::$permissions,
            ...$rolePermission,
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'admin']);
        }
    }
}
