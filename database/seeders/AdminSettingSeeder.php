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
                'phone' => '0512312584',
                'email' => 'admin@gmail.com',
                'whatsapp' => '0512312586',
                'admin_id' => 1,
            ],
            [
                'extra_weight_price' => 0,
                'cash_on_delivery_price' => 0,
                'phone' => '0512312585',
                'email' => 'admin2@gmail.com',
                'whatsapp' => '0512312587',
                'admin_id' => 2,
            ]
        ];

        foreach ($adminSettings as $adminSetting) {
            AdminSetting::create($adminSetting);
        }
    }
}
