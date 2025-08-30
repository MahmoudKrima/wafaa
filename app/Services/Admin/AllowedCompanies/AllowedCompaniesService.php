<?php

namespace App\Services\Admin\AllowedCompanies;

use App\Traits\ImageTrait;
use App\Traits\TranslateTrait;
use App\Models\AllowedCompany;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class AllowedCompaniesService
{
    use ImageTrait, TranslateTrait;

    public function getAll()
    {
        $adminId = getAdminIdOrCreatedBy();
        $apiPayload = $this->getShippingCompanies();
        $shippingCompanies = collect(data_get($apiPayload, 'results', []))
            ->filter(fn($c) => (bool) data_get($c, 'isActive', false) === true)
            ->values()
            ->all();
        $existingIds = AllowedCompany::where('admin_id', $adminId)
            ->pluck('company_id')
            ->all();

        $toInsert = [];
        foreach ($shippingCompanies as $company) {
            $companyId = (string) data_get($company, 'id');
            if (!$companyId) {
                continue;
            }
            if (!in_array($companyId, $existingIds, true)) {
                $name = data_get($company, 'name');
                $toInsert[] = [
                    'company_id'   => $companyId,
                    'company_name' => $name,
                    'status'       => 'deactive',
                    'image'        => data_get($company, 'logoUrl'),
                    'admin_id'     => $adminId,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
        }

        if (!empty($toInsert)) {
            DB::transaction(fn() => AllowedCompany::insert($toInsert));
        }

        return AllowedCompany::where('admin_id', $adminId)
            ->orderByDesc('id')
            ->paginate();
    }



    public function getShippingCompanies(): array
    {
        try {
            $response = Http::withHeaders([
                'accept'    => '*/*',
                'x-api-key' => env('GHAYA_API_KEY', 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'),
            ])->get('https://ghaya-express-staging-af597af07557.herokuapp.com/api/shipping-companies');

            return $response->successful() ? $response->json() : [];
        } catch (\Throwable $e) {
            return [];
        }
    }


    function updateAllowedCompanyStatus($allowedCompany)
    {
        if ($allowedCompany->status->value == 'active') {
            $allowedCompany->update([
                'status' => 'deactive',
            ]);
        } else {
            $allowedCompany->update([
                'status' => 'active',
            ]);
        }
    }
}
