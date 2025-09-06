<?php

namespace App\Services\Admin\UserSettings;

use App\Models\User;
use App\Models\WalletLog;
use App\Filters\CityFilter;
use Illuminate\Support\Str;
use App\Filters\EmailFilter;
use App\Filters\PhoneFilter;
use Illuminate\Http\Request;
use App\Filters\DateToFilter;
use App\Models\AllowedCompany;
use App\Traits\TranslateTrait;
use App\Filters\DateFromFilter;
use App\Filters\NameJsonFilter;
use Illuminate\Http\Client\Pool;
use Illuminate\Pipeline\Pipeline;
use App\Enum\NotificationTypeEnum;
use App\Filters\TransActionFilter;
use App\Filters\WalletLogTypeFilter;
use Illuminate\Support\Facades\Http;
use App\Filters\ActivationStatusFilter;


class UserService
{
    use TranslateTrait;

    public function index()
    {
        return User::withAllRelations()
            ->where('created_by', getAdminIdOrCreatedBy())
            ->orderBy('id', 'desc')
            ->paginate();
    }
    public function search(Request $request)
    {

        $request->validated();
        return app(Pipeline::class)
            ->send(User::query())
            ->through([
                NameJsonFilter::class,
                EmailFilter::class,
                PhoneFilter::class,
                CityFilter::class,
                ActivationStatusFilter::class,
            ])
            ->thenReturn()
            ->withAllRelations()
            ->where('created_by', getAdminIdOrCreatedBy())
            ->orderBy('id', 'desc')
            ->paginate()
            ->withQueryString();
    }


    public function allowedCompanies()
    {
        $adminId = getAdminIdOrCreatedBy();
        $ids = AllowedCompany::where('admin_id', $adminId)
            ->pluck('company_id')
            ->all();
        $companies = $this->getShippingCompaniesByIds($ids, onlyActive: true, keyById: false);
        return $companies;
    }

