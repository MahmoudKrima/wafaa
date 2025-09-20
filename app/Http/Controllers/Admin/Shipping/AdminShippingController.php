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

    // Controller
    public function index(SearchShippingRequest $request, ?User $user = null)
    {
        $page    = max(1, (int) $request->input('page', 1));
        $perPage = (int) $request->input('pageSize', 10);

        $filters = $request->validated();

        if ($request->filled('isCod')) {
            $filters['isCod'] = $request->input('isCod') === 'true' ? 'true' : 'false';
        }

        // Build user filter: only when explicitly specified (no default to all users)
        $users = [];
        if ($user) {
            $check = User::where('created_by', getAdminIdOrCreatedBy())
                ->where('id', $user->id)
                ->first();
            if (!$check) {
                return back()->with('Error', __('admin.user_not_found'));
            }
            $users = [(string) $user->id];
            unset($filters['userId']);
        } elseif (!empty($filters['userId'])) {
            $ids   = is_array($filters['userId']) ? $filters['userId'] : [$filters['userId']];
            $users = array_values(array_map('strval', $ids));
            unset($filters['userId']);
        }
        // else: leave $users empty → do NOT send externalAppId; GHAYA key separation handles scope

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

    public function delete(string $id, $externalAppId)
    {
        $data = $this->adminShippingService->delete($id, $externalAppId);
        if ($data == 'canceled') {
            return back()
                ->with('Success', __('admin.canceled_successfully'));
        } else {
            return back()
                ->with('Error', __('admin.canceled_failed'));
        }
    }
}
