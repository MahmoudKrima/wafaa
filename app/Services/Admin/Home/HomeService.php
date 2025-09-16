<?php

namespace App\Services\Admin\Home;

use App\Models\User;
use App\Models\Slider;
use App\Models\Contact;
use App\Models\Partner;
use App\Models\Service;
use App\Models\Reciever;
use App\Models\Testimonial;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class HomeService
{
    private const EXTERNAL_IDS_CHUNK_SIZE = 150;
    private const PAGE_SIZE = 200;

    public function dashboardStats(array $filters = []): array
    {
        $adminId = getAdminIdOrCreatedBy();

        $userIds = User::query()
            ->where(function ($q) use ($adminId) {
                $q->where('created_by', $adminId)
                    ->orWhere('added_by', $adminId);
            })
            ->pluck('id')
            ->unique()
            ->map(fn($id) => (string) $id)
            ->values()
            ->all();

        $methodCounts = ['local' => 0, 'international' => 0];
        $statusCounts = [
            'pending' => 0,
            'processing' => 0,
            'delivered' => 0,
            'returned' => 0,
            'failed' => 0,
            'cancelRequest' => 0,
            'canceled' => 0,
            'other' => 0,
        ];
        $grandTotal = 0;

        if (empty($userIds)) {
            return [
                'total' => 0,
                'methods' => $methodCounts,
                'status' => $statusCounts,
            ];
        }

        $filters = $this->cleanFilters($filters);

        foreach (array_chunk($userIds, self::EXTERNAL_IDS_CHUNK_SIZE) as $idsChunk) {
            $chunkTotal = $this->getChunkTotal($idsChunk, $filters);
            if ($chunkTotal <= 0) {
                continue;
            }
            $grandTotal += $chunkTotal;

            $page = 0;
            do {
                $pageFilters = array_merge($filters, [
                    'page' => $page,
                    'pageSize' => self::PAGE_SIZE,
                ]);

                $chunkData = $this->fetchShipmentsChunk($idsChunk, $pageFilters);
                $results = (array) ($chunkData['results'] ?? []);

                foreach ($results as $row) {
                    $m = strtolower((string) ($row['method'] ?? ''));
                    if (isset($methodCounts[$m])) {
                        $methodCounts[$m]++;
                    }

                    $s = (string) ($row['status'] ?? '');
                    if (isset($statusCounts[$s])) {
                        $statusCounts[$s]++;
                    } else {
                        $statusCounts['other']++;
                    }
                }

                $page++;
                $fetched = $page * self::PAGE_SIZE;
            } while ($fetched < $chunkTotal && !empty($results));
        }

        return [
            'total' => $grandTotal,
            'methods' => $methodCounts,
            'status' => $statusCounts,
        ];
    }


    private function getChunkTotal(array $externalIds, array $filters = []): int
    {
        $params = array_merge([
            'page' => 0,
            'pageSize' => 1,   // only need total
            'orderColumn' => 'createdAt',
            'orderDirection' => 'desc',
        ], $filters);

        $url = $this->buildShipmentsUrl($params, $externalIds);

        try {
            $res = $this->ghayaRequest()->get($url);
            $json = $res->json();
            return (int) ($json['total'] ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }


    private function fetchShipmentsChunk(array $externalIds, array $filters = []): array
    {
        $params = array_merge([
            'page' => 0,
            'pageSize' => self::PAGE_SIZE,
            'orderColumn' => 'createdAt',
            'orderDirection' => 'desc',
        ], $filters);

        $url = $this->buildShipmentsUrl($params, $externalIds);

        $res = $this->ghayaRequest()->get($url);
        return (array) $res->json();
    }


    private function buildShipmentsUrl(array $params, array $externalIds): string
    {
        $parts = [];
        foreach ($params as $k => $v) {
            $parts[] = urlencode($k) . '=' . urlencode((string) $v);
        }
        foreach ($externalIds as $id) {
            $parts[] = 'externalAppId=' . urlencode((string) $id);
        }
        return $this->ghayaUrl('shipments') . '?' . implode('&', $parts);
    }


    private function cleanFilters(array $filters): array
    {
        $clean = array_filter($filters, fn($v) => !is_null($v) && $v !== '');
        unset($clean['receiverName'], $clean['receiverPhone'], $clean['userId']);
        return $clean;
    }

    private function ghayaRequest()
    {
        return Http::withHeaders([
            'accept' => '*/*',
            'x-api-key' => config('services.ghaya.key'),
        ]);
    }

    private function ghayaUrl(string $endpoint): string
    {
        return rtrim(config('services.ghaya.base_url'), '/') . '/' . ltrim($endpoint, '/');
    }

    public function usersStatistics()
    {

        $adminId = getAdminIdOrCreatedBy();

        $stats = User::query()
            ->where(function ($q) use ($adminId) {
                $q->where('created_by', $adminId)
                    ->orWhere('added_by', $adminId);
            })
            ->selectRaw('COUNT(*) AS usersCount')
            ->selectRaw("SUM(CASE WHEN status = 'active'   THEN 1 ELSE 0 END) AS activeUsersCount")
            ->selectRaw("SUM(CASE WHEN status = 'deactive' THEN 1 ELSE 0 END) AS inactiveUsersCount")
            ->first();
        return [
            'usersCount' => (int) $stats->usersCount,
            'activeUsersCount' => (int) $stats->activeUsersCount,
            'inactiveUsersCount' => (int) $stats->inactiveUsersCount,
        ];
    }

    public function transactionsStatistics()
    {

        $adminId = getAdminIdOrCreatedBy();

        $stats = Transaction::query()
            ->whereHas('user', function ($q) use ($adminId) {
                $q->where(function ($q) use ($adminId) {
                    $q->where('created_by', $adminId)
                        ->orWhere('added_by', $adminId);
                });
            })
            ->selectRaw('COUNT(*) AS transactionsCount')
            ->selectRaw("SUM(CASE WHEN status = 'pending'   THEN 1 ELSE 0 END) AS pendingTransactionsCount")
            ->selectRaw("SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) AS acceptedTransactionsCount")
            ->selectRaw("SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejectedTransactionsCount")
            ->first();
        return [
            'transactionsCount' => (int) $stats->transactionsCount,
            'pendingTransactionsCount' => (int) $stats->pendingTransactionsCount,
            'acceptedTransactionsCount' => (int) $stats->acceptedTransactionsCount,
            'rejectedTransactionsCount' => (int) $stats->rejectedTransactionsCount,
        ];
    }

    public function messagesStatistics()
    {
        $stats = Contact::query()
            ->selectRaw('COUNT(*) AS messagesCount')
            ->selectRaw("SUM(CASE WHEN status = 'pending'   THEN 1 ELSE 0 END) AS pendingMessagesCount")
            ->selectRaw("SUM(CASE WHEN status = 'replied' THEN 1 ELSE 0 END) AS repliedMessagesCount")
            ->first();
        return [
            'messagesCount' => (int) $stats->messagesCount,
            'pendingMessagesCount' => (int) $stats->pendingMessagesCount,
            'repliedMessagesCount' => (int) $stats->repliedMessagesCount,
        ];
    }

    public function globalStatistics()
    {
        $slidersCount = Slider::count();
        $partnersCount = Partner::count();
        $servicesCount = Service::count();
        $testimonialsCount = Testimonial::count();
        return [
            'slidersCount' => $slidersCount,
            'partnersCount' => $partnersCount,
            'servicesCount' => $servicesCount,
            'testimonialsCount' => $testimonialsCount,
        ];
    }
}
