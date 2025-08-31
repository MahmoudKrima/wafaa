<?php

namespace App\Services\User\Shipping;

use App\Models\Reciever;
use App\Traits\ImageTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\AdminSetting;
use App\Traits\TranslateTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use App\Models\AllowedCompany;

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
        // dd($data);
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

        $balanceErr = $this->ensureWalletBalance(auth()->user(),  $pricing['grand_total']);
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
        array  $pricing,          // unused in minimal payload
        array  $requestData,
        ?string $shipmentImagePath, // unused in minimal payload
        string $payment,
        string $method
    ) {
        $baseUrl = 'https://ghaya-express-staging-af597af07557.herokuapp.com';
        $headers = [
            'accept'       => '*/*',
            'x-api-key'    => env('GHAYA_API_KEY', 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'),
            'Content-Type' => 'application/json',
        ];

        $results = ['success' => [], 'failed' => []];

        $senderPayload     = $this->resolveSenderPayload($user); // we’ll still prefer $requestData for sender fields
        $isCod             = ($payment === 'cod');
        $shippingCompanyId = (string) \Illuminate\Support\Arr::get($company, 'id', \Illuminate\Support\Arr::get($requestData, 'shipping_company_id', ''));
        $userId            = (string) (auth()->id() ?? '');

        foreach ($selectedReceivers as $r) {
            $receiverId = $r['id'] ?? null;
            if (!$receiverId) {
                $results['failed'][] = ['receiver' => $r, 'error' => 'Receiver id is missing.'];
                continue;
            }

            // Optional fallback to model for missing pieces ONLY
            $receiverModel = \App\Models\Reciever::find($receiverId);

            // Build minimal receiver structure (names exactly as Ghaya expects)
            $receiver = [
                'id'            => (string) $receiverId,
                'name'          => (string)($r['name']             ?? $receiverModel?->name ?? ''),
                'email'         => (string)($r['email']            ?? $receiverModel?->email ?? ''),
                'phone'         => (string)($r['phone']            ?? $receiverModel?->phone ?? ''),
                'phone1'        => (string)($r['additional_phone'] ?? $receiverModel?->additional_phone ?? ''),
                'country_id'    => (string)($r['country_id']       ?? $receiverModel?->country_id ?? ''),
                'country_name'  => (string)($r['country_name']     ?? ($receiverModel?->getTranslation('country_name', 'en') ?? '')),
                'country_code'  => (string)($r['country_code']     ?? $receiverModel?->country_code ?? 'SA'),
                'city_id'       => (string)($r['city_id']          ?? $receiverModel?->city_id ?? ''),
                'city_name'     => (string)($r['city_name']        ?? ($receiverModel?->getTranslation('city_name', 'en') ?? '')),
                'street'        => (string)($r['address']          ?? $receiverModel?->address ?? ''),
                'zip'           => (string)($r['postal_code']      ?? $receiverModel?->postal_code ?? ''),
            ];

            $body = $this->buildGhayaShipmentBody(
                shippingCompanyId: $shippingCompanyId,
                method: $method,
                isCod: $isCod,
                sender: $senderPayload,
                receiver: $receiver,
                requestData: $requestData,
                userId: $userId
            );

            try {
                $resp = \Illuminate\Support\Facades\Http::withHeaders($headers)
                    ->timeout(20)
                    ->retry(2, 200)
                    ->post("{$baseUrl}/api/shipments", $body);

                if ($resp->failed()) {
                    $results['failed'][] = [
                        'receiver_id'   => $receiverId,
                        'status'        => $resp->status(),
                        'url'           => "{$baseUrl}/api/shipments",
                        'body_sent'     => $body,
                        'response_json' => $resp->json(),
                        'response_text' => $resp->body(),
                    ];
                } else {
                    $results['success'][] = [
                        'receiver_id'   => $receiverId,
                        'status'        => $resp->status(),
                        'body_sent'     => $body,
                        'response_json' => $resp->json(),
                    ];
                }
            } catch (\Throwable $e) {
                $results['failed'][] = [
                    'receiver_id'   => $receiverId,
                    'url'           => "{$baseUrl}/api/shipments",
                    'body_sent'     => $body,
                    'error_message' => $e->getMessage(),
                ];
            }
        }

        // Now you can dd or return (your choice)
        dd($results);
        // return $results;
    }



    private function buildGhayaShipmentBody(
        string $shippingCompanyId,
        string $method,
        bool   $isCod,
        array  $sender,        // from resolveSenderPayload($user) — fallback only
        array  $receiver,      // merged/minimal from caller
        array  $requestData,
        string $userId
    ): array {

        // --- helper to normalize phones into +CCC-... and strip junk ---
        $normalizePhone = function (?string $raw, string $defaultCC = 'SA') {
            $p = trim((string)$raw);

            // remove spaces, parentheses, and dots
            $p = preg_replace('/[()\s.]/', '', $p) ?? '';

            // if starts 00CC -> +CC
            if (preg_match('/^00(\d+)/', $p, $m)) {
                $p = '+' . $m[1];
            }

            // KSA convenience: 05xxxxxxxx -> +966-5xxxxxxxx
            if (str_starts_with($p, '05')) {
                $p = '+966-' . substr($p, 1); // 05 -> +966-5
            }

            // ensure we have at least +… or a simple +CCC-… layout
            if (preg_match('/^\+\d+$/', $p)) {
                // ok like +9665xxxxxxxx, format with a dash after country code if you want:
                // try to split country code vs subscriber for +9665... form:
                if (str_starts_with($p, '+9665')) {
                    return '+966-' . substr($p, 4);
                }
                return $p;
            }

            if (preg_match('/^\+\d+-?\d+$/', $p)) {
                return $p; // already like +966-5xxxxxxx
            }

            // fallback: if it’s all digits and length looks like local mobile, assume SA
            if (preg_match('/^\d{9,12}$/', $p) && $defaultCC === 'SA') {
                if (str_starts_with($p, '5')) {
                    return '+966-' . $p;
                }
                if (str_starts_with($p, '05')) {
                    return '+966-' . substr($p, 1);
                }
            }

            // last resort: return as-is
            return $p;
        };

        // ----- SENDER (prefer requestData, fallback to $sender) -----
        $senderName        = (string)($requestData['sender_name']         ?? $sender['name']         ?? '');
        $senderEmail       = (string)($requestData['sender_email']        ?? $sender['email']        ?? '');
        $senderPhone       = $normalizePhone($requestData['sender_phone'] ?? ($sender['phone'] ?? ''));
        $senderPhone1Raw   = $requestData['sender_additional_phone'] ?? ($sender['phone1'] ?? '');
        $senderPhone1      = $senderPhone1Raw !== '' ? $normalizePhone($senderPhone1Raw) : '';

        $senderCountryId   = (string)($requestData['sender_country_id']   ?? $sender['country_id']   ?? '');
        $senderCountryName = (string)($requestData['sender_country_name'] ?? $sender['country_name'] ?? '');
        $senderCountryCode = (string)($sender['country_code'] ?? 'SA');

        $senderCityId      = (string)($requestData['sender_city_id']      ?? $sender['city_id']      ?? '');
        $senderCityName    = (string)($requestData['sender_city_name']    ?? $sender['city_name']    ?? '');
        $senderStreet      = (string)($requestData['sender_address']      ?? $sender['street']       ?? '');
        $senderZipCode     = (string)($requestData['sender_postal_code']  ?? $sender['zip_code']     ?? '');

        // ----- PACKAGE from request -----
        $type          = (string)($requestData['package_type'] ?? 'box');
        $length        = (int) ($requestData['length']       ?? 0);
        $width         = (int) ($requestData['width']        ?? 0);
        $height        = (int) ($requestData['height']       ?? 0);
        $weight        = (int) ($requestData['entered_weight'] ?? $requestData['weight'] ?? 0);
        $packagesCount = (int)   ($requestData['package_number'] ?? 1);
        $description   = (string)($requestData['package_description'] ?? '');
        $isCommercial  = (bool)  ($requestData['is_commercial'] ?? false);
        $isWeightEdited = (bool) ($requestData['is_weight_edited'] ?? array_key_exists('entered_weight', $requestData));

        // ----- RECEIVER (Ghaya minimal) -----
        $receiverName        = (string)($receiver['name'] ?? '');
        $receiverEmail       = (string)($receiver['email'] ?? '');
        $receiverPhone       = $normalizePhone($receiver['phone'] ?? '');
        $receiverPhone1Raw   = (string)($receiver['phone1'] ?? '');
        $receiverPhone1      = $receiverPhone1Raw !== '' ? $normalizePhone($receiverPhone1Raw) : '';

        $receiverCountryId   = (string)($receiver['country_id'] ?? '');
        $receiverCountryName = (string)($receiver['country_name'] ?? '');
        $receiverCountryCode = (string)($receiver['country_code'] ?? 'SA');

        $receiverCityId      = (string)($receiver['city_id'] ?? '');
        $receiverCityName    = (string)($receiver['city_name'] ?? '');
        $receiverStreet      = (string)($receiver['street'] ?? '');
        $receiverZipCode     = (string)($receiver['zip'] ?? '');

        // ----- Build EXACTLY like your working body -----
        $body = [
            "shippingCompanyId" => (string) $shippingCompanyId,
            "method"            => (string) $method,

            "senderName"        => $senderName,
            "senderEmail"       => $senderEmail,
            "senderPhone"       => $senderPhone,
            "senderPhone1"      => $senderPhone1,
            "senderCountryId"   => $senderCountryId,
            "senderCountryName" => $senderCountryName,
            "senderCountryCode" => $senderCountryCode,
            "senderCityId"      => $senderCityId,
            "senderStreet"      => $senderStreet,
            "senderZipCode"     => $senderZipCode,

            "type"              => $type,
            "length"            => $length,
            "width"             => $width,
            "height"            => $height,
            "weight"            => $weight,
            "packagesCount"     => $packagesCount,
            "description"       => $description,
            "isCommercial"      => $isCommercial,
            "isCod"             => (bool) false,
            "isWeightEdited"    => (bool)$isWeightEdited,
            "senderCityName"    => $senderCityName,

            "receiverName"        => $receiverName,
            "receiverEmail"       => $receiverEmail,
            "receiverPhone"       => $receiverPhone,
            "receiverPhone1"      => $receiverPhone1,
            "receiverCountryId"   => $receiverCountryId,
            "receiverCountryName" => $receiverCountryName,
            "receiverCountryCode" => $receiverCountryCode,
            "receiverCityId"      => $receiverCityId,
            "receiverStreet"      => $receiverStreet,

            "receiverZipCode"     => $receiverZipCode,
            "receiverCityName"    => $receiverCityName,
        ];

        // $body = [
        //     "shippingCompanyId" => "660030be07d4cc8bca3eb0eb",
        //     "method" => "local",
        //     "senderName" => "متجر Oday Ahmed",
        //     "senderEmail" => "aaaa@gmail.com",
        //     "senderPhone" => "+966-501932466",
        //     "senderPhone1" => "",
        //     "senderCountryId" => "65fd1a1c1fdbc094e3369b29",
        //     "senderCountryName" => "Saudi Arabia",
        //     "senderCountryCode" => "SA",
        //     "senderCityId" => "6875240b26cb8ca53909f2f4",
        //     "senderStreet" => "635 Corkeryton, HI 98474-3921",
        //     "senderZipCode" => "",
        //     "type" => "box",
        //     "length" => 1,
        //     "width" => 1,
        //     "height" => 1,
        //     "weight" => 1,
        //     "packagesCount" => 1,
        //     "description" => "jyui",
        //     "isCommercial" => false,
        //     "isCod" => false,
        //     "isWeightEdited" => true,
        //     "senderCityName" => "Diriyah",
        //     "receiverName" => "عدي محمود",
        //     "receiverEmail" => "ahmedred@gmail.com",
        //     "receiverPhone" => "+966-501932466",
        //     "receiverPhone1" => "",
        //     "receiverCountryId" => "65fd1a1c1fdbc094e3369b29",
        //     "receiverCountryName" => "Saudi Arabia",
        //     "receiverCountryCode" => "SA",
        //     "receiverCityId" => "6875236a26cb8ca53909f28d",
        //     "receiverStreet" => "شارع ٢٣ يوليو امام قاعة كليوباترا",
        //     "receiverZipCode" => "",
        //     "receiverCityName" => "Rumah",
        // ];

        // If your API sometimes requires userId, uncomment:
        // $body['userId'] = $userId;

        // Do NOT add anything else.

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

    protected function ensureWalletBalance($user, float $grandTotal): ?array
    {
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
                $codFeePerReceiver     = 15;
            } else {
                $adminSettings = AdminSetting::where('admin_id', $adminId)->first();
                $extraWeightPricePerKg = $adminSettings ? $adminSettings->extra_weight_price : 2;
                $codFeePerReceiver     = $adminSettings ? $adminSettings->cash_on_delivery_price : 15;
            }
            $response = Http::withHeaders([
                'accept'    => '*/*',
                'x-api-key' => env('GHAYA_API_KEY', 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'),
            ])->get('https://ghaya-express-staging-af597af07557.herokuapp.com/api/shipping-companies', [
                'page'           => 0,
                'pageSize'       => 50,
                'orderColumn'    => 'createdAt',
                'orderDirection' => 'desc',
            ]);

            if (!$response->successful()) {
                return ['results' => []];
            }

            $data = $response->json();
            $apiCompanies = isset($data['results']) && is_array($data['results']) ? $data['results'] : [];
            $allowedIds = [];
            if ($adminId) {
                $allowedIds = AllowedCompany::where('admin_id', $adminId)
                    ->where('status', 'active')
                    ->pluck('company_id')
                    ->map(fn($v) => (string) $v)
                    ->all();
            }
            $userShippingPrices = $user->shippingPrices()
                ->select('company_id', 'company_name', 'local_price', 'international_price')
                ->get();

            $userCompanyIds       = $userShippingPrices->pluck('company_id')->map(fn($v) => (string) $v)->all();
            $userShippingPricesMap = $userShippingPrices->keyBy('company_id');
            $enrich = function (array $company, $userPrice = null) use ($extraWeightPricePerKg, $codFeePerReceiver) {
                $methods = (array) Arr::get($company, 'shippingMethods', []);
                $company['userLocalPrice']          = $userPrice->local_price ?? Arr::get($company, 'localPrice');
                $company['userInternationalPrice']  = $userPrice->international_price ?? Arr::get($company, 'internationalPrice');
                $company['adminExtraWeightPrice']   = $extraWeightPricePerKg;
                $company['adminCodFee']             = $codFeePerReceiver;
                if (in_array('local', $methods, true)) {
                    $company['effectiveLocalPrice'] = $userPrice->local_price ?? Arr::get($company, 'localPrice');
                }

                if (in_array('international', $methods, true)) {
                    $company['effectiveInternationalPrice'] = $userPrice->international_price ?? Arr::get($company, 'internationalPrice');
                }

                $company['hasCod'] = in_array('cashOnDelivery', $methods, true);

                return $company;
            };
            if (empty($userCompanyIds)) {
                $filteredCompanies = collect($apiCompanies)
                    ->filter(function ($company) use ($allowedIds) {
                        $id       = (string) Arr::get($company, 'id', '');
                        $isActive = (bool) Arr::get($company, 'isActive', false);

                        if (!$isActive) {
                            return false;
                        }
                        if (!empty($allowedIds)) {
                            return in_array($id, $allowedIds, true);
                        }
                        return true;
                    })
                    ->map(fn($company) => $enrich($company, null))
                    ->values()
                    ->toArray();

                return [
                    'results' => $filteredCompanies,
                    'admin_settings' => [
                        'extra_weight_price_per_kg' => $extraWeightPricePerKg,
                        'cod_fee_per_receiver'      => $codFeePerReceiver,
                    ],
                ];
            }
            $targetIds = !empty($allowedIds)
                ? array_values(array_intersect($userCompanyIds, $allowedIds))
                : $userCompanyIds;

            $filteredCompanies = collect($apiCompanies)
                ->filter(function ($company) use ($targetIds) {
                    $id       = (string) Arr::get($company, 'id', '');
                    $isActive = (bool) Arr::get($company, 'isActive', false);
                    return $isActive && in_array($id, $targetIds, true);
                })
                ->map(function ($company) use ($userShippingPricesMap, $enrich) {
                    $id        = (string) Arr::get($company, 'id', '');
                    $userPrice = $userShippingPricesMap->get($id);
                    return $enrich($company, $userPrice);
                })
                ->values()
                ->toArray();

            return [
                'results' => $filteredCompanies,
                'admin_settings' => [
                    'extra_weight_price_per_kg' => $extraWeightPricePerKg,
                    'cod_fee_per_receiver'      => $codFeePerReceiver,
                ],
            ];
        } catch (\Throwable $e) {
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
