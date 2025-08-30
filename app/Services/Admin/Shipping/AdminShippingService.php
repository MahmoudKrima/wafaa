<?php

namespace App\Services\Admin\Shipping;

use App\Models\Reciever;
use App\Models\Shipping;
use App\Models\User;
use App\Traits\ImageTrait;
use App\Traits\TranslateTrait;
use App\Services\User\Shipping\ShippingService;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class AdminShippingService extends ShippingService
{
    use ImageTrait, TranslateTrait;

    public function getUserShippingCompanies()
    {
        try {
            // This method should be called after a user is selected in the admin context
            // For now, return empty results as this will be handled by the controller
            return ['results' => []];
        } catch (\Exception $e) {
            return ['results' => []];
        }
    }

    public function getUserShippingCompaniesForUser(string $userId)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return ['results' => []];
            }

            $adminId = $user->created_by;

            if (!$adminId) {
                $extraWeightPricePerKg = 2;
                $codFeePerReceiver = 15;
            } else {
                $adminSettings = \App\Models\AdminSetting::where('admin_id', $adminId)->first();
                $extraWeightPricePerKg = $adminSettings ? $adminSettings->extra_weight_price : 2;
                $codFeePerReceiver = $adminSettings ? $adminSettings->cash_on_delivery_price : 15;
            }

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

            $userShippingPrices = $user->shippingPrices()
                ->select('company_id', 'company_name', 'local_price', 'international_price')
                ->get();

            $userCompanyIds = $userShippingPrices->pluck('company_id')->toArray();
            $userShippingPricesMap = $userShippingPrices->keyBy('company_id');

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

            return [
                'results' => $filteredCompanies,
                'admin_settings' => [
                    'extra_weight_price_per_kg' => $extraWeightPricePerKg,
                    'cod_fee_per_receiver' => $codFeePerReceiver
                ]
            ];
        } catch (\Exception $e) {
            return ['results' => []];
        }
    }

    public function getUserReceivers(string $userId)
    {
        return Reciever::where('user_id', $userId)
            ->withAllRelations()
            ->get();
    }

    protected function getUserWalletBalance($user): float
    {
        return optional($user->wallet)->balance;
    }

    public function getCountries()
    {
        try {
            $response = Http::withHeaders([
                'accept' => '*/*',
                'x-api-key' => 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu',
            ])->get('https://ghaya-express-staging-af597af07557.herokuapp.com/api/countries');

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function store($request)
    {
        $data = $request->validated();
        $user = User::find($data['user_id']);
        
        if (!$user) {
            return back()->with('Error', __('admin.user_not_found'));
        }

        // Call the parent store method with the user context
        return parent::store($request);
    }
}
