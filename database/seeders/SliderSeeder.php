<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Slider;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sliders = [
            [
                'title' => 'شحن سريع وموثوق',
                'description' => 'ارسل شحناتك معنا بسرعة وثقة على مدار الساعة بخيارات متعددة',
                'subtitle' => null,
                'button_text' => 'تسجيل الدخول',
                'button_url' => '#',
                'image' => 'defaults/admin.jpg',
                'status' => 'active',
            ],
            [
                'title' => 'Navigating Business Challenges Delivering Results',
                'description' => 'Vestibulum rhoncus nisl ac gravida porta. Mauris eu sapien lacus. Etiam molestie justo neque, in convallis massa tempus in.',
                'subtitle' => 'Smart Solutions',
                'button_text' => 'Learn More',
                'button_url' => 'about.html',
                'image' => 'defaults/admin.jpg',
                'status' => 'active',
            ],
            [
                'title' => 'خدمة عملاء متميزة',
                'description' => 'نوفر لك أفضل خدمة عملاء على مدار الساعة مع فريق متخصص ومحترف',
                'subtitle' => 'خدمة 24/7',
                'button_text' => 'تواصل معنا',
                'button_url' => 'contact.html',
                'image' => 'defaults/admin.jpg',
                'status' => 'active',
            ],
        ];

        foreach ($sliders as $slider) {
            Slider::create($slider);
        }
    }
}
