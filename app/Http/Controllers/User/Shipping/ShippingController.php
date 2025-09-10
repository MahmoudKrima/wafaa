<?php

namespace App\Http\Controllers\User\Shipping;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User\Shipping\ShippingService;
use App\Http\Requests\User\Shipping\StoreShippingRequest;
use App\Http\Requests\User\Shipping\SearchShippingRequest;
use Illuminate\Pagination\LengthAwarePaginator;

class ShippingController extends Controller
{
    public function __construct(private ShippingService $shippingService) {}


    public function index(SearchShippingRequest $request)
    {
        $page    = max(1, (int) $request->input('page', 1));
        $perPage = (int) $request->input('pageSize', 10);

        $filters = $request->validated();
        if ($request->has('isCod')) {
            $bool = filter_var($request->input('isCod'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            $bool = $bool === null ? false : $bool;
            $filters['isCod'] = $bool ? 'true' : 'false';
        }

        $hasReceiverFilters = filled($filters['receiverName'] ?? null) || filled($filters['receiverPhone'] ?? null);

        if ($hasReceiverFilters) {
            $filters['page']     = 0;
            $filters['pageSize'] = max($perPage, 200);
        } else {
            $filters['page']     = $page - 1;
            $filters['pageSize'] = $perPage;
        }

        $data = $this->shippingService->getUserListShipments($filters);

        if ($hasReceiverFilters) {
            $results = collect($data['results'] ?? []);

            $receiverName  = $request->input('receiverName');
            $receiverPhone = $request->input('receiverPhone');

            if ($receiverName || $receiverPhone) {
                $results = $results->filter(function ($shipment) use ($receiverName, $receiverPhone) {
                    $details = data_get($shipment, 'shipmentDetails', []);
                    $name  = (string) data_get($details, 'receiverName', '');
                    $phone = (string) data_get($details, 'receiverPhone', '');
                    $ok = true;
                    if ($receiverName) {
                        $ok = $ok && (mb_stripos($name, $receiverName) !== false);
                    }
                    if ($receiverPhone) {
                        $ok = $ok && ($phone === $receiverPhone);
                    }
                    return $ok;
                })->values();
            }

            $total     = $results->count();
            $offset    = ($page - 1) * $perPage;
            $pageItems = $results->slice($offset, $perPage)->values();
        } else {
            $pageItems = collect($data['results'] ?? []);
            $total = (int) (
                $data['total']
                ?? $data['count']
                ?? data_get($data, 'pagination.total', 0)
            );
        }

        $shipments = new LengthAwarePaginator(
            $pageItems,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $companies = $this->shippingService->getShippingCompanies();

        return view('user.pages.shippings.index', compact('shipments', 'companies'));
    }


    public function export(SearchShippingRequest $request)
    {
        return $this->shippingService->export($request);
    }

    public function create()
    {
        return view('user.pages.shippings.create');
    }

    public function store(StoreShippingRequest $request)
    {
        return $this->shippingService->store($request);
    }

    public function show(string $id)
    {
        $data = $this->shippingService->show($id);
        return view('user.pages.shippings.show', $data);
    }

    public function delete(string $id)
    {
        $data = $this->shippingService->delete($id);
        return view('user.pages.shippings.show', $data);
    }


    public function receivers()
    {
        $recievers = $this->shippingService->receivers();
        return response()->json($recievers);
    }

    public function shippingCompanies()
    {
        $companies = $this->shippingService->getUserShippingCompanies();
        return response()->json($companies);
    }


    public function getStates()
    {
        $states = $this->shippingService->getStates();
        return response()->json($states);
    }

    public function getCities()
    {
        $cities = $this->shippingService->getCities();
        return response()->json($cities);
    }

    public function getCitiesByState(Request $request)
    {
        $stateId = $request->get('state_id');
        $cities = $this->shippingService->getCitiesByState($stateId);
        return response()->json($cities);
    }
    public function walletBalance()
    {
        $user = auth()->user();
        $wallet = $user->wallet;
        return response()->json([
            'balance' => $wallet ? $wallet->balance : 0
        ]);
    }
}
