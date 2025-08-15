<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AboutItem;

class AboutItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'about_id' => 1,
                'title' => 'خدمة متميزة',
                'description' => 'نوفر لك أفضل خدمة شحن مع ضمان الجودة والسرعة في التوصيل',
            ],
            [
                'about_id' => 1,
                'title' => 'أسعار تنافسية',
                'description' => 'أسعار مناسبة للجميع مع خيارات متعددة تناسب احتياجاتك',
            ],
            [
                'about_id' => 1,
                'title' => 'توصيل سريع',
                'description' => 'خدمة توصيل سريعة وآمنة لجميع أنحاء المملكة',
            ],
            [
                'about_id' => 1,
                'title' => 'دعم فني 24/7',
                'description' => 'فريق دعم فني متاح على مدار الساعة لمساعدتك',
            ],
        ];

        foreach ($items as $item) {
            AboutItem::create($item);
        }
    }
}
