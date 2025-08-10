<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\City;
use Illuminate\Support\Facades\Log;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting to fetch cities from API...');
        
        try {
            $response = Http::withHeaders([
                'accept' => '*/*',
                'x-api-key' => 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'
            ])->get('https://ghaya-express-staging-af597af07557.herokuapp.com/api/cities', [
                'page' => 0,
                'pageSize' => 100, // Increased to get more cities
                'orderColumn' => 'createdAt',
                'orderDirection' => 'desc'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $cities = $data['results'] ?? [];
                
                $this->command->info("Found " . count($cities) . " cities to seed");
                
                foreach ($cities as $cityData) {
                    $this->seedCity($cityData);
                }
                
                $this->command->info('Cities seeded successfully!');
            } else {
                $this->command->error('Failed to fetch cities from API. Status: ' . $response->status());
                Log::error('Cities API failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            $this->command->error('Error fetching cities: ' . $e->getMessage());
            Log::error('Cities seeder error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Seed a single city
     */
    private function seedCity($cityData): void
    {
        try {
            // Check if city already exists by city_id from API
            $existingCity = City::where('city_id', $cityData['id'])->first();

            if ($existingCity) {
                $this->command->line("City {$cityData['name']['en']} (ID: {$cityData['id']}) already exists, skipping...");
                return;
            }

            $city = City::create([
                'city_id' => $cityData['id'],
                'name' => [
                    'en' => $cityData['name']['en'],
                    'ar' => $cityData['name']['ar']
                ],
                'country_id' => $cityData['countryId'],
                'country_name_en' => $cityData['country']['name']['en'],
                'country_name_ar' => $cityData['country']['name']['ar'],
            ]);

            $this->command->line("Seeded city: {$cityData['name']['en']} ({$cityData['name']['ar']}) - ID: {$cityData['id']}");
            
        } catch (\Exception $e) {
            $this->command->error("Error seeding city {$cityData['name']['en']}: " . $e->getMessage());
            Log::error('City seeding error', [
                'city_data' => $cityData,
                'error' => $e->getMessage()
            ]);
        }
    }
}
