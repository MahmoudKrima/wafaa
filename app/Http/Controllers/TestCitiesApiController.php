<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestCitiesApiController extends Controller
{
    /**
     * Test the cities API connection
     */
    public function test()
    {
        try {
            $response = Http::withHeaders([
                'accept' => '*/*',
                'x-api-key' => 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'
            ])->get('https://ghaya-express-staging-af597af07557.herokuapp.com/api/cities', [
                'page' => 0,
                'pageSize' => 5,
                'orderColumn' => 'createdAt',
                'orderDirection' => 'desc'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $cities = $data['results'] ?? [];
                
                $simplifiedCities = [];
                foreach (array_slice($cities, 0, 3) as $city) {
                    $simplifiedCities[] = [
                        'city_id' => $city['id'],
                        'name' => $city['name'],
                        'country_id' => $city['countryId'],
                        'country_name' => $city['country']['name']
                    ];
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'API connection successful',
                    'total_cities' => $data['total'] ?? 0,
                    'sample_cities' => $simplifiedCities,
                    'status_code' => $response->status()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'API connection failed',
                    'status_code' => $response->status(),
                    'response' => $response->body()
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Cities API test failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'API connection error: ' . $e->getMessage()
            ], 500);
        }
    }
}