    protected function getShippingCompaniesByIds(array $ids, bool $onlyActive = false, bool $keyById = false): array
    {
        $ids = array_values(array_filter(array_unique(array_map('strval', $ids))));
        if (empty($ids)) {
            return [];
        }

        $headers = [
            'accept'    => '*/*',
            'x-api-key' => env('GHAYA_API_KEY', 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'),
        ];
        $responses = Http::pool(function (Pool $pool) use ($ids, $headers) {
            return array_map(
                fn($id) => $pool->withHeaders($headers)
                    ->get('https://ghaya-express-staging-af597af07557.herokuapp.com/api/shipping-companies/' . $id),
                $ids
            );
        });

        $outList = [];
        $outMap  = [];

        foreach ($responses as $i => $response) {
            $id = $ids[$i];

            if (!$response || !$response->successful()) {
                continue;
            }

            $json = $response->json();
            $company = is_array($json) ? (data_get($json, 'result', $json)) : null;
            if (!$company) {
                continue;
            }

            if ($onlyActive && !data_get($company, 'isActive', false)) {
                continue;
            }

            if (!isset($company['id'])) {
                $company['id'] = $id;
            }

            if ($keyById) {
                $outMap[(string) $id] = $company;
            } else {
                $outList[] = $company;
            }
        }

        return $keyById ? $outMap : $outList;
    }


    public function store($request)
    {
        $data = $request->validated();
        $data['created_by'] = getAdminIdOrCreatedBy();
        $data['name'] = $this->translate($data['name_ar'], $data['name_en']);
        $data['added_by'] = auth('admin')->id();
        $user = User::create($data);
        foreach ($data['shipping_prices'] as $shippingPrice) {
            $user->shippingPrices()->create([
                'company_id' => $shippingPrice['id'],
                'company_name' => $shippingPrice['name'],
                'local_price' => $shippingPrice['localprice'],
                'international_price' => $shippingPrice['internationalprice'] ?? null,
            ]);
        }
        if (isset($data['balance']) && $data['balance'] !== null) {
            $user->wallet()->update([
                'balance' => $data['balance'],
            ]);
            $user->walletLogs()->create([
                'user_id' => $user->id,
                'amount' => $data['balance'],
                'type' => 'deposit',
                'trans_type' => 'edit_balance',
                'admin_id' => auth('admin')->id(),
                'description' => [
                    'ar' => __('admin.deposit_balance', [
                        'previous' => 0,
                        'current'  => $data['balance']
                    ], 'ar'),
                    'en' => __('admin.deposit_balance', [
                        'previous' => 0,
                        'current'  => $data['balance']
                    ], 'en'),
                ],
            ]);
            $message = [
                'en' => __('admin.balance_deposited_notification', [], 'en'),

                'ar' => __('admin.balance_deposited_notification', [], 'ar'),
            ];

            auth('admin')->user()->notifications()->create([
                'id'               => (string) Str::uuid(),
                'type'             => NotificationTypeEnum::BALANCEDEPOSITED->value,
                'data'             => $message,
                'reciverable_type' => User::class,
                'reciverable_id'   => $user->id,
            ]);
        }
        return $user;
    }

    public function update($request, User $user)
    {
        $data = $request->validated();

        if (!isset($data['password']) || $data['password'] === null || $data['password'] === '') {
            unset($data['password']);
        }
        $data['name'] = $this->translate($data['name_ar'], $data['name_en']);
        $user->update($data);
        if (isset($data['shipping_prices']) && is_array($data['shipping_prices'])) {
            $this->updateShippingPrices($user, $data['shipping_prices']);
        }
        if (isset($data['balance']) && $data['balance'] !== null && $data['balance'] !== $user->wallet->balance) {
            $oldBalance = $user->wallet->balance;
            $user->wallet->update([
                'balance' => $data['balance'],
            ]);
            $user->walletLogs()->create([
                'user_id' => $user->id,
                'amount' => $data['balance'] > $oldBalance ? $data['balance'] - $oldBalance : $oldBalance - $data['balance'],
                'type' => $data['balance'] > $oldBalance ? 'deposit' : 'deduct',
                'trans_type' => 'edit_balance',
                'admin_id' => auth('admin')->id(),
                'description' => [
                    'ar' => __('admin.deposit_balance', [
                        'previous' => $oldBalance,
                        'current'  => $data['balance']
                    ], 'ar'),
                    'en' => __('admin.deposit_balance', [
                        'previous' => $oldBalance,
                        'current'  => $data['balance']
                    ], 'en'),
                ],
            ]);
            if ($data['balance'] > $oldBalance) {
                $message = [
                    'en' => __('admin.balance_deposited_notification', [], 'en'),

                    'ar' => __('admin.balance_deposited_notification', [], 'ar'),
                ];

                auth('admin')->user()->notifications()->create([
                    'id'               => (string) Str::uuid(),
                    'type'             => NotificationTypeEnum::BALANCEDEPOSITED->value,
                    'data'             => $message,
                    'reciverable_type' => User::class,
                    'reciverable_id'   => $user->id,
                ]);
            } else {
                $message = [
                    'en' => __('admin.balance_deducted_notification', [], 'en'),

                    'ar' => __('admin.balance_deducted_notification', [], 'ar'),
                ];

                auth('admin')->user()->notifications()->create([
                    'id'               => (string) Str::uuid(),
                    'type'             => NotificationTypeEnum::BALANCEDEDUCTION->value,
                    'data'             => $message,
                    'reciverable_type' => User::class,
                    'reciverable_id'   => $user->id,
                ]);
            }
        }
        return $user;
    }
    protected function updateShippingPrices(User $user, array $rows): void
    {
        $seen = [];

        foreach ($rows as $row) {
            $companyId = (string) ($row['id'] ?? '');
            if ($companyId === '') {
                continue;
            }
            $seen[] = $companyId;

            $attrs = [
                'company_name'         => $row['name'] ?? null,
                'local_price'          => isset($row['localprice']) && $row['localprice'] !== '' ? $row['localprice'] : null,
                'international_price'  => isset($row['internationalprice']) && $row['internationalprice'] !== '' ? $row['internationalprice'] : null,
            ];

            $user->shippingPrices()->updateOrCreate(
                ['company_id' => $companyId],
                $attrs
            );
        }
    }

    function updateUserStatus($user)
    {
        if ($user->status->value == 'active') {
            $user->update([
                'status' => 'deactive',
            ]);
        } else {
            $user->update([
                'status' => 'active',
            ]);
        }
    }



    public function delete(User $user)
    {
        $user->delete();
        return true;
    }

    public function walletLogs($request, User $user)
    {
        $request->validated();
        $logs = app(Pipeline::class)
            ->send(WalletLog::query())
            ->through([
                WalletLogTypeFilter::class,
                TransActionFilter::class,
                DateFromFilter::class,
                DateToFilter::class
            ])
            ->thenReturn()
            ->where('user_id', $user->id)
            ->withAllRelations()
            ->orderBy('id', 'desc')
            ->paginate()
            ->withQueryString();
        return $logs;
    }
}
