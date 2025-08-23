<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminSetting;

class AdminSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminSettings = [
            [
                'extra_weight_price' => 0,
                'cash_on_delivery_price' => 0,
                'admin_id' => 1,
            ],
            [
                'extra_weight_price' => 0,
                'cash_on_delivery_price' => 0,
                'admin_id' => 2,
            ]
        ];

        foreach ($adminSettings as $adminSetting) {
            AdminSetting::create($adminSetting);
        }
    }
}
