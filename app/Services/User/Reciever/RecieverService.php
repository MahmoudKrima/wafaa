<?php

namespace App\Services\User\Reciever;

use App\Models\Reciever;
use App\Filters\EmailFilter;
use App\Filters\PhoneFilter;
use Illuminate\Http\Request;
use App\Traits\TranslateTrait;
use Illuminate\Pipeline\Pipeline;
use App\Filters\NameFilter;
use App\Models\RecieverCity;
use Illuminate\Support\Facades\Http;

class RecieverService
{
    use TranslateTrait;

    public function index()
    {
        return Reciever::withAllRelations()
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate();
    }
    public function search(Request $request)
    {
        $request->validated();
        return app(Pipeline::class)
            ->send(Reciever::query())
            ->through([
                NameFilter::class,
                EmailFilter::class,
                PhoneFilter::class,
            ])
            ->thenReturn()
            ->withAllRelations()
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate()
            ->withQueryString();
    }

    public function store($request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $shippingCompanies = $data['shipping_companies'] ?? [];
        unset($data['shipping_companies']);
        $reciever = Reciever::create($data);
        if (!empty($shippingCompanies)) {
            foreach ($shippingCompanies as $shippingCompany) {
                if (!empty($shippingCompany['company_id']) && !empty($shippingCompany['city_id'])) {
                    RecieverCity::create([
                        'reciever_id' => $reciever->id,
                        'shipping_company_id' => $shippingCompany['company_id'],
                        'city_id' => $shippingCompany['city_id'],
                    ]);
                }
            }
        }

        return $reciever;
    }

    public function update($request, Reciever $reciever)
    {
        $data = $request->validated();
        $shippingCompanies = $data['shipping_companies'] ?? [];
        unset($data['shipping_companies']);
        
        $reciever->update($data);
        
        $reciever->shippingCompanies()->delete();
        
        if (!empty($shippingCompanies)) {
            foreach ($shippingCompanies as $shippingCompany) {
                if (!empty($shippingCompany['company_id']) && !empty($shippingCompany['city_id'])) {
                    RecieverCity::create([
                        'reciever_id' => $reciever->id,
                        'shipping_company_id' => $shippingCompany['company_id'],
                        'city_id' => $shippingCompany['city_id'],
                    ]);
                }
            }
        }
        
        return $reciever;
    }

    public function delete(Reciever $reciever)
    {
        $reciever->delete();
        return true;
    }

    public function getShippingCompanies()
    {
        $response = $this->ghayaRequest()
            ->get($this->ghayaUrl('shipping-companies'));
        return $response->successful() ? $response->json() : [];
    }

    private function resolveGhayaApiKey(): string
    {
        $user = auth()->user();
        if (!$user) {
            return config('services.ghaya.key');
        }
        
        $ownerId = $user->created_by;
        return (string) ((string)$ownerId === '1'
            ? config('services.ghaya.key')
            : config('services.ghaya.key_two'));
    }

    private function ghayaRequest()
    {
        return Http::withHeaders([
            'accept'    => '*/*',
            'x-api-key' => $this->resolveGhayaApiKey(),
        ]);
    }

    private function ghayaUrl(string $endpoint): string
    {
        return rtrim(config('services.ghaya.base_url'), '/') . '/' . ltrim($endpoint, '/');
    }

    public function getCitiesByCompanyAndCountry(
        string $shippingCompanyId,
        string $countryId,
        int $page = 0,
        int $pageSize = 10,
        string $orderColumn = 'createdAt',
        string $orderDirection = 'desc'
    ): array {
        try {
            $query = [
                'page'              => $page,
                'pageSize'          => $pageSize,
                'orderColumn'       => $orderColumn,
                'orderDirection'    => $orderDirection,
                'countryId'         => $countryId,
                'shippingCompanyId' => $shippingCompanyId,
            ];

            $response = $this->ghayaRequest()
                ->get($this->ghayaUrl('cities'), $query);

            if ($response->successful()) {
                return $response->json();
            }
            return [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
