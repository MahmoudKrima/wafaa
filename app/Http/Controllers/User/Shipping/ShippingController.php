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

        if ($request->filled('isCod')) {
            $filters['isCod'] = $request->input('isCod') === 'true' ? 'true' : 'false';
        }

        $filters = array_merge($filters, [
            'page'     => $page - 1,
            'pageSize' => $perPage,
        ]);

        $data = $this->shippingService->getUserListShipments($filters);

        $results = collect($data['results'] ?? []);
        $total   = $data['total'] ?? $results->count();

        $shipments = new LengthAwarePaginator(
            $results,
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
        $this->shippingService->store($request);

        return redirect()
            ->route('user.shippings.index')
            ->with('Success', __('admin.shippment_created_successfully'));
    }

    public function show(string $id)
    {
        $data = $this->shippingService->show($id);
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
