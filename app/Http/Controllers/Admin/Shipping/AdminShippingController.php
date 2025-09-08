<?php

namespace App\Http\Controllers\Admin\Shipping;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\Admin\Shipping\AdminShippingService;
use App\Http\Requests\Admin\Shipping\SearchShippingRequest;
use Illuminate\Pagination\Paginator;

class AdminShippingController extends Controller
{
    public function __construct(private AdminShippingService $adminShippingService) {}

    public function index(SearchShippingRequest $request, ?User $user = null)
    {
        if (!$user || $user->created_by != getAdminIdOrCreatedBy()) {
            return back()
                ->with('Error', __('admin.not_found_data'));
        }
        $page    = max(1, (int) $request->input('page', 1));
        $perPage = (int) $request->input('pageSize', 10);

        $filters = $request->validated();

        if ($request->filled('isCod')) {
            $filters['isCod'] = $request->input('isCod') === 'true' ? 'true' : 'false';
        }

        if ($user) {
            $users = [(string) $user->id];
            unset($filters['userId']);
        } elseif (!empty($filters['userId'])) {
            $ids   = is_array($filters['userId']) ? $filters['userId'] : [$filters['userId']];
            $users = array_values(array_map('strval', $ids));
            unset($filters['userId']);
        } else {
            $users = User::where('created_by', getAdminIdOrCreatedBy())
                ->pluck('id')->map(fn($id) => (string) $id)->values()->all();
        }

        $hasReceiverFilters = filled($filters['receiverName'] ?? null) || filled($filters['receiverPhone'] ?? null);

        if ($hasReceiverFilters) {
            $filters = array_merge($filters, [
                'page'     => 0,
                'pageSize' => max($perPage, 200),
            ]);
        } else {
            $filters = array_merge($filters, [
                'page'     => $page - 1,
                'pageSize' => $perPage,
            ]);
        }

        $data    = $this->adminShippingService->getUserListShipments($filters, $users);
        $results = collect($data['results'] ?? []);

        $receiverName  = (string) $request->input('receiverName', '');
        $receiverPhone = (string) $request->input('receiverPhone', '');

        if ($receiverName || $receiverPhone) {
            $results = $results->filter(function ($shipment) use ($receiverName, $receiverPhone) {
                $details = data_get($shipment, 'shipmentDetails', []);
                $name    = (string) data_get($details, 'receiverName', '');
                $phone   = (string) data_get($details, 'receiverPhone', '');
                $ok      = true;
                if ($receiverName)  $ok = $ok && (mb_stripos($name, $receiverName) !== false);
                if ($receiverPhone) $ok = $ok && ($phone === $receiverPhone);
                return $ok;
            })->values();

            $total     = $results->count();
            $offset    = ($page - 1) * $perPage;
            $pageItems = $results->slice($offset, $perPage)->values();

            $shipments = new LengthAwarePaginator(
                $pageItems,
                $total,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $apiTotal = (int) (
                data_get($data, 'total') ??
                data_get($data, 'pagination.total') ??
                data_get($data, 'totalCount') ??
                data_get($data, 'count') ??
                0
            );

            if ($apiTotal > 0) {
                $shipments = new LengthAwarePaginator(
                    $results,
                    $apiTotal,
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            } else {
                $shipments = new Paginator(
                    $results,
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'pageName' => 'page']
                );
            }
        }

        $companies    = $this->adminShippingService->getShippingCompanies();
        $allUsers     = User::where('created_by', getAdminIdOrCreatedBy())->get();
        $forcedUserId = $user?->id;

        return view('dashboard.pages.admin_shipping.index', compact(
            'shipments',
            'companies',
            'allUsers',
            'forcedUserId'
        ));
    }

    public function export(SearchShippingRequest $request)
    {
        return $this->adminShippingService->export($request);
    }

    public function show(string $id)
    {
        $data = $this->adminShippingService->show($id);
        return view('dashboard.pages.admin_shipping.show', $data);
    }
}
