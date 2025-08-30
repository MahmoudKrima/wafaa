<?php

namespace App\Services\Admin\UserSettings;

use App\Models\User;
use App\Filters\CityFilter;
use App\Filters\EmailFilter;
use App\Filters\PhoneFilter;
use Illuminate\Http\Request;
use App\Models\AllowedCompany;
use App\Traits\TranslateTrait;
use App\Filters\NameJsonFilter;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

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

    public function allowedCompanies()
    {
        $adminId = getAdminIdOrCreatedBy();
        $ids = AllowedCompany::where('admin_id', $adminId)
            ->where('status', 'active')
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
            ])
            ->thenReturn()
            ->withAllRelations()
            ->where('created_by', getAdminIdOrCreatedBy())
            ->orderBy('id', 'desc')
            ->paginate()
            ->withQueryString();
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
        return $user;
    }

    public function update($request, User $user)
    {
        $data = $request->validated();
        if (!isset($data['password'])) {
            unset($data['password']);
        }
        $data['name'] = $this->translate($data['name_ar'], $data['name_en']);
        $user->update($data);

        // // Handle shipping prices if provided
        // if (isset($data['shipping_prices']) && is_array($data['shipping_prices'])) {
        //     $this->updateShippingPrices($user, $data['shipping_prices']);
        // }

        return $user;
    }

    public function delete(User $user)
    {
        $user->delete();
        return true;
    }

    public function walletLogs(User $user)
    {
        return $user->walletLogs()->orderBy('id', 'desc')->paginate();
    }
}
