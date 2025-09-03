<?php

namespace App\Http\Controllers\User\Home;

use App\Models\Reciever;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $walletBalance = (float) (optional($user->wallet)->balance ?? 0);
        $receiversCount = Reciever::where('user_id', $user->id)->count();
        $stats = $this->dashboardStats();
        return view('user.pages.home.index', compact(
            'walletBalance',
            'receiversCount',
            'stats'
        ));
    }


    private function dashboardStats(array $filters = []): array
    {
        $methodCounts = ['local' => 0, 'international' => 0];
        $statusCounts = [
            'pending'       => 0,
            'processing'    => 0,
            'failed'        => 0,
            'cancelRequest' => 0,
            'canceled'      => 0,
            'other'         => 0,
        ];

        $total = 0;
        $page     = 0;
        $pageSize = 200;

        do {
            $pageFilters = array_merge($filters, [
                'page'     => $page,
                'pageSize' => $pageSize,
            ]);

            $chunk   = $this->getUserListShipments($pageFilters);
            $results = $chunk['results'] ?? [];
            $grand   = (int) ($chunk['total'] ?? count($results));

            foreach ($results as $row) {
                $total++;
                $m = strtolower((string)($row['method'] ?? ''));
                if (isset($methodCounts[$m])) {
                    $methodCounts[$m]++;
                }
                $s = (string)($row['status'] ?? '');
                if (isset($statusCounts[$s])) {
                    $statusCounts[$s]++;
                } else {
                    $statusCounts['other']++;
                }
            }

            $page++;
            $fetched = $page * $pageSize;
        } while ($fetched < $grand && !empty($results));

        return [
            'total'   => $total,
            'methods' => $methodCounts,
            'status'  => $statusCounts,
        ];
    }

    private function getUserListShipments(array $filters = [])
    {
        $base = [
            'page'           => 0,
            'pageSize'       => 15,
            'orderColumn'    => 'createdAt',
            'orderDirection' => 'desc',
            'externalAppId'  => (string) auth()->id(),
        ];

        $clean = array_filter($filters, function ($v) {
            return !is_null($v) && $v !== '';
        });

        $query = array_merge($base, $clean);

        $res = $this->ghayaRequest()
            ->get($this->ghayaUrl('shipments'), $query);

        return $res->json();
    }

    private function ghayaRequest()
    {
        return Http::withHeaders([
            'accept'    => '*/*',
            'x-api-key' => config('services.ghaya.key'),
        ]);
    }

    private function ghayaUrl(string $endpoint): string
    {
        return rtrim(config('services.ghaya.base_url'), '/') . '/' . ltrim($endpoint, '/');
    }
}
