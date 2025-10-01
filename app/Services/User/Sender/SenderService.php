<?php

namespace App\Services\User\Sender;

use App\Models\Sender;
use App\Filters\PhoneFilter;
use Illuminate\Http\Request;
use App\Traits\TranslateTrait;
use Illuminate\Pipeline\Pipeline;
use App\Filters\NameFilter;
use Illuminate\Support\Facades\Http;

class SenderService
{
    use TranslateTrait;

    public function resolveGhayaApiKey()
    {
        $ownerId = auth()->user()->created_by;
        return (string) ((string)$ownerId == '1'
            ? config('services.ghaya.key')
            : config('services.ghaya.key_two'));
    }

    public function ghayaBaseUrl(): string
    {
        return rtrim((string) config('services.ghaya.base_url'), '/');
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


    public function index()
    {
        return Sender::withAllRelations()
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate();
    }
    public function search(Request $request)
    {
        $request->validated();
        return app(Pipeline::class)
            ->send(Sender::query())
            ->through([
                NameFilter::class,
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
        $sender = Sender::create($data);
        if (!empty($shippingCompanies)) {
            foreach ($shippingCompanies as $shippingCompany) {
                if (!empty($shippingCompany['company_id']) && !empty($shippingCompany['city_id'])) {
                    \App\Models\SenderCity::create([
                        'sender_id' => $sender->id,
                        'shipping_company_id' => $shippingCompany['company_id'],
                        'city_id' => $shippingCompany['city_id'],
                    ]);
                }
            }
        }
        return $sender;
    }

    public function update($request, Sender $sender)
    {
        $data = $request->validated();
        $shippingCompanies = $data['shipping_companies'] ?? [];
        unset($data['shipping_companies']);
        
        $sender->update($data);
        
        $sender->shippingCompanies()->delete();
        
        if (!empty($shippingCompanies)) {
            foreach ($shippingCompanies as $shippingCompany) {
                if (!empty($shippingCompany['company_id']) && !empty($shippingCompany['city_id'])) {
                    \App\Models\SenderCity::create([
                        'sender_id' => $sender->id,
                        'shipping_company_id' => $shippingCompany['company_id'],
                        'city_id' => $shippingCompany['city_id'],
                    ]);
                }
            }
        }
        
        return $sender;
    }

    public function delete(Sender $sender)
    {
        $sender->delete();
        return true;
    }

    public function getSenders()
    {
        return Sender::withAllRelations()
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->get();
    }

    public function getShippingCompanies()
    {
        $response = $this->ghayaRequest()
            ->get($this->ghayaUrl('shipping-companies'));
        return $response->successful() ? $response->json() : [];
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
