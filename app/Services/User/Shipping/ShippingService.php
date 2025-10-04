<?php

namespace App\Services\User\Shipping;

use App\Models\Reciever;
use App\Models\WalletLog;
use App\Traits\ImageTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\AdminSetting;
use App\Models\CancelRequest;
use App\Models\AllowedCompany;
use App\Traits\TranslateTrait;
use App\Models\UserDescription;
use App\Enum\NotificationTypeEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Requests\User\Shipping\SearchShippingRequest;
use App\Models\Sender;

class ShippingService
{
    use ImageTrait, TranslateTrait;

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

    public function getUserListShipments(array $filters = [])
    {
        $base = [
            'page'           => 0,
            'pageSize'       => 10,
            'orderColumn'    => 'createdAt',
            'orderDirection' => 'desc',
            'externalAppId'  => (string) auth()->id(),
        ];
        $localOnly = ['receiverName', 'receiverPhone'];
        foreach ($localOnly as $l) {
            unset($filters[$l]);
        }
        $clean = array_filter(
            $filters,
            fn($v) => !is_null($v) && !(is_string($v) && $v === '')
        );

        $query = array_merge($base, $clean);

        $res = $this->ghayaRequest()
            ->get($this->ghayaUrl('shipments'), $query);

        return $res->json();
    }

    public function getShippingCompanies()
    {
        $res = $this->ghayaRequest()
            ->get($this->ghayaUrl('shipping-companies'), [
                'page'     => 0,
                'pageSize' => 500,
            ]);

        $json = $res->json();
        return $json['results'] ?? $json ?? [];
    }


    private function getShipmentById(string $id): array
    {
        $query = [
            'externalAppId' => (string) auth()->id(),
        ];

        $res = $this->ghayaRequest()->get($this->ghayaUrl("shipments/{$id}"), $query);

        if ($res->status() === 404) {
            abort(404, 'Shipment not found');
        }
        $res->throw();
        $payload = (array) $res->json();
        $shipment = (array) (
            data_get($payload, 'result') ??
            data_get($payload, 'data') ??
            $payload
        );

        if (empty($shipment)) {
            abort(404, 'Shipment not found');
        }

        return $shipment;
    }

    public function show(string $id): array
    {
        $authUser = auth()->user();
        $shipment = $this->getShipmentById($id);
        $company  = (array) data_get($shipment, 'shippingCompany', []);
        $details  = (array) data_get($shipment, 'shipmentDetails', []);
        $trackingNumber  = (string) data_get($shipment, 'trackingNumber', '—');
        $senderName   =  (string) (data_get($details, 'senderName'));
        $senderPhone        = (string) (data_get($details, 'senderPhone'));
        $senderPhone1        = (string) (data_get($details, 'senderPhone1'));
        $senderAddress      = (string) (data_get($details, 'senderStreet'));
        $senderCountryName  = (string) data_get($details, 'senderCountryName');
        $senderCityName     = (string) data_get($details, 'senderCityName');
        $length        = (int) data_get($details, 'length', 0);
        $width         = (int) data_get($details, 'width', 0);
        $height        = (int) data_get($details, 'height', 0);
        $weight        = (float) data_get($details, 'weight', 0);
        $packagesCount = (int) data_get($details, 'packagesCount', 1);
        $pkgDescription = (string) data_get($details, 'description', '');
        $companyId = (string) data_get($shipment, 'shippingCompanyId', '');
        $codFee         = (float) data_get($shipment, 'shipmentCod.codPrice', '');
        $receiverName = (string) data_get($details, 'receiverName', '—');
        $receiverPhone = (string) data_get($details, 'receiverPhone', '—');
        $receiverPhone1 = (string) data_get($details, 'receiverPhone1', '—');
        $receiverStreet = (string) data_get($details, 'receiverStreet', '—');
        $receiverCountryName = (string) data_get($details, 'receiverCountryName', '—');
        $receiverCityName    = (string) data_get($details, 'receiverCityName', '—');
        $labelUrl    = (string) data_get($shipment, 'labelUrl', '—');
        $status    = (string) data_get($shipment, 'status', '—');
        $createdAtRaw    = (string) data_get($details, 'createdAt', '—');
        $created_at = \Carbon\Carbon::parse($createdAtRaw)->format('Y-m-d H:i');

        $shipPrice = $authUser->shippingPrices()
            ->where('company_id', $companyId)
            ->first();

        $shippingFee = 0.0;
        $internationalShippingFee = $shipPrice->international_price ?? 0.0;
        $localShippingFee = $shipPrice->local_price ?? 0.0;
        if ($shipPrice) {
            $shippingFee = data_get($shipment, 'method') === 'local'
                ? (float) $localShippingFee
                : (float) $internationalShippingFee;
        }

        $companyWeight = (float) data_get($shipment, 'shippingCompany.maxWeight', 0);
        $adminSetting = AdminSetting::where('admin_id', $authUser->created_by)->first();
        $extraWeightPer = 0.0;

        if ($weight > $companyWeight) {
            $extraWeight = (float) $weight - $companyWeight;
            $extraWeightPer = $extraWeight * (float) $adminSetting->extra_weight_price;
        }

        $isCod          = (bool) data_get($shipment, 'isCod', false);
        $codPerReceiver = ($adminSetting && $isCod) ? (float) $adminSetting->cash_on_delivery_price : 0.0;

        $extraWeightfee = $extraWeightPer;
        if ($isCod) {
            $finalTotalCod = $codPerReceiver;
            $finalWeightFee = $extraWeightPer;
            $finalShippingFee = $shippingFee;
            $total = (float) ($finalShippingFee + $finalTotalCod + $finalWeightFee) ?: 0.0;
        } else {
            $total = (float) ($shippingFee + $extraWeightfee) ?: 0.0;
        }

        $perReceiverTotal = $total;
        $extraWeightFee = $adminSetting->extra_weight_price;

        return [
            'shipment'               => $shipment,
            'senderName'             => $senderName,
            'senderPhone'            => $senderPhone,
            'senderPhone1'           => $senderPhone1,
            'senderAddress'          => $senderAddress,
            'senderCity'             => $senderCityName,
            'senderCountryName'      => $senderCountryName,
            'receiverCityName'       => $receiverCityName,
            'receiverCountryName'    => $receiverCountryName,
            'company'                => $company,
            'receiverCount'          => 1,
            'shippingFee'            => $shippingFee,
            'codFee'                 => $codFee,
            'extraWeightPerReceiver' => (float) $extraWeightPer,
            'codPerReceiver'         => (float) $codPerReceiver,
            'total'                  => $total,
            'perReceiverTotal'       => $perReceiverTotal,
            'length'                 => $length,
            'width'                  => $width,
            'height'                 => $height,
            'weight'                 => $weight,
            'packagesCount'          => $packagesCount,
            'packageDescription'     => $pkgDescription,
            'extraWeightfee'         => $extraWeightfee,
            'companyWeight'          => $companyWeight,
            'extraWeightFee'         => $extraWeightFee,
            'receiverName'           => $receiverName,
            'receiverPhone'          => $receiverPhone,
            'receiverPhone1'         => $receiverPhone1,
            'receiverStreet'         => $receiverStreet,
            'trackingNumber'         => $trackingNumber,
            'labelUrl'               => $labelUrl,
            'status'                 => $status,
            'created_at'             => $created_at,
        ];
    }

