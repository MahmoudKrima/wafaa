<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Industry;
use App\Models\IndustryItem;
use App\Models\User;
use App\Models\Quote;
use App\Models\Insight;
use App\Models\Service;
use App\Models\InsightItem;
use App\Models\MainSection;
use App\Models\MainSectionItems;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ServiceItem;
use App\Models\Tech;
use App\Models\TechItem;
use App\Models\Who;
use App\Models\WhoItems;
use App\Models\WhoWorks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Artisan::call('optimize:clear');
        $service = Service::factory()->create();
        ServiceItem::factory()
            ->count(4)
            ->for($service)
            ->create();

        $insight = Insight::factory()->create();
        InsightItem::factory()
            ->count(3)
            ->for($insight)
            ->create();

        $main = MainSection::factory()->create();
        MainSectionItems::factory()
            ->count(3)
            ->for($main)
            ->create();
        Quote::factory(10)->create();
        $who = Who::factory()->create();
        WhoItems::factory()
            ->count(3)
            ->for($who)
            ->create();

        WhoWorks::factory()
            ->count(4)
            ->for($who)
            ->create();

        $industry = Industry::factory()->create();
        IndustryItem::factory()
            ->count(9)
            ->for($industry)
            ->create();

        $techs = Tech::factory(8)->create();
        TechItem::factory()
            ->count(20)
            ->make()
            ->each(function ($item) use ($techs) {
                $item->tech()->associate($techs->random());
                $item->save();
            });
        Contact::factory()->create();
    }
}
