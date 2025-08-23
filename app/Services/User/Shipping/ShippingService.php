<?php

namespace App\Services\User\Shipping;

use App\Models\Reciever;
use App\Models\Shipping;
use App\Traits\ImageTrait;
use App\Filters\CodeFilter;
use App\Models\AdminSetting;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Http;
use App\Filters\ActivationStatusFilter;

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
                return ['results' => []];
            }
            
            $adminId = $user->added_by;
            
            if (!$adminId) {
                $extraWeightPricePerKg = 2;
                $codFeePerReceiver = 15;
            } else {
                $adminSettings = AdminSetting::where('admin_id', $adminId)->first();
                $extraWeightPricePerKg = $adminSettings ? $adminSettings->extra_weight_price : 2;
                $codFeePerReceiver = $adminSettings ? $adminSettings->cash_on_delivery_price : 15;
            }

            $userShippingPrices = $user->shippingPrices()
                ->select('company_id', 'company_name', 'local_price', 'international_price')
                ->get()
                ->keyBy('company_id');

            $userCompanyIds = $userShippingPrices->keys()->toArray();

            if (empty($userCompanyIds)) {
                return ['results' => []];
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

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['results']) && is_array($data['results'])) {
                    $filteredCompanies = collect($data['results'])
                        ->filter(function ($company) use ($userCompanyIds) {
                            return in_array($company['id'], $userCompanyIds) && $company['isActive'] === true;
                        })
                        ->map(function ($company) use ($userShippingPrices, $extraWeightPricePerKg, $codFeePerReceiver) {
                            $userPrice = $userShippingPrices->get($company['id']);
                            
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
                }
            }

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



    public function store($request) {}
}
