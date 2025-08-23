<?php

namespace App\Services\Admin\UserSettings;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CityService
{
    private $apiKey = 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu';
    private $baseUrl = 'https://ghaya-express-staging-af597af07557.herokuapp.com/api';

    public function getCitiesByState($stateId)
    {
        $cacheKey = "cities_state_{$stateId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($stateId) {
            try {
                $response = Http::withHeaders([
                    'accept' => '*/*',
                    'x-api-key' => $this->apiKey
                ])->get($this->baseUrl . '/cities', [
                    'page' => 0,
                    'pageSize' => 100,
                    'orderColumn' => 'createdAt',
                    'orderDirection' => 'desc',
                    'stateId' => $stateId
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['results'] ?? [];
                }
            } catch (\Exception $e) {
                return [];
            }

            return [];
        });
    }
}
