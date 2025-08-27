<?php

namespace App\Services\User\Shipping;

use App\Models\Reciever;
use App\Models\Shipping;
use App\Traits\ImageTrait;
use App\Filters\CodeFilter;
use App\Models\AdminSetting;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Filters\ActivationStatusFilter;
use Illuminate\Validation\ValidationException;

class ShippingService
{
    use ImageTrait;

    public function receivers()
    {
        $recievers = Reciever::where('user_id', auth()->user()->id)
            ->withAllRelations()
            ->get();
        return $recievers;
    }

    public function getUserShippingCompanies()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                \Log::info('No authenticated user found');
                return ['results' => []];
            }

            \Log::info('Getting shipping companies for user: ' . $user->id);

            $adminId = $user->added_by;

            if (!$adminId) {
                $extraWeightPricePerKg = 2;
                $codFeePerReceiver = 15;
            } else {
                $adminSettings = AdminSetting::where('admin_id', $adminId)->first();
                $extraWeightPricePerKg = $adminSettings ? $adminSettings->extra_weight_price : 2;
                $codFeePerReceiver = $adminSettings ? $adminSettings->cash_on_delivery_price : 15;
            }

            // Get all available shipping companies first
            $response = Http::withHeaders([
                'accept' => '*/*',
                'x-api-key' => 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu',
            ])->get('https://ghaya-express-staging-af597af07557.herokuapp.com/api/shipping-companies', [
                'page' => 0,
                'pageSize' => 50,
                'orderColumn' => 'createdAt',
                'orderDirection' => 'desc'
            ]);

            if (!$response->successful()) {
                return ['results' => []];
            }

            $data = $response->json();
            if (!isset($data['results']) || !is_array($data['results'])) {
                return ['results' => []];
            }

            // Get user's shipping prices
            $userShippingPrices = $user->shippingPrices()
                ->select('company_id', 'company_name', 'local_price', 'international_price')
                ->get();

            $userCompanyIds = $userShippingPrices->pluck('company_id')->toArray();
            $userShippingPricesMap = $userShippingPrices->keyBy('company_id');

            \Log::info('User shipping prices found', [
                'user_id' => $user->id,
                'shipping_prices_count' => $userShippingPrices->count(),
                'company_ids' => $userCompanyIds
            ]);

            // If user has no specific pricing, show all active companies with default pricing
            if (empty($userCompanyIds)) {
                $filteredCompanies = collect($data['results'])
                    ->filter(function ($company) {
                        return $company['isActive'] === true;
                    })
                    ->map(function ($company) use ($extraWeightPricePerKg, $codFeePerReceiver) {
                        $company['userLocalPrice'] = $company['localPrice'] ?? null;
                        $company['userInternationalPrice'] = $company['internationalPrice'] ?? null;
                        $company['adminExtraWeightPrice'] = $extraWeightPricePerKg;
                        $company['adminCodFee'] = $codFeePerReceiver;

                        if ($company['shippingMethods'] && in_array('local', $company['shippingMethods'])) {
                            $company['effectiveLocalPrice'] = $company['localPrice'];
                        }

                        if ($company['shippingMethods'] && in_array('international', $company['shippingMethods'])) {
                            $company['effectiveInternationalPrice'] = $company['internationalPrice'];
                        }

                        $company['hasCod'] = in_array('cashOnDelivery', $company['shippingMethods']);

                        return $company;
                    })
                    ->values()
                    ->toArray();

                return [
                    'results' => $filteredCompanies,
                    'admin_settings' => [
                        'extra_weight_price_per_kg' => $extraWeightPricePerKg,
                        'cod_fee_per_receiver' => $codFeePerReceiver
                    ]
                ];
            }

            // User has specific pricing, filter companies to only show those with user pricing
            $filteredCompanies = collect($data['results'])
                ->filter(function ($company) use ($userCompanyIds) {
                    return in_array($company['id'], $userCompanyIds) && $company['isActive'] === true;
                })
                ->map(function ($company) use ($userShippingPricesMap, $extraWeightPricePerKg, $codFeePerReceiver) {
                    $userPrice = $userShippingPricesMap->get($company['id']);

                    $company['userLocalPrice'] = $userPrice->local_price ?? null;
                    $company['userInternationalPrice'] = $userPrice->international_price ?? null;
                    $company['adminExtraWeightPrice'] = $extraWeightPricePerKg;
                    $company['adminCodFee'] = $codFeePerReceiver;

                    if ($company['shippingMethods'] && in_array('local', $company['shippingMethods'])) {
                        $company['effectiveLocalPrice'] = $userPrice->local_price ?? $company['localPrice'];
                    }

                    if ($company['shippingMethods'] && in_array('international', $company['shippingMethods'])) {
                        $company['effectiveInternationalPrice'] = $userPrice->international_price ?? null;
                    }

                    $company['hasCod'] = in_array('cashOnDelivery', $company['shippingMethods']);

                    return $company;
                })
                ->values()
                ->toArray();

            \Log::info('Returning filtered companies for user with pricing', [
                'user_id' => $user->id,
                'companies_count' => count($filteredCompanies)
            ]);

            return [
                'results' => $filteredCompanies,
                'admin_settings' => [
                    'extra_weight_price_per_kg' => $extraWeightPricePerKg,
                    'cod_fee_per_receiver' => $codFeePerReceiver
                ]
            ];

            return ['results' => []];
        } catch (\Exception $e) {
            return ['results' => []];
        }
    }

    public function index($request)
    {
        $request->validated();
        $banks = $this->banksHasTransactions();
        $shippings = app(Pipeline::class)
            ->send(Shipping::query())
            ->through([
                ActivationStatusFilter::class,
                CodeFilter::class
            ])
            ->thenReturn()
            ->where('user_id', auth()->id())
            ->with('bank')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('id')
            ->paginate()
            ->withQueryString();
        return compact('banks', 'shippings');
    }



    public function store($request)
    {
        try {
            $data = $request->validated();
        } catch (ValidationException $e) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('force_step', 7)
                // send the company + pricing + method back so we can rehydrate JS
                ->with('selectedCompany', $selectedCompanyArray)   // name, logoUrl, serviceName, maxWeight, currency_symbol, etc.
                ->with('companyPricing', $companyPricingArray)     // if you show any per-company pricing
                ->with('selectedMethod', $request->input('shipping_method')); // "local" 
        }
        dd($data);

        $data['selected_receivers'] = json_decode($data['selected_receivers'], true);

        if ($request->hasFile('shipment_image')) {
            $data['shipment_image_path'] = $request->file('shipment_image')->store('shipments', 'public');
        }
    }

    public function getStates()
    {
        try {
            $response = Http::withHeaders([
                'accept' => '*/*',
                'x-api-key' => 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu',
            ])->get('https://ghaya-express-staging-af597af07557.herokuapp.com/api/states');

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getCities()
    {
        try {
            $response = Http::withHeaders([
                'accept' => '*/*',
                'x-api-key' => 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu',
            ])->get('https://ghaya-express-staging-af597af07557.herokuapp.com/api/cities');

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getCitiesByState($stateId)
    {
        try {
            $response = Http::withHeaders([
                'accept' => '*/*',
                'x-api-key' => 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu',
            ])->get('https://ghaya-express-staging-af597af07557.herokuapp.com/api/cities', [
                'state_id' => $stateId
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }
}
