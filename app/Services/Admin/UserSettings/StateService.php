<?php

namespace App\Services\Admin\UserSettings;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class StateService
{
    private $apiKey = 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu';
    private $baseUrl = 'https://ghaya-express-staging-af597af07557.herokuapp.com/api';

    public function getStates()
    {
        return Cache::remember('states', 3600, function () {
            try {
                $response = Http::withHeaders([
                    'accept' => '*/*',
                    'x-api-key' => $this->apiKey
                ])->get($this->baseUrl . '/states', [
                    'page' => 0,
                    'pageSize' => 100,
                    'orderColumn' => 'createdAt',
                    'orderDirection' => 'desc'
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
