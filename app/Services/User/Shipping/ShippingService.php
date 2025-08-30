<?php

namespace App\Services\User\Shipping;

use App\Models\Reciever;
use App\Models\Shipping;
use App\Traits\ImageTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\AdminSetting;
use App\Traits\TranslateTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class ShippingService
{
    use ImageTrait, TranslateTrait;

    public function index($request)
    {
        // $request->validated();
        // $banks = $this->banksHasTransactions();
        // $shippings = app(Pipeline::class)
        //     ->send(Shipping::query())
        //     ->through([
        //         ActivationStatusFilter::class,
        //         CodeFilter::class
        //     ])
        //     ->thenReturn()
        //     ->where('user_id', auth()->id())
        //     ->with('bank')
        //     ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
        //     ->orderBy('id')
        //     ->paginate()
        //     ->withQueryString();
        // return compact('banks', 'shippings');
    }

    public function store($request)
    {
        $data = $request->validated();
        $user = auth()->user();
        $company = $this->getShippingCompany($data['shipping_company_id'] ?? '');
        if (!$company) {
            return back()->with('Error', __('admin.shipping_company_not_found'));
        }
        $companyPrice = $user->shippingPrices->where('company_id', $data['shipping_company_id'])->first();
        if (!$companyPrice) {
            return back()->with('Error', __('admin.shipping_company_not_found'));
        }
        $method  = Str::lower(Arr::get($data, 'shipping_method', ''));
        $payment = Str::lower(Arr::get($data, 'payment_method', 'wallet'));

        $capErr = $this->validateCapabilities($company, $method, $payment);
        if ($capErr) {
            return back()->with('Error', $capErr);
        }

        $receivers = $this->decodeReceivers(Arr::get($data, 'selected_receivers', ''));
        if (isset($receivers['error'])) {
            return back()->with('Error', $receivers['error']);
        }
        $data['selected_receivers'] = $receivers;
        $data['receivers_count']    = count($receivers);

        $adminSettings = AdminSetting::where('admin_id', $user->created_by)->first();
        $enteredWeight = ($data['entered_weight'] ?? $data['weight'] ?? 0);

        $pricing = $this->computePricing(
            company: $company,
            payment: $payment,
            enteredWeight: $enteredWeight,
            receiversCount: (int) $data['receivers_count'],
            adminSettings: $adminSettings,
            method: $method,
            companyPrice: $companyPrice,
        );

        $balanceErr = $this->ensureWalletBalance(auth()->user(), $payment, $pricing['grand_total']);
        if ($balanceErr) {
            return back()->with('Error', $balanceErr);
        }

        if ($request->hasFile('shipment_image')) {
            $data['shipment_image_path'] = ImageTrait::uploadImage($request->file('shipment_image'), 'shipments');
        }

        $sync = $this->syncNewReceivers($data['selected_receivers'], $user);
        $data['selected_receivers'] = $sync['receivers'];

        $shipResults = $this->createShipmentsForReceivers(
            company: $company,
            user: $user,
            selectedReceivers: $data['selected_receivers'],
            pricing: $pricing,
            requestData: $data,
            shipmentImagePath: $data['shipment_image_path'] ?? null,
            payment: $payment,
            method: $method
        );

        $data['shipments'] = $shipResults;
        return $data;
    }

    private function createShipmentsForReceivers(
        array  $company,
        $user,
        array  $selectedReceivers,
        array  $pricing,
        array  $requestData,
        ?string $shipmentImagePath,
        string $payment,
        string $method
    ) {
        $baseUrl = 'https://ghaya-express-staging-af597af07557.herokuapp.com';
        $headers = [
            'accept'       => '*/*',
            'x-api-key'    => env('GHAYA_API_KEY', 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'),
            'Content-Type' => 'application/json',
        ];

        $results = [
            'success' => [],
            'failed'  => [],
        ];

        $sender = $this->resolveSenderPayload($user);
        $imageUrl = $shipmentImagePath ? displayImage($shipmentImagePath) : null;

        $isCod = $payment === 'cod';
        $shippingCompanyId = (string) Arr::get($company, 'id', Arr::get($requestData, 'shipping_company_id', ''));

        foreach ($selectedReceivers as $r) {
            $receiverId = $r['id'] ?? null;
            if (!$receiverId || !is_numeric($receiverId)) {
                $results['failed'][] = [
                    'receiver' => $r,
                    'error'    => 'Receiver id missing or invalid after sync.',
                ];
                continue;
            }

            $receiverModel = Reciever::find($receiverId);
            if (!$receiverModel) {
                $results['failed'][] = [
                    'receiver' => $r,
                    'error'    => "Receiver model not found (id {$receiverId}).",
                ];
                continue;
            }

            $body = $this->buildGhayaShipmentBody(
                shippingCompanyId: $shippingCompanyId,
                orderNumber: $requestData['order_number'] ?? $this->generateOrderNumber(),
                method: $method,
                isCod: $isCod,
                sender: $sender,
                receiver: $receiverModel,
                company: $company,
                requestData: $requestData,
                pricing: $pricing,
                imageUrl: $imageUrl
            );

            // try {
            $resp = Http::withHeaders($headers)
                ->timeout(20)
                ->retry(2, 200)
                ->post("{$baseUrl}/api/shipments", $body)
                ->throw();

            $senderUserPayload = $this->buildShippingUserBodyForSender($sender);
            $senderUserResult  = $this->postShippingUser($baseUrl, $headers, $senderUserPayload);

            $results['success'][] = [
                'receiver_id'    => $receiverModel->id,
                'status'         => $resp->status(),
                'body'           => $body,
                'response'       => $resp,
                'shipping_users' => [
                    'sender' => $senderUserResult,
                ],
            ];
            dd($results);
            // } catch (RequestException $e) {
            //     $response = $e->response;
            //     $results['failed'][] = [
            //         'receiver_id'   => $receiverModel->id,
            //         'status'        => $response?->status(),
            //         'reason'        => $response?->reason(),
            //         'body'          => $body,
            //         'error'         => $e->getMessage(),
            //         'response_text' => Str::limit($response?->body() ?? '', 4000),
            //     ];
            // } catch (\Throwable $e) {
            //     $results['failed'][] = [
            //         'receiver_id' => $receiverModel->id,
            //         'body'        => $body,
            //         'error'       => $e->getMessage(),
            //     ];
            // }
        }

        return $results;
    }

    private function buildShippingUserBodyForSender(array $sender): array
    {
        return [
            'fullName' => (string) ($sender['name'] ?? ''),
            'email'    => (string) ($sender['email'] ?? ''),
            'phone'    => (string) ($sender['phone'] ?? ''),
            'phone1'   => (string) ($sender['phone1'] ?? ''),
            'address'  => [
                'countryId' => (string) ($sender['country_id'] ?? ''),
                'stateId'   => (string) ($sender['state_id'] ?? ''),
                'cityId'    => (string) ($sender['city_id'] ?? ''),
                'street'    => (string) ($sender['street'] ?? ''),
                'zipCode'   => (string) ($sender['zip_code'] ?? ''),
            ],
            'type' => 'sender',
        ];
    }

    private function postShippingUser(string $baseUrl, array $headers, array $userBody): array
    {
        try {
            $resp = Http::withHeaders($headers)
                ->timeout(10)
                ->retry(2, 200)
                ->post("{$baseUrl}/api/shipping-users", $userBody)
                ->throw();

            return [
                'status'   => $resp->status(),
                'response' => $resp->json(),
                'body'     => $userBody,
            ];
        } catch (\Throwable $e) {
            return [
                'error' => $e->getMessage(),
                'body'  => $userBody,
            ];
        }
    }

    private function buildGhayaShipmentBody(
        string $shippingCompanyId,
        string $orderNumber,
        string $method,
        bool   $isCod,
        array  $sender,
        Reciever $receiver,
        array $company,
        array $requestData,
        array $pricing,
        ?string $imageUrl
    ): array {
        $type           = $requestData['package_type'] ?? 'boxes';
        $isCommercial   = (bool) ($requestData['is_commercial'] ?? false);
        $description    = $requestData['package_description'] ?? '';
        $notes          = $requestData['package_notes'] ?? '';
        $weight         = (float) ($requestData['entered_weight'] ?? $requestData['weight'] ?? 0);
        $length         = (float) ($requestData['length'] ?? 0);
        $width          = (float) ($requestData['width']  ?? 0);
        $height         = (float) ($requestData['height'] ?? 0);
        $packagesCount  = (int)   ($requestData['package_number'] ?? 1);
        $codPrice       = $isCod ? (float) ($pricing['cod_price_per_receiver'] ?? 0) : 0.0;
        $senderIso2     = $sender['country_code'] ?? 'SA';
        $receiverIso2   = $receiver->country_code ?? 'SA';
        $senderPhone    = $sender['phone']  ?? '';
        $senderPhone1   = $sender['phone1'] ?? '';
        $receiverPhone  = $receiver->phone ?? '';
        $receiverPhone1 = $receiver->additional_phone ?? '';

        $products = [[
            'title' => (string) ($requestData['package_description'] ?? 'Package'),
            'price' => (float) (
                (($pricing['extra_weight_per_receiver'] ?? 0) + ($pricing['company_shipping_price_per_receiver'] ?? 0))
            ),
        ]];

        $body = [
            'shippingCompanyId'   => (string) $shippingCompanyId,
            'orderNumber'         => (string) $orderNumber,
            'method'              => (string) $method,
            'type'                => (string) $type,
            'isCod'               => (bool) $isCod,
            'isCommercial'        => (bool) $isCommercial,
            'senderName'          => (string) ($sender['name'] ?? ''),
            'senderPhone'         => (string) $senderPhone,
            'senderPhone1'        => (string) $senderPhone1,
            'senderEmail'         => (string) ($sender['email'] ?? ''),
            'senderStreet'        => (string) ($sender['street'] ?? ''),
            'senderCountryId'     => (string) ($sender['country_id'] ?? ''),
            'senderStateId'       => (string) ($sender['state_id'] ?? ''),
            'senderCityId'        => (string) ($sender['city_id'] ?? ''),
            'senderCountryName'   => (string) ($sender['country_name'] ?? ''),
            'senderCountryCode'   => (string) ($senderIso2 ?? 'SA'),
            'senderStateName'     => (string) ($sender['state_name'] ?? ''),
            'senderCityName'      => (string) ($sender['city_name'] ?? ''),
            'receiverName'        => (string) ($receiver->name ?? ''),
            'receiverPhone'       => (string) $receiverPhone,
            'receiverPhone1'      => (string) $receiverPhone1,
            'receiverEmail'       => (string) ($receiver->email ?? ''),
            'receiverStreet'      => (string) ($receiver->address ?? ''),
            'receiverCountryId'   => (string) ($receiver->country_id ?? ''),
            'receiverStateId'     => (string) ($receiver->state_id ?? ''),
            'receiverCityId'      => (string) ($receiver->city_id ?? ''),
            'receiverCountryName' => (string) ($receiver->getTranslation('country_name', 'en') ?? ''),
            'receiverCountryCode' => (string) ($receiverIso2 ?? 'SA'),
            'receiverStateName'   => (string) ($receiver->getTranslation('state_name', 'en') ?? ''),
            'receiverCityName'    => (string) ($receiver->getTranslation('city_name', 'en') ?? ''),
            'description'         => (string) $description,
            'notes'               => (string) $notes,
            'weight'              => $weight,
            'length'              => $length,
            'width'               => $width,
            'height'              => $height,
            'packagesCount'       => $packagesCount,
            'codPrice'            => $codPrice,
            'products'            => $products,
        ];

        if (!empty($imageUrl)) {
            $body['imageUrl'] = $imageUrl;
        }

        return $body;
    }

    private function resolveSenderPayload($user)
    {
        $sender = [
            'id'           => (string) ($user->id ?? ''),
            'name'         => (string) ($user->getTranslation('name', 'en') ?? ''),
            'phone'        => (string) ($user->phone ?? ''),
            'phone1'       => (string) ($user->additional_phone ?? ''),
            'email'        => (string) ($user->email ?? ''),
            'street'       => (string) ($user->address ?? ''),
            'zip_code'     => (string) ($user->postal_code ?? ''),
            'country_id'   => (string) ($user->country_id ?? ''),
            'state_id'     => (string) ($user->state_id ?? ''),
            'city_id'      => (string) ($user->city_id ?? ''),
            'country_name' => (string) ($user->getTranslation('country_name', 'en') ?? ''),
            'country_code' => (string) ($user->country_code ?? ''),
            'state_name'   => (string) ($user->getTranslation('state_name', 'en') ?? ''),
            'city_name'    => (string) ($user->getTranslation('city_name', 'en') ?? ''),
        ];

        if (empty($sender['country_code']) && !empty($sender['country_id'])) {
            $c = $this->getCountryById($sender['country_id']);
            if (!empty($c['code'])) {
                $sender['country_code'] = (string) $c['code'];
            }
        }

        return $sender;
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-' . now()->format('Ymd-His') . '-' . (string) Str::uuid();
    }

    private function syncNewReceivers(array $selectedReceivers, $user): array
    {
        $createdIds   = [];
        $existingIds  = [];
        $final        = [];
        $countryCache = [];
        $stateCache   = [];
        $cityCache    = [];

        DB::transaction(function () use ($selectedReceivers, $user, &$createdIds, &$existingIds, &$final, &$countryCache, &$stateCache, &$cityCache) {
            foreach ($selectedReceivers as $r) {
                $r = is_object($r) ? get_object_vars($r) : (array) $r;

                if (empty($r['isNew']) || $r['isNew'] !== true) {
                    if (!empty($r['id']) && is_numeric($r['id'])) {
                        $existingIds[] = (int) $r['id'];
                        $r['isNew'] = false;
                        $final[] = $r;
                        continue;
                    }

                    $phone = isset($r['phone']) ? trim((string) $r['phone']) : '';
                    $email = isset($r['email']) ? trim((string) $r['email']) : '';

                    $existing = Reciever::where('user_id', $user->id)
                        ->where(function ($q) use ($phone, $email) {
                            if ($phone !== '') $q->orWhere('phone', $phone);
                            if ($email !== '') $q->orWhere('email', $email);
                        })
                        ->first();

                    if ($existing) {
                        $existingIds[] = $existing->id;
                        $r['id'] = $existing->id;
                        $r['isNew'] = false;
                        $final[] = $r;
                        continue;
                    }

                    $r['isNew'] = true;
                }

                $phone = isset($r['phone']) ? trim((string) $r['phone']) : '';
                $email = isset($r['email']) ? trim((string) $r['email']) : '';

                $existing = Reciever::where('user_id', $user->id)
                    ->where(function ($q) use ($phone, $email) {
                        if ($phone !== '') $q->orWhere('phone', $phone);
                        if ($email !== '') $q->orWhere('email', $email);
                    })
                    ->first();

                if ($existing) {
                    $existingIds[] = $existing->id;
                    $r['id'] = $existing->id;
                    $r['isNew'] = false;
                    $final[] = $r;
                    continue;
                }

                $countryId = $r['country_id'] ?? null;
                $stateId   = $r['state_id']   ?? null;
                $cityId    = $r['city_id']    ?? null;

                if ($countryId && !isset($countryCache[$countryId])) {
                    $countryCache[$countryId] = $this->getCountryById($countryId);
                }
                if ($stateId && !isset($stateCache[$stateId])) {
                    $stateCache[$stateId] = $this->getStateById($stateId);
                }
                if ($cityId && !isset($cityCache[$cityId])) {
                    $cityCache[$cityId] = $this->getCityById($cityId);
                }

                $countryName = '';
                if (!empty($countryCache[$countryId]['name'])) {
                    $countryName = $this->translate(
                        $countryCache[$countryId]['name']['ar'] ?? '',
                        $countryCache[$countryId]['name']['en'] ?? ''
                    );
                }

                $stateName = '';
                if (!empty($stateCache[$stateId]['name'])) {
                    $stateName = $this->translate(
                        $stateCache[$stateId]['name']['ar'] ?? '',
                        $stateCache[$stateId]['name']['en'] ?? ''
                    );
                }

                $cityName = '';
                if (!empty($cityCache[$cityId]['name'])) {
                    $cityName = $this->translate(
                        $cityCache[$cityId]['name']['ar'] ?? '',
                        $cityCache[$cityId]['name']['en'] ?? ''
                    );
                }

                $new = Reciever::create([
                    'user_id'           => $user->id,
                    'name'              => $r['name'] ?? null,
                    'phone'             => $phone ?: null,
                    'additional_phone'  => $r['additional_phone'] ?? null,
                    'email'             => $email ?: null,
                    'address'           => $r['address'] ?? null,
                    'postal_code'       => $r['postal_code'] ?? null,
                    'country_id'        => $countryId,
                    'state_id'          => $stateId,
                    'city_id'           => $cityId,
                    'country_name'      => $countryName,
                    'state_name'        => $stateName,
                    'city_name'         => $cityName,
                ]);

                $createdIds[] = $new->id;
                $r['id']    = $new->id;
                $r['isNew'] = false;

                $final[] = $r;
            }
        });

        return [
            'created_ids'  => $createdIds,
            'existing_ids' => $existingIds,
            'receivers'    => $final,
        ];
    }

    protected function getShippingCompany(string $companyId)
    {
        if (!$companyId) return null;

        try {
            $res = Http::withHeaders([
                'accept'    => '*/*',
                'x-api-key' => env('GHAYA_API_KEY', 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'),
            ])->get("https://ghaya-express-staging-af597af07557.herokuapp.com/api/shipping-companies/{$companyId}");

            if (!$res->successful()) return null;

            $json = $res->json();
            if (is_array($json) && Arr::isAssoc($json)) return $json;
            if (is_array($json) && isset($json['data']) && is_array($json['data'])) return $json['data'];

            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function validateCapabilities(array $company, string $method, string $payment): ?array
    {
        $methods = collect(Arr::get($company, 'shippingMethods', []))
            ->filter()
            ->map(fn($m) => Str::lower($m))
            ->values()
            ->all();

        if (!in_array($method, ['local', 'international'], true)) {
            return ['shipping_method' => __('admin.invalid_shipping_method')];
        }
        if (!in_array($method, $methods, true)) {
            return ['shipping_method' => __('admin.company_does_not_support_method')];
        }
        if ($payment === 'cod' && !in_array('cashondelivery', $methods, true)) {
            return ['payment_method' => __('admin.company_does_not_support_cod')];
        }
        return null;
    }

    protected function decodeReceivers(string $json): array
    {
        $arr = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => __('validation.custom.selected_receivers.invalid_json')];
        }
        if (!is_array($arr) || count($arr) === 0) {
            return ['error' => __('validation.custom.selected_receivers.empty')];
        }
        return $arr;
    }

    protected function computePricing(
        array  $company,
        string $payment,
        float  $enteredWeight,
        int    $receiversCount,
        object $adminSettings,
        string $method,
        object $companyPrice,
    ) {
        $maxWeight   = (float) Arr::get($company, 'maxWeight', 0);
        $extraKg     = max(0, $enteredWeight - $maxWeight);
        $extraKgRate = (float) $adminSettings->extra_weight_price;
        $extraPer    = round($extraKg * $extraKgRate, 2);
        $codPer      = ($payment === 'cod') ? (float) $adminSettings->cash_on_delivery_price : 0.0;

        $method = strtolower($method);
        $companyPer = 0.0;
        if ($method === 'local') {
            $companyPer = (float) ($companyPrice->local_price ?? 0);
        } elseif ($method === 'international') {
            $companyPer = (float) ($companyPrice->international_price ?? 0);
        }

        $perReceiverTotal = round($companyPer + $extraPer + $codPer, 2);
        $grandTotal       = round($perReceiverTotal * max(1, (int) $receiversCount), 2);

        return [
            'max_weight'                              => $maxWeight,
            'entered_weight'                          => $enteredWeight,
            'extra_kg'                                => $extraKg,
            'extra_weight_per_receiver'               => $extraPer,
            'cod_price_per_receiver'                  => $codPer,
            'company_extra_weight_price_per_receiver' => $extraKgRate,
            'company_shipping_price_per_receiver'     => $companyPer,
            'per_receiver_total'                      => $perReceiverTotal,
            'grand_total'                             => $grandTotal,
        ];
    }

    protected function ensureWalletBalance($user, string $payment, float $grandTotal): ?array
    {
        if ($payment !== 'wallet') {
            return null;
        }
        $balance = $this->getUserWalletBalance($user);
        if ($balance < $grandTotal) {
            return ['payment_method' => __('admin.insufficient_balance')];
        }
        return null;
    }

    protected function getUserWalletBalance($user): float
    {
        return optional($user->wallet)->balance;
    }

    public function receivers()
    {
        return Reciever::where('user_id', auth()->user()->id)
            ->withAllRelations()
            ->get();
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

            $response = Http::withHeaders([
                'accept'    => '*/*',
                'x-api-key' => env('GHAYA_API_KEY', 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'),
            ])->get('https://ghaya-express-staging-af597af07557.herokuapp.com/api/shipping-companies', [
                'page'           => 0,
                'pageSize'       => 50,
                'orderColumn'    => 'createdAt',
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
                    ->filter(fn($company) => $company['isActive'] === true)
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
                        'cod_fee_per_receiver'      => $codFeePerReceiver
                    ]
                ];
            }

            $filteredCompanies = collect($data['results'])
                ->filter(fn($company) => in_array($company['id'], $userCompanyIds) && $company['isActive'] === true)
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
                    'cod_fee_per_receiver'      => $codFeePerReceiver
                ]
            ];
        } catch (\Exception $e) {
            return ['results' => []];
        }
    }

    public function getStates()
    {
        try {
            $response = Http::withHeaders([
                'accept'    => '*/*',
                'x-api-key' => env('GHAYA_API_KEY', 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'),
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
                'accept'    => '*/*',
                'x-api-key' => env('GHAYA_API_KEY', 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'),
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
                'accept'    => '*/*',
                'x-api-key' => env('GHAYA_API_KEY', 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'),
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

    public function getCountryById($countryId)
    {
        try {
            $response = Http::withHeaders([
                'accept'    => '*/*',
                'x-api-key' => env('GHAYA_API_KEY', 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'),
            ])->get("https://ghaya-express-staging-af597af07557.herokuapp.com/api/countries/{$countryId}");

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getStateById($stateId)
    {
        try {
            $response = Http::withHeaders([
                'accept'    => '*/*',
                'x-api-key' => env('GHAYA_API_KEY', 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'),
            ])->get("https://ghaya-express-staging-af597af07557.herokuapp.com/api/states/{$stateId}");

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getCityById($cityId)
    {
        try {
            $response = Http::withHeaders([
                'accept'    => '*/*',
                'x-api-key' => env('GHAYA_API_KEY', 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'),
            ])->get("https://ghaya-express-staging-af597af07557.herokuapp.com/api/cities/{$cityId}");

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }
}
