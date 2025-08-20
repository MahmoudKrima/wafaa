<?php

namespace App\Http\Controllers\Admin\UserShippingPrice;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admin\UserShippingPrice\UserShippingPriceService;
use App\Http\Requests\Admin\UserShippingPrice\StoreUserShippingPriceRequest;
use App\Http\Requests\Admin\UserShippingPrice\UpdateUserShippingPriceRequest;
use Illuminate\Http\Request;

class UserShippingPriceController extends Controller
{
    protected $userShippingPriceService;

    public function __construct(UserShippingPriceService $userShippingPriceService)
    {
        $this->userShippingPriceService = $userShippingPriceService;
    }

    public function index(User $user)
    {
        $userShippingPrices = $this->userShippingPriceService->getAll($user);
        
        return view('dashboard.pages.shipping_prices.index', compact('user', 'userShippingPrices'));
    }

    public function create(User $user)
    {
        return view('dashboard.pages.shipping_prices.create', compact('user'));
    }

    public function store(StoreUserShippingPriceRequest $request, User $user)
    {        
        $this->userShippingPriceService->storeUserShippingPrice($request, $user);
        
        return redirect()->route('admin.user-shipping-prices.index', $user->id)
            ->with('Success', __('admin.created_successfully'));
    }

    public function edit($userId, $id)
    {
        $user = User::findOrFail($userId);
        $userShippingPrice = $this->userShippingPriceService->findById($id);
        
        return view('dashboard.pages.users.shipping_prices.edit', compact('user', 'userShippingPrice'));
    }

    public function update(UpdateUserShippingPriceRequest $request, $userId, $id)
    {
        $userShippingPrice = $this->userShippingPriceService->findById($id);
        $this->userShippingPriceService->updateUserShippingPrice($userShippingPrice, $request->validated());
        
        return redirect()->route('admin.user-shipping-prices.index', $userId)
            ->with('success', __('admin.updated_successfully'));
    }

    public function updateStatus(Request $request, $userId, $id)
    {
        $userShippingPrice = $this->userShippingPriceService->findById($id);
        $this->userShippingPriceService->updateUserShippingPriceStatus($userShippingPrice, $request->status);
        
        return response()->json(['success' => true]);
    }

    public function destroy($userId, $id)
    {
        $userShippingPrice = $this->userShippingPriceService->findById($id);
        $this->userShippingPriceService->deleteUserShippingPrice($userShippingPrice);
        
        return redirect()->route('admin.user-shipping-prices.index', $userId)
            ->with('success', __('admin.deleted_successfully'));
    }
}
