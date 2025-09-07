<?php

namespace Database\Seeders;

use App\Models\WhyChooseUs;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WhyChooseUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WhyChooseUs::create([
            'title' => 'ليه تختار منصة دبليو إم اكسبريس ؟',
            'description' => 'نجمع بين الأسعار التنافسية والشراكات القوية، مع شبكة شحن واسعة النطاق تغطي كافة البلدان، وخدمة عملاء استثنائية لتتبع شحنتك بدقة وتقدم خيارات دفع متعددة وآمنة، كل هذا متاح على مدار الساعة لتلبية احتياجاتك.',
            'image' => 'front/assets/img/about/about-1.png',
        ]);
    }
}
