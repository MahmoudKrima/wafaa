<?php

namespace App\Http\Controllers\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Shipping\StoreAdminShippingRequest;
use App\Services\Admin\Shipping\AdminShippingService;
use App\Models\User;
use Illuminate\Http\Request;

class AdminShippingController extends Controller
{
    public function __construct(private AdminShippingService $adminShippingService) {}

    public function index()
    {
        $users = User::where('created_by', getAdminIdOrCreatedBy())
            ->withAllRelations()
            ->orderBy('name')
            ->get();
        return view('dashboard.pages.admin_shipping.index', compact('users'));
    }

    public function create()
    {
        $users = User::where('created_by', getAdminIdOrCreatedBy())
            ->withAllRelations()
            ->orderBy('name')
            ->get();
        return view('dashboard.pages.admin_shipping.create', compact('users'));
    }

    public function store(StoreAdminShippingRequest $request)
    {
        $result = $this->adminShippingService->store($request);
        return back()
            ->with('Success', __('admin.created_successfully'));
    }

    public function getUserShippingCompanies(Request $request)
    {
        $userId = $request->get('user_id');
        if (!$userId) {
            return response()->json(['results' => []]);
        }
        
        $companies = $this->adminShippingService->getUserShippingCompanies($userId);
        return response()->json($companies);
    }

    public function getUserReceivers(Request $request)
    {
        $userId = $request->get('user_id');
        if (!$userId) {
            return response()->json([]);
        }
        
        $receivers = $this->adminShippingService->getUserReceivers($userId);
        return response()->json($receivers);
    }

    public function getUserWalletBalance(Request $request)
    {
        $userId = $request->get('user_id');
        if (!$userId) {
            return response()->json(['balance' => 0]);
        }
        
        $balance = $this->adminShippingService->getUserWalletBalance($userId);
        return response()->json(['balance' => $balance]);
    }

    public function getStates()
    {
        $states = $this->adminShippingService->getStates();
        return response()->json($states);
    }

    public function getCities()
    {
        $cities = $this->adminShippingService->getCities();
        return response()->json($cities);
    }

    public function getCitiesByState(Request $request)
    {
        $stateId = $request->get('state_id');
        $cities = $this->adminShippingService->getCitiesByState($stateId);
        return response()->json($cities);
    }

    public function getCountries()
    {
        $countries = $this->adminShippingService->getCountries();
        return response()->json($countries);
    }
}
