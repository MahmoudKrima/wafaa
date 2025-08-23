<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Partner;
use App\Models\Service;
use App\Models\Reciever;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\AboutSeeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\CitiesSeeder;
use Database\Seeders\SliderSeeder;
use Database\Seeders\SettingSeeder;
use Database\Seeders\AboutItemSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\AdminSettingSeeder;
use App\Models\Testimonial;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Artisan::call('optimize:clear');
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            AdminSeeder::class,
            SettingSeeder::class,
            CitiesSeeder::class,
            SliderSeeder::class,
            AboutSeeder::class,
            AboutItemSeeder::class,
            AdminSettingSeeder::class,
        ]);
        User::factory(10)->create();
        Reciever::factory(10)->create();
        Partner::factory(10)->create();
        Service::factory(10)->create();
        Testimonial::factory(10)->create();
    }
}
