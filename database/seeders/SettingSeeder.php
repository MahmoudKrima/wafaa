<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Cache;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cache::forget('settings');
        $settings = [
            'app_name_ar' => 'شحن',
            'app_name_en' => 'Shahn',
            'address_ar' => 'عنوان',
            'address_en' => 'Address',
            'phone' => '0512312584',
            'email' => 'admin@gmail.com',
            'whatsapp' => '0512312584',
            'facebook' => 'https://www.facebook.com',
            'twitter' => 'https://twitter.com',
            'instagram' => 'https://www.instagram.com',
            'tiktok' => 'https://www.tiktok.com',
            'snapchat' => 'https://www.snapchat.com',
            'logo' => 'defaults/admin.jpg',
            'fav_icon' => 'defaults/admin.jpg',
        ];
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