    public function export($request): StreamedResponse
    {
        $filters = $this->buildFiltersFromRequest($request);
        $pageSize = 200;
        $page     = 0;
        $allRows  = [];

        do {
            $pageFilters = array_merge($filters, [
                'page'     => $page,
                'pageSize' => $pageSize,
            ]);

            $chunk = $this->getUserListShipments($pageFilters);

            $results = $chunk['results'] ?? [];
            $total   = (int) ($chunk['total'] ?? count($results));

            foreach ($results as $r) {
                $allRows[] = $r;
            }

            $page++;
            $fetched = count($allRows);
        } while ($fetched < $total && !empty($results));

        $fileName = 'shipments_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($allRows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, [
                'Company',
                'Tracking Number',
                'Sender',
                'Receiver',
                'Weight',
                'Method',
                'Type',
                'Created At',
                'COD',
                'Status',
                'Label URL',
                'Tracking URL',
            ]);

            foreach ($allRows as $row) {
                $companyName = $row['shippingCompany']['name'] ?? __('admin.n/a');
                $tracking    = $row['trackingNumber'] ?? __('admin.n/a');
                $sender      = $row['shipmentDetails']['senderName'] ?? __('admin.n/a');
                $receiver    = $row['receiver']['fullName'] ?? __('admin.n/a');
                $weight      = $row['shipmentDetails']['weight'] ?? __('admin.n/a');
                $method      = __('admin.' . $row['method']) ?? __('admin.n/a');
                $type        = __('admin.' . $row['type']) ?? __('admin.n/a');
                $createdAt   = isset($row['createdAt']) ? \Carbon\Carbon::parse($row['createdAt'])->format('Y-m-d H:i') : __('admin.n/a');
                $cod         = !empty($row['isCod']) ? __('admin.cash_on_delivery') : __('admin.regular_shipment');
                $status      = __('admin.' . strtolower($row['status'])) ?? __('admin.n/a');
                $labelUrl    = $row['labelUrl'] ?? __('admin.n/a');
                $trackUrl    = $row['trackingUrl'] ?? __('admin.n/a');

                fputcsv($out, [
                    $companyName,
                    $tracking,
                    $sender,
                    $receiver,
                    $weight,
                    $method,
                    $type,
                    $createdAt,
                    $cod,
                    $status,
                    $labelUrl,
                    $trackUrl,
                ]);
            }

            fclose($out);
        }, $fileName, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    private function buildFiltersFromRequest(SearchShippingRequest $request): array
    {
        $filters = $request->validated();
        if ($request->filled('isCod')) {
            $filters['isCod'] = $request->input('isCod') === 'true' ? 'true' : 'false';
        }
        if ($request->filled('dateFrom')) {
            $filters['dateFrom'] = \Carbon\Carbon::parse($request->input('dateFrom'))->format('Y-m-d');
        }
        if ($request->filled('dateTo')) {
            $filters['dateTo'] = \Carbon\Carbon::parse($request->input('dateTo'))->format('Y-m-d');
        }
        return $filters;
    }

    public function store($request)
    {
        \Log::info('Shipment creation request received', [
            'all_input' => $request->all(),
            'validated_data' => $request->validated(),
            'step' => 'request_received'
        ]);
        
        try {
            $data = $request->validated();
            \Log::info('Validation passed', ['data_keys' => array_keys($data)]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            throw $e;
        }
        
        $user = auth()->user();
        
        \Log::info('User authenticated', [
            'user_id' => $user->id,
            'user_email' => $user->email
        ]);

        $company = $this->getShippingCompany($data['shipping_company_id'] ?? null);
        if (!$company) {
            session()->forget('Success');
            return back()->with('Error', __('admin.shipping_company_not_found'));
        }

        $companyPrice = $user->shippingPrices
            ->firstWhere('company_id', (string)($data['shipping_company_id'] ?? ''));
        if (!$companyPrice) {
            session()->forget('Success');
            return back()->with('Error', __('admin.shipping_company_not_found'));
        }

        $method  = Str::lower(Arr::get($data, 'shipping_method', ''));
        $payment = Str::lower(Arr::get($data, 'payment_method', 'wallet'));

        if ($capErr = $this->validateCapabilities($company, $method, $payment)) {
            session()->forget('Success');
            return back()->with('Error', $capErr);
        }

        $receivers = $this->decodeReceivers(Arr::get($data, 'selected_receivers', ''));
        if (isset($receivers['error'])) {
            session()->forget('Success');
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

        if ($err = $this->ensureWalletBalance($user, $pricing['grand_total'])) {
            session()->forget('Success');
            return back()->with('Error', $err);
        }

        if ($request->hasFile('shipment_image')) {
            $data['shipment_image_path'] = ImageTrait::uploadImage($request->file('shipment_image'), 'shipments');
        }

        $sync = $this->syncNewReceivers($data['selected_receivers'], $user,$company);
        $data['selected_receivers'] = $sync['receivers'];

        try {
            $results = $this->createShipmentsForReceivers(
                company: $company,
                user: $user,
                selectedReceivers: $data['selected_receivers'],
                pricing: $pricing,
                requestData: $data,
                shipmentImagePath: $data['shipment_image_path'] ?? null,
                payment: $payment,
                method: $method,
                companyPrice: $companyPrice
            );
        } catch (\Throwable $e) {
            \Log::error('Shipment creation error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $data
            ]);
            session()->forget('Success');
            return redirect()
                ->route('user.shippings.index')
                ->with('Error', __('admin.something_wrong') . ': ' . $e->getMessage());
        }

        if (!empty($results['server_error'])) {
            session()->forget('Success');
            return redirect()
                ->route('user.shippings.index')
                ->with('Error', __('admin.something_wrong'));
        }

        if (!empty($results['failed'])) {
            session()->forget('Success');
            return redirect()
                ->route('user.shippings.index')
                ->with('Error', __('admin.shipments_partial_or_failed'));
        }

        session()->forget('Error');
        if ($data['description_type'] == 'new') {
            UserDescription::create([
                'user_id' => $user->id,
                'description' => $data['package_description'],
            ]);
        }
        if ($data['sender_kind'] == 'new') {
           $sender= Sender::create([
                'user_id' => $user->id,
                'name' => $data['sender_name'],
                'email' => $data['sender_email'] ?? null,
                'phone' => $data['sender_phone'],
                'additional_phone' => $data['sender_additional_phone'] ?? null,
                'postal_code' => $data['sender_postal_code'] ?? null,
                'address' => $data['sender_address'] ?? 'Address not provided',
            ]);
            $sender->shippingCompanies()->create([
                'shipping_company_id' => $data['shipping_company_id'],
                'city_id' => $data['sender_city_id'],
            ]);
        }

        return redirect()
            ->route('user.shippings.index')
            ->with('Success', __('admin.shippment_created_successfully'));
    }


    private function createShipmentsForReceivers(
        array $company,
        $user,
        array $selectedReceivers,
        array $pricing,
        array $requestData,
        ?string $shipmentImagePath,
        string $payment,
        string $method,
        $companyPrice
    ) {
        $results = [
            'success'      => [],
            'failed'       => [],
            'server_error' => false,
        ];

        $senderPayload     = $this->resolveSenderPayload($user);
        $isCod             = ($payment === 'cod');
        $shippingCompanyId = (string) Arr::get($company, 'id', Arr::get($requestData, 'shipping_company_id', ''));
        $userId            = (string) (auth()->id() ?? '');

        foreach ($selectedReceivers as $r) {
            $receiverId = $r['id'] ?? null;
            if (!$receiverId) {
                $results['failed'][] = ['receiver' => $r, 'error' => 'Receiver id is missing.'];
                continue;
            }

            $receiverModel = Reciever::find($receiverId);
            $receiver = [
                'id'           => (string) $receiverId,
                'name'         => (string)($r['name'] ?? $receiverModel?->name ?? ''),
                'phone'        => (string)($r['phone'] ?? $receiverModel?->phone ?? ''),
                'phone1'       => (string)($r['additional_phone'] ?? $receiverModel?->additional_phone ?? ''),
                'country_id'   => (string)($r['country_id'] ?? $receiverModel?->country_id ?? ''),
                'country_name' => (string)($r['country_name'] ?? ($receiverModel?->getTranslation('country_name', 'en') ?? '')),
                'country_code' => (string)($r['country_code'] ?? $receiverModel?->country_code ?? 'SA'),
                'city_id'      => (string)($r['city_id'] ?? $receiverModel?->city_id ?? ''),
                'city_name'    => (string)($r['city_name'] ?? ($receiverModel?->getTranslation('city_name', 'en') ?? '')),
                'street'       => (string)($r['address'] ?? $receiverModel?->address ?? ''),
                'zip'          => (string)($r['postal_code'] ?? $receiverModel?->postal_code ?? ''),
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

            if ($shipmentImagePath) {
                $body["imageUrl"] = displayImage($shipmentImagePath);
            }

            \Log::info('Sending shipment request to Ghaya API', [
                'url' => $this->ghayaUrl('shipments'),
                'body' => $body
            ]);

            $resp = $this->ghayaRequest()
                ->timeout(20)
                ->retry(2, 200)
                ->post($this->ghayaUrl('shipments'), $body);

            \Log::info('Ghaya API response', [
                'status' => $resp->status(),
                'response' => $resp->json(),
                'body' => $resp->body()
            ]);

            if ($resp->serverError()) {

                $results['server_error'] = true;
                break;
            }

            if ($resp->failed()) {
                $results['failed'][] = [
                    'receiver_id'   => $receiverId,
                    'status'        => $resp->status(),
                    'url'           => $this->ghayaUrl('shipments'),
                    'body_sent'     => $body,
                    'response_json' => $resp->json(),
                    'response_text' => $resp->body(),
                ];
                break;
            }

            $results['success'][] = [
                'receiver_id'   => $receiverId,
                'status'        => $resp->status(),
                'body_sent'     => $body,
                'response_json' => $resp->json(),
            ];
            $this->deductAndLog($user, $requestData, $companyPrice);
            $user->load('wallet');
        }

        return $results;
    }


    private function deductAndLog($user, array $data, $companyPrice): float
    {
        $amount         = 0.0;
        $isCod          = (string) Arr::get($data, 'payment_method');
        $extraKg        = (float) Arr::get($data, 'extra_kg', 0);
        $shippingMethod = (string) Arr::get($data, 'shipping_method', 'local');
        $adminSetting   = AdminSetting::where('admin_id', $user->created_by)->first();

        if ($isCod == 'cod') {
            $amount += (float) ($adminSetting->cash_on_delivery_price ?? 0);
        }

        if ($extraKg > 0) {
            $amount += (float) ($adminSetting->extra_weight_price ?? 0) * $extraKg;
        }

        $amount += (float) ($shippingMethod == 'local'
            ? ($companyPrice->local_price ?? 0)
            : ($companyPrice->international_price ?? 0));

        if ($amount <= 0) {
            return (float) optional($user->wallet)->balance ?? 0.0;
        }

        return DB::transaction(function () use ($user, $amount) {
            $wallet = $user->wallet()->lockForUpdate()->first()
                ?? $user->wallet()->create(['balance' => 0]);

            $oldBalance = (float) $wallet->balance;
            $newBalance = $oldBalance - (float) $amount;

            $wallet->balance = $newBalance;
            $wallet->save();

            WalletLog::create([
                'user_id'    => $user->id,
                'amount'     => number_format($amount, 2, '.', ''),
                'trans_type' => 'shippment',
                'type'       => 'deduct',
                'description' => [
                    'ar' => __('admin.shippment_paid', [
                        'previous' => number_format($oldBalance, 2),
                        'current'  => number_format($newBalance, 2),
                    ], 'ar'),
                    'en' => __('admin.shippment_paid', [
                        'previous' => number_format($oldBalance, 2),
                        'current'  => number_format($newBalance, 2),
                    ], 'en'),
                ],
            ]);

            $message = [
                'en' => __('admin.new_shippment_created_notification', [], 'en'),
                'ar' => __('admin.new_shippment_created_notification', [], 'ar'),
            ];

            $user->notifications()->create([
                'id'               => (string) Str::uuid(),
                'type'             => NotificationTypeEnum::NEWSHIPMENT->value,
                'data'             => $message,
                'reciverable_type' => null,
                'reciverable_id'   => null,
            ]);

            return (float) $wallet->balance;
        });
    }


    private function buildGhayaShipmentBody(
        string $shippingCompanyId,
        string $method,
        bool   $isCod,
        array  $sender,
        array  $receiver,
        array  $requestData,
        string $userId
    ): array {
        $normalizePhone = function (?string $raw, string $defaultCC = 'SA') {
            $p = trim((string)$raw);
            $p = preg_replace('/[()\s.]/', '', $p) ?? '';
            if (preg_match('/^00(\d+)/', $p, $m)) {
                $p = '+' . $m[1];
            }
            if (str_starts_with($p, '05')) {
                $p = '+966-' . substr($p, 1);
            }
            if (preg_match('/^\+\d+$/', $p)) {
                if (str_starts_with($p, '+9665')) {
                    return '+966-' . substr($p, 4);
                }
                return $p;
            }

            if (preg_match('/^\+\d+-?\d+$/', $p)) {
                return $p;
            }
            if (preg_match('/^\d{9,12}$/', $p) && $defaultCC === 'SA') {
                if (str_starts_with($p, '5')) {
                    return '+966-' . $p;
                }
                if (str_starts_with($p, '05')) {
                    return '+966-' . substr($p, 1);
                }
            }
            return $p;
        };

        $senderName        = (string)($requestData['sender_name']         ?? $sender['name']         ?? '');
        $senderPhone       = $normalizePhone($requestData['sender_phone'] ?? ($sender['phone'] ?? ''));
        $senderPhone1Raw   = $requestData['sender_additional_phone'] ?? ($sender['phone1'] ?? '');
        $senderPhone1      = $senderPhone1Raw !== '' ? $normalizePhone($senderPhone1Raw) : '';

        $senderCountryId   = (string)($requestData['sender_country_id']   ?? $sender['country_id']   ?? '65fd1a1c1fdbc094e3369b29');
        $senderCountryName = (string)($requestData['sender_country_name'] ?? $sender['country_name'] ?? 'Saudi Arabia');
        $senderCountryCode = (string)($sender['country_code'] ?? 'SA');

        // Ensure city fields have fallbacks
        $senderCityId      = (string)($requestData['sender_city_id']      ?? $sender['city_id']      ?? '1');
        $senderCityName    = (string)($requestData['sender_city_name']    ?? $sender['city_name']    ?? 'Default City');
        $senderStateId     = (string)($requestData['sender_state_id']     ?? $sender['state_id']     ?? '');
        $senderStateName   = (string)($requestData['sender_state_name']   ?? $sender['state_name']   ?? '');
        $senderStreet      = (string)($requestData['sender_address']      ?? $sender['street']       ?? '');
        $senderZipCode     = (string)($requestData['sender_postal_code']  ?? $sender['zip_code']     ?? '');

        // ----- PACKAGE from request -----
        $type          = (string)($requestData['package_type'] ?? 'box');
        $length        = (int) ($requestData['length']       ?? 0);
        $width         = (int) ($requestData['width']        ?? 0);
        $height        = (int) ($requestData['height']       ?? 0);
        $weight        = (int) ($requestData['entered_weight'] ?? $requestData['weight'] ?? 0);
        $packagesCount = (int)   ($requestData['packagesCount'] ?? 1);
        $description   = (string)($requestData['package_description'] ?? '');
        $isCommercial  = (bool)  ($requestData['is_commercial'] ?? false);
        $isWeightEdited = (bool) ($requestData['is_weight_edited'] ?? array_key_exists('entered_weight', $requestData));

        // ----- RECEIVER (Ghaya minimal) -----
        $receiverName        = (string)($receiver['name'] ?? '');
        $receiverPhone       = $normalizePhone($receiver['phone'] ?? '');
        $receiverPhone1Raw   = (string)($receiver['phone1'] ?? '');
        $receiverPhone1      = $receiverPhone1Raw !== '' ? $normalizePhone($receiverPhone1Raw) : '';

        $receiverCountryId   = (string)($receiver['country_id'] ?? '');
        $receiverCountryName = (string)($receiver['country_name'] ?? '');
        $receiverCountryCode = (string)($receiver['country_code'] ?? 'SA');

        $receiverCityId      = (string)($receiver['city_id'] ?? '');
        $receiverCityName    = (string)($receiver['city_name'] ?? '');
        $receiverStateId     = (string)($receiver['state_id'] ?? '');
        $receiverStateName   = (string)($receiver['state_name'] ?? '');
        $receiverStreet      = (string)($receiver['street'] ?? '');
        $receiverZipCode     = (string)($receiver['zip'] ?? '');

        $body = [
            "shippingCompanyId" => (string) $shippingCompanyId,
            "method"            => (string) $method,

            "senderName"        => $senderName,
            "senderPhone"       => $senderPhone,
            "senderPhone1"      => $senderPhone1,
            "senderCountryId"   => $senderCountryId,
            "senderCountryName" => $senderCountryName,
            "senderCountryCode" => $senderCountryCode,
            "senderCityId"      => $senderCityId,
            "senderStreet"      => $senderStreet,
            "senderZipCode"     => $senderZipCode,

            "type"              => $type,
            "length"            => (int)$length,
            "width"             => (int)$width,
            "height"            => (int)$height,
            "weight"            => (int) $weight,
            "packagesCount"     => $packagesCount,
            "description"       => $description,
            "isCommercial"      => $isCommercial,
            "isCod"             => (bool) $isCod,
            "isWeightEdited"    => (bool)$isWeightEdited,
            "senderCityName"    => $senderCityName,

            "receiverName"        => $receiverName,
            "receiverPhone"       => $receiverPhone,
            "receiverPhone1"      => $receiverPhone1,
            "receiverCountryId"   => $receiverCountryId,
            "receiverCountryName" => $receiverCountryName,
            "receiverCountryCode" => $receiverCountryCode,
            "receiverCityId"      => $receiverCityId,
            "receiverStreet"      => $receiverStreet,

            "receiverZipCode"     => $receiverZipCode,
            "receiverCityName"    => $receiverCityName,
            'externalAppId'       => $userId
        ];

        // Always include state fields - some companies like J&T require them
        // If state info is not available, we'll try to fetch it or use defaults
        if (empty($senderStateId) || empty($senderStateName)) {
            \Log::info('Fetching sender state info from city data', [
                'city_id' => $senderCityId,
                'shipping_company_id' => $shippingCompanyId
            ]);
            // Try to fetch state info from city data
            $cityStateInfo = $this->getStateInfoFromCity($senderCityId, $shippingCompanyId);
            if ($cityStateInfo) {
                $senderStateId = $cityStateInfo['state_id'] ?? $senderStateId;
                $senderStateName = $cityStateInfo['state_name'] ?? $senderStateName;
                \Log::info('Sender state info fetched', [
                    'state_id' => $senderStateId,
                    'state_name' => $senderStateName
                ]);
            }
        }
        
        if (empty($receiverStateId) || empty($receiverStateName)) {
            \Log::info('Fetching receiver state info from city data', [
                'city_id' => $receiverCityId,
                'shipping_company_id' => $shippingCompanyId
            ]);
            // Try to fetch state info from city data
            $cityStateInfo = $this->getStateInfoFromCity($receiverCityId, $shippingCompanyId);
            if ($cityStateInfo) {
                $receiverStateId = $cityStateInfo['state_id'] ?? $receiverStateId;
                $receiverStateName = $cityStateInfo['state_name'] ?? $receiverStateName;
                \Log::info('Receiver state info fetched', [
                    'state_id' => $receiverStateId,
                    'state_name' => $receiverStateName
                ]);
            }
        }
        
        // Always include state fields (required by some companies like J&T)
        $body["senderStateId"] = $senderStateId ?: 'default_state_id';
        $body["senderStateName"] = $senderStateName ?: 'Default State';
        $body["receiverStateId"] = $receiverStateId ?: 'default_state_id';
        $body["receiverStateName"] = $receiverStateName ?: 'Default State';
        
        \Log::info('Final state fields for API request', [
            'sender_state_id' => $body["senderStateId"],
            'sender_state_name' => $body["senderStateName"],
            'receiver_state_id' => $body["receiverStateId"],
            'receiver_state_name' => $body["receiverStateName"]
        ]);

        if ($isCod) {
            $body["codPrice"] = (int) $requestData['cod_amount'];
        }

        return $body;
    }

    private function getStateInfoFromCity($cityId, $shippingCompanyId)
    {
        try {
            // Get cities data for the company and country
            $citiesData = $this->getCitiesByCompanyAndCountry($shippingCompanyId, '65fd1a1c1fdbc094e3369b29');
            
            if (isset($citiesData['results']) && is_array($citiesData['results'])) {
                foreach ($citiesData['results'] as $city) {
                    if (($city['id'] ?? $city['_id'] ?? '') === $cityId) {
                        return [
                            'state_id' => $city['state_id'] ?? '',
                            'state_name' => $city['state_name'] ?? ''
                        ];
                    }
                }
            } elseif (is_array($citiesData)) {
                foreach ($citiesData as $city) {
                    if (($city['id'] ?? $city['_id'] ?? '') === $cityId) {
                        return [
                            'state_id' => $city['state_id'] ?? '',
                            'state_name' => $city['state_name'] ?? ''
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::error('Error fetching state info from city', [
                'city_id' => $cityId,
                'shipping_company_id' => $shippingCompanyId,
                'error' => $e->getMessage()
            ]);
        }
        
        return null;
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

    private function syncNewReceivers(array $selectedReceivers, $user, $company)
    {
        $createdIds   = [];
        $existingIds  = [];
        $final        = [];
        $countryCache = [];
        $stateCache   = [];
        $cityCache    = [];

        DB::transaction(function () use ($selectedReceivers, $user, $company, &$createdIds, &$existingIds, &$final, &$countryCache, &$stateCache, &$cityCache) {
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
                   
                ]);
                $new->shippingCompanies()->create([
                    'shipping_company_id' => $company['id'],
                    'city_id' => $cityId,
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
            $res = $this->ghayaRequest()
                ->get($this->ghayaUrl("shipping-companies/{$companyId}"));

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
        return (float) (optional($user->wallet)->balance ?? 0);
    }

    public function receivers($shippingCompanyId)
    {
        return Reciever::where('user_id', auth()->user()->id)->get();
    }

    public function senders($shippingCompanyId)
    {
        return Sender::where('user_id', auth()->user()->id)->get();
    }

    public function getUserShippingCompanies()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return ['results' => []];
            }

            $adminId = $user->created_by;

            $extraWeightPricePerKg = 0;
            $codFeePerReceiver     = 0;
            $allowInternational = true;

            if ($adminId) {
                $adminSettings = AdminSetting::where('admin_id', $adminId)->first();
                $extraWeightPricePerKg = (float) ($adminSettings?->extra_weight_price ?? 0);
                $codFeePerReceiver     = (float) ($adminSettings?->cash_on_delivery_price ?? 0);
                $allowInternational = strtolower((string) ($adminSettings?->international_shipping ?? 'yes')) !== 'no';
            }

            $resp = $this->ghayaRequest()->get($this->ghayaUrl('shipping-companies'), [
                'page' => 0,
                'pageSize' => 50,
                'orderColumn' => 'createdAt',
                'orderDirection' => 'desc',
            ]);
            if (!$resp->successful()) {
                return ['results' => []];
            }
            $apiCompanies = (array) data_get($resp->json(), 'results', []);

            $userShippingPrices = $user->shippingPrices()
                ->select('company_id', 'company_name', 'local_price', 'international_price')
                ->get();

            if ($userShippingPrices->isEmpty()) {
                return [
                    'results' => [],
                    'admin_settings' => [
                        'extra_weight_price_per_kg' => $extraWeightPricePerKg,
                        'cod_fee_per_receiver' => $codFeePerReceiver,
                    ],
                ];
            }

            $userCompanyIds = $userShippingPrices->pluck('company_id')->map(fn($v) => (string) $v)->all();
            $userPriceMap   = $userShippingPrices->keyBy(fn($row) => (string) $row->company_id);

            $enforceAllowed = false;
            $activeAllowedIds = [];
            if ($adminId) {
                $hasAnyAllowed = AllowedCompany::where('admin_id', $adminId)->exists();
                if ($hasAnyAllowed) {
                    $enforceAllowed = true;
                    $activeAllowedIds = AllowedCompany::where('admin_id', $adminId)
                        ->where('status', 'active')
                        ->pluck('company_id')
                        ->map(fn($v) => (string) $v)
                        ->all();
                }
            }

            $targetIds = $enforceAllowed
                ? array_values(array_intersect($userCompanyIds, $activeAllowedIds))
                : $userCompanyIds;

            if (empty($targetIds)) {
                return [
                    'results' => [],
                    'admin_settings' => [
                        'extra_weight_price_per_kg' => $extraWeightPricePerKg,
                        'cod_fee_per_receiver' => $codFeePerReceiver,
                    ],
                ];
            }

            $results = collect($apiCompanies)
                ->filter(function ($company) use ($targetIds) {
                    $id = (string) data_get($company, 'id', '');
                    $isActive = (bool) data_get($company, 'isActive', true);
                    return $isActive && in_array($id, $targetIds, true);
                })
                ->map(function ($company) use ($userPriceMap, $extraWeightPricePerKg, $codFeePerReceiver, $allowInternational) {
                    $id      = (string) data_get($company, 'id', '');
                    $methods = (array) data_get($company, 'shippingMethods', []);

                    $price = $userPriceMap->get($id);
                    if (!$price) return null;

                    if (!$allowInternational) {
                        $methods = array_values(array_filter($methods, fn($m) => $m !== 'international'));
                        $company['shippingMethods'] = $methods;
                    }

                    $company['userLocalPrice']         = (float) $price->local_price;
                    $company['userInternationalPrice'] = (float) $price->international_price;
                    $company['adminExtraWeightPrice']  = (float) $extraWeightPricePerKg;
                    $company['adminCodFee']            = (float) $codFeePerReceiver;

                    if (in_array('local', $methods, true)) {
                        $company['effectiveLocalPrice'] = (float) $price->local_price;
                    }
                    if (in_array('international', $methods, true)) {
                        $company['effectiveInternationalPrice'] = (float) $price->international_price;
                    }

                    $company['hasCod'] = in_array('cashOnDelivery', $methods, true)
                        || data_get($company, 'cash_on_delivery.enabled') === true
                        || data_get($company, 'cod.enabled') === true;

                    return $company;
                })
                ->filter()
                ->values()
                ->toArray();

            return [
                'results' => $results,
                'admin_settings' => [
                    'extra_weight_price_per_kg' => $extraWeightPricePerKg,
                    'cod_fee_per_receiver' => $codFeePerReceiver,
                ],
            ];
        } catch (\Throwable $e) {
            return ['results' => []];
        }
    }

    public function getStates()
    {
        try {
            $response = $this->ghayaRequest()
                ->get($this->ghayaUrl('states'));

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
            $response = $this->ghayaRequest()
                ->get($this->ghayaUrl('cities'));

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
            $response = $this->ghayaRequest()
                ->get($this->ghayaUrl('cities'), [
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
            $response = $this->ghayaRequest()
                ->get($this->ghayaUrl("countries/{$countryId}"));

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
            $response = $this->ghayaRequest()
                ->get($this->ghayaUrl("states/{$stateId}"));

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
            $response = $this->ghayaRequest()
                ->get($this->ghayaUrl("cities/{$cityId}"));

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function delete(string $id)
    {

        $payload = [
            'type' => 'cancelShipment',
            'shipmentId' => $id,
        ];

        $response = $this->ghayaRequest()
            ->asJson()
            ->post($this->ghayaUrl('requests'), $payload);

        if ($response->successful()) {
            CancelRequest::create([
                'user_id' => auth()->id(),
                'shipment_id' => $id,
                'status' => 'cancelShipment',
            ]);
            return 'canceled';
        }
        return 'failed';
    }

    public function getCitiesByCompanyAndCountry(
        string $shippingCompanyId,
        string $countryId,
        int $page = 0,
        int $pageSize = 1000,
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
                $data = $response->json();
                
                // Extract state information from city data
                if (isset($data['results']) && is_array($data['results'])) {
                    foreach ($data['results'] as &$city) {
                        // Extract state information from city data based on actual API structure
                        if (isset($city['state']) && is_array($city['state'])) {
                            // State is a nested object with id and name
                            $city['state_id'] = $city['state']['id'] ?? $city['state']['_id'] ?? null;
                            
                            // Handle multilingual state names
                            if (isset($city['state']['name']) && is_array($city['state']['name'])) {
                                $city['state_name'] = $city['state']['name']['en'] ?? $city['state']['name']['ar'] ?? '';
                            } else {
                                $city['state_name'] = $city['state']['name'] ?? '';
                            }
                        } elseif (isset($city['stateId'])) {
                            // Fallback: stateId is directly on city object
                            $city['state_id'] = $city['stateId'];
                            $city['state_name'] = $city['stateName'] ?? '';
                            
                            // Handle multilingual state names if stateName is an array
                            if (is_array($city['state_name'])) {
                                $city['state_name'] = $city['state_name']['en'] ?? $city['state_name']['ar'] ?? '';
                            }
                        }
                    }
                } elseif (is_array($data)) {
                    // Handle case where data is directly an array of cities
                    foreach ($data as &$city) {
                        // Extract state information from city data based on actual API structure
                        if (isset($city['state']) && is_array($city['state'])) {
                            // State is a nested object with id and name
                            $city['state_id'] = $city['state']['id'] ?? $city['state']['_id'] ?? null;
                            
                            // Handle multilingual state names
                            if (isset($city['state']['name']) && is_array($city['state']['name'])) {
                                $city['state_name'] = $city['state']['name']['en'] ?? $city['state']['name']['ar'] ?? '';
                            } else {
                                $city['state_name'] = $city['state']['name'] ?? '';
                            }
                        } elseif (isset($city['stateId'])) {
                            // Fallback: stateId is directly on city object
                            $city['state_id'] = $city['stateId'];
                            $city['state_name'] = $city['stateName'] ?? '';
                            
                            // Handle multilingual state names if stateName is an array
                            if (is_array($city['state_name'])) {
                                $city['state_name'] = $city['state_name']['en'] ?? $city['state_name']['ar'] ?? '';
                            }
                        }
                    }
                }
                
                return $data;
            }
            return [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
