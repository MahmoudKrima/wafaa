<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\About;

class AboutSeeder extends Seeder
{
    public function run(): void
    {
        About::create([
            'title' => 'من نحن',
            'subtitle' => 'شركة شحن رائدة في مجال النقل والتوصيل',
            'image' => 'defaults/admin.jpg',
        ]);
    }
}
