<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\User;
use App\Models\Contact;
use App\Models\Partner;
use App\Models\Service;
use App\Models\Reciever;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TermSeeder;
use Database\Seeders\AboutSeeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\SliderSeeder;
use Database\Seeders\SettingSeeder;
use Database\Seeders\AboutItemSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\WhyChooseUsSeeder;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\AdminSettingSeeder;
use Database\Seeders\AllowedCompaniesSeeder;

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
            SliderSeeder::class,
            AboutSeeder::class,
            AboutItemSeeder::class,
            AdminSettingSeeder::class,
            AllowedCompaniesSeeder::class,
            TermSeeder::class,
            WhyChooseUsSeeder::class,
        ]);
        User::factory(10)->create();
        Reciever::factory(10)->create();
        Partner::factory(10)->create();
        Service::factory(10)->create();
        Testimonial::factory(10)->create();
        Contact::factory(10)->create();
        Faq::factory(10)->create();
    }
}
