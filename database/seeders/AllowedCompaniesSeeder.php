<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Models\AllowedCompany;
use App\Services\Admin\AllowedCompanies\AllowedCompaniesService;

class AllowedCompaniesSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(AllowedCompaniesService::class);

        $payload = $service->getShippingCompanies();
        $companies = collect(Arr::get($payload, 'results', []))
            ->filter(fn($c) => (bool) data_get($c, 'isActive', false) === true)
            ->values();

        if ($companies->isEmpty()) {
            return;
        }

        $now = now();
        $admins = [1, 2];

        $rows = [];

        foreach ($admins as $adminId) {
            foreach ($companies as $company) {
                $companyId = (string) data_get($company, 'id');
                if (!$companyId) {
                    continue;
                }

                $rows[] = [
                    'company_id'   => $companyId,
                    'company_name' => (string) data_get($company, 'name', ''),
                    'status'       => 'deactive',
                    'image'        => (string) data_get($company, 'logoUrl', ''),
                    'admin_id'     => $adminId,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }
        }

        DB::transaction(function () use ($rows) {
            AllowedCompany::upsert(
                $rows,
                ['admin_id', 'company_id'],
                ['company_name', 'status', 'image', 'updated_at']
            );
        });
    }
}
