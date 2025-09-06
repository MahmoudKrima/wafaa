<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Term;

class TermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $term = [
            'term_description' => [
                'ar' => 'الشروط والأحكام بالعربية',
                'en' => 'Term & Conditions En',
            ],
            'policy_description' => [
                'ar' => 'سياسة الخصوصية بالعربية',
                'en' => 'Privacy Policy En',
            ],
        ];
        Term::create($term);
    }
}
