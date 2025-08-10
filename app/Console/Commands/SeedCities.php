<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\CitiesSeeder;

class SeedCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cities:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed cities from the external API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cities seeding process...');
        
        $seeder = new CitiesSeeder();
        $seeder->setCommand($this);
        $seeder->run();
        
        $this->info('Cities seeding completed!');
        
        return Command::SUCCESS;
    }
}
