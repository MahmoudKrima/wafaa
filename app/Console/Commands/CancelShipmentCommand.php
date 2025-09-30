<?php

namespace App\Console\Commands;

use App\Enum\NotificationTypeEnum;
use App\Models\AdminSetting;
use App\Models\CancelRequest;
use App\Models\User;
use App\Models\WalletLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CancelShipmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipment:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $shipments = CancelRequest::where('status', 'cancelShipment')->get();
        if ($shipments) {
            foreach ($shipments as $shipment) {
                $shipmentt = $this->getShipmentById($shipment->shipment_id, $shipment->user_id);
                if ($shipmentt['status'] == 'canceled') {
                    $data['method'] = $shipmentt['method'];
                    $data['userId'] = $shipmentt['externalAppId'];
                    $data['companyId'] = $shipmentt['shippingCompanyId'];
                    $data['extraWeight'] = $shipmentt['shipmentPrice']['extraWeight'] ?? 0;
                    $data['isCod'] = $shipmentt['isCod'];
                    $data['trackingNumber'] = $shipmentt['trackingNumber'];
                    $this->userCancelShipment($data);
                    $shipment->delete();
                }
            }
        }

        $this->info('Shipment cancel job executed.');
    }

  
    public function userCancelShipment($data)
    {
        $user = User::find($data['userId']);
        $shippingPrice = $user->shippingPrices()->where('company_id', $data['companyId'])->first();
        $amount = 0;
        $adminSetting = AdminSetting::where('admin_id', $user->created_by)->first();
        if ($data['method'] == 'local') {
            $amount += $shippingPrice->local_price ?? 0.0;
        }
        if ($data['method'] == 'international') {
            $amount += $shippingPrice->international_price ?? 0.0;
        }
        if ($data['isCod'] == true) {
            $amount += $adminSetting->cash_on_delivery_price ?? 0.0;
        }
        if ($data['extraWeight'] > 0) {
            $amount += $data['extraWeight'] * $adminSetting->extra_weight_price;
        }
        $oldBalance = $user->wallet->balance;
        $newBalance = $user->wallet->balance + $amount;
        $user->wallet->balance += $amount;
        $user->wallet->save();
        WalletLog::create([
            'user_id'    => $user->id,
            'amount'     => $amount,
            'trans_type' => 'cancel_shipment',
            'type'       => 'deposit',
            //'admin_id'   => auth('admin')->user()->id,
            'description' => [
                'ar' => __('admin.cancel_shippment_status_updated', [
                    'status'   => __("admin.canceled", [], 'ar'),
                    'previous' => number_format($oldBalance, 2),
                    'current'  => number_format($newBalance, 2),
                    'tracking_number'  => $data['trackingNumber'],
                ], 'ar'),

                'en' => __('admin.cancel_shippment_status_updated', [
                    'status'   => __("admin.canceled", [], 'en'),
                    'previous' => number_format($oldBalance, 2),
                    'current'  => number_format($newBalance, 2),
                    'tracking_number'  => $data['trackingNumber'],
                ], 'en'),
            ],
        ]);
        $message = [
            'en' => __('admin.cancel_shippment_status_updated', [
                'status'   => __("admin.canceled", [], 'en'),
                'previous' => number_format($oldBalance, 2),
                'current'  => number_format($newBalance, 2),
                'tracking_number'  => $data['trackingNumber'],
            ], 'en'),

            'ar' => __('admin.cancel_shippment_status_updated', [
                'status'   => __("admin.canceled", [], 'en'),
                'previous' => number_format($oldBalance, 2),
                'current'  => number_format($newBalance, 2),
                'tracking_number'  => $data['trackingNumber'],
            ], 'ar'),
        ];

        $user->notifications()->create([
            'id'               => (string) Str::uuid(),
            'type'             => NotificationTypeEnum::CANCELSHIPMENT->value,
            'data'             => $message,
            'reciverable_type' => null,
            'reciverable_id'   => null,
        ]);
    }

    public function getShipmentById(string $id, int $userId = null): array
    {
        $response = $this->ghayaRequest($userId)->get($this->ghayaUrl("shipments/{$id}"));

        if ($response->status() === 404) {
            abort(404, 'Shipment not found');
        }

        return $response->json();
    }

    private function resolveGhayaApiKey(int $userId = null): string
    {
        if ($userId) {
            $user = User::find($userId);
            $ownerId = $user ? $user->created_by : 2;
        }
        return (string) ((string)$ownerId == '1'
            ? config('services.ghaya.key')
            : config('services.ghaya.key_two'));
    }

    public function ghayaRequest(int $userId = null)
    {
        return Http::withHeaders([
            'accept'    => '*/*',
            'x-api-key' => $this->resolveGhayaApiKey($userId),
        ]);
    }


    public function ghayaUrl(string $endpoint): string
    {
        return rtrim(config('services.ghaya.base_url'), '/') . '/' . ltrim($endpoint, '/');
    }
}
