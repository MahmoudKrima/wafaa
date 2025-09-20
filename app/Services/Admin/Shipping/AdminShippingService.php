<?php

namespace App\Services\Admin\Shipping;


use App\Models\User;
use App\Traits\ImageTrait;
use App\Models\AdminSetting;
use App\Models\CancelRequest;
use App\Traits\TranslateTrait;
use Illuminate\Support\Facades\Http;
use App\Services\User\Shipping\ShippingService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Requests\Admin\Shipping\SearchShippingRequest;

class AdminShippingService extends ShippingService
{
    use ImageTrait, TranslateTrait;

    public function resolveGhayaApiKey()
    {
        $ownerId = getAdminIdOrCreatedBy();
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
    public function getUserListShipments(array $filters = [], array $users = [])
    {
        $base = [
            'page'           => 0,
            'pageSize'       => 15,
            'orderColumn'    => 'createdAt',
            'orderDirection' => 'desc',
        ];

        if (!empty($users)) {
            $filters['externalAppId'] = array_values(array_map('strval', $users));
        }

        $clean = array_filter($filters, fn($v) => !is_null($v) && $v !== '');
        unset($clean['receiverName'], $clean['receiverPhone'], $clean['userId']);

        $params = array_merge($base, $clean);

        $parts = [];
        foreach ($params as $k => $v) {
            if ($k === 'externalAppId') {
                $vals = is_array($v) ? $v : [$v];
                foreach ($vals as $one) {
                    $parts[] = 'externalAppId=' . urlencode((string) $one);
                }
            } else {
                $parts[] = urlencode($k) . '=' . urlencode((string) $v);
            }
        }

        $url = $this->ghayaUrl('shipments') . '?' . implode('&', $parts);

        $res = $this->ghayaRequest()->get($url);

        return $res->json();
    }

    public function getShippingCompanies()
    {
        $res = $this->ghayaRequest()
            ->get($this->ghayaUrl('shipping-companies'), [
                'page' => 0,
                'pageSize' => 500,
            ]);

        $json = $res->json();
        return $json['results'] ?? $json ?? [];
    }

    public function export($request): StreamedResponse
    {
        $filters = $this->buildFiltersFromRequest($request);
        $pageSize = 200;
        $page = 0;
        $allRows = [];

        do {
            $pageFilters = array_merge($filters, [
                'page' => $page,
                'pageSize' => $pageSize,
            ]);

            $chunk = $this->getUserListShipments($pageFilters);

            $results = $chunk['results'] ?? [];
            $total = (int) ($chunk['total'] ?? count($results));

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
                $tracking = $row['trackingNumber'] ?? __('admin.n/a');
                $sender = $row['shipmentDetails']['senderName'] ?? __('admin.n/a');
                $receiver = $row['receiver']['fullName'] ?? __('admin.n/a');
                $weight = $row['shipmentDetails']['weight'] ?? __('admin.n/a');
                $method = __('admin.' . $row['method']) ?? __('admin.n/a');
                $type = __('admin.' . $row['type']) ?? __('admin.n/a');
                $createdAt = isset($row['createdAt']) ? \Carbon\Carbon::parse($row['createdAt'])->format('Y-m-d H:i') : __('admin.n/a');
                $cod = !empty($row['isCod']) ? __('admin.cash_on_delivery') : __('admin.regular_shipment');
                $status = __('admin.' . strtolower($row['status'])) ?? __('admin.n/a');
                $labelUrl = $row['labelUrl'] ?? __('admin.n/a');
                $trackUrl = $row['trackingUrl'] ?? __('admin.n/a');

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
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
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

    public function show(string $id): array
    {
        $shipment = $this->getShipmentById($id);
        if (!$shipment) {
            abort(404, 'Shipment not found');
        }
        $externalAppId = (string) data_get($shipment, 'externalAppId', '');
        $authUser = User::where('id', $externalAppId)
            ->where('created_by', getAdminIdOrCreatedBy())
            ->first();

        if (!$authUser) {
            abort(404, 'User not found');
        }
        $company        = (array) data_get($shipment, 'shippingCompany', []);
        $details        = (array) data_get($shipment, 'shipmentDetails', []);
        $trackingNumber = (string) data_get($shipment, 'trackingNumber', '—');
        $senderName        = (string) data_get($details, 'senderName');
        $senderPhone       = (string) data_get($details, 'senderPhone');
        $senderPhone1      = (string) data_get($details, 'senderPhone1');
        $senderAddress     = (string) data_get($details, 'senderStreet');
        $senderCountryName = (string) data_get($details, 'senderCountryName');
        $senderCityName    = (string) data_get($details, 'senderCityName');

        $length         = (int)   data_get($details, 'length', 0);
        $width          = (int)   data_get($details, 'width', 0);
        $height         = (int)   data_get($details, 'height', 0);
        $weight         = (float) data_get($details, 'weight', 0);
        $packagesCount  = (int)   data_get($details, 'packagesCount', 1);
        $pkgDescription = (string) data_get($details, 'description', '');

        $companyId = (string) data_get($shipment, 'shippingCompanyId', '');
        $codFee    = (float)  data_get($shipment, 'shipmentCod.codPrice', '');

        $receiverName        = (string) data_get($details, 'receiverName', '—');
        $receiverPhone       = (string) data_get($details, 'receiverPhone', '—');
        $receiverPhone1      = (string) data_get($details, 'receiverPhone1', '—');
        $receiverStreet      = (string) data_get($details, 'receiverStreet', '—');
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
        $localShippingFee         = $shipPrice->local_price ?? 0.0;

        if ($shipPrice) {
            $shippingFee = data_get($shipment, 'method') === 'local'
                ? (float) $localShippingFee
                : (float) $internationalShippingFee;
        }
        $companyWeight = (float) data_get($shipment, 'shippingCompany.maxWeight', 0);
        $adminSetting  = AdminSetting::where('admin_id', getAdminIdOrCreatedBy())->first();
        $extraWeightPer = 0.0;

        if ($weight > $companyWeight) {
            $extraWeight    = (float) $weight - $companyWeight;
            $extraWeightPer = $extraWeight * (float) ($adminSetting->extra_weight_price ?? 0.0);
        }

        $isCod          = (bool) data_get($shipment, 'isCod', false);
        $codPerReceiver = ($adminSetting && $isCod) ? (float) $adminSetting->cash_on_delivery_price : 0.0;

        $extraWeightfee = $extraWeightPer;
        if ($isCod) {
            $finalTotalCod    = $codPerReceiver;
            $finalWeightFee   = $extraWeightPer;
            $finalShippingFee = $shippingFee;
            $total = (float) ($finalShippingFee + $finalTotalCod + $finalWeightFee) ?: 0.0;
        } else {
            $total = (float) ($shippingFee + $extraWeightfee) ?: 0.0;
        }

        $perReceiverTotal = $total;
        $extraWeightFee   = (float) ($adminSetting->extra_weight_price ?? 0.0);

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


    private function getShipmentById(string $id): array
    {
        $res = $this->ghayaRequest()->get($this->ghayaUrl("shipments/{$id}"));

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

    public function delete(string $id, $externalAppId = null)
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
                'user_id' => $externalAppId,
                'shipment_id' => $id,
                'status' => 'cancelShipment',
            ]);
            return 'canceled';
        }
        return 'failed';
    }
}
