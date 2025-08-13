<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\CitiesSeeder;
use Database\Seeders\SliderSeeder;
use Database\Seeders\SettingSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\Artisan;

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
        ]);
        User::factory(10)->create();
    }
}
