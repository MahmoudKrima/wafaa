<?php

namespace App\Http\Controllers\Admin\UserShippingPrice;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserShippingPrice;
use App\Http\Controllers\Controller;
use App\Services\Admin\UserShippingPrice\UserShippingPriceService;
use App\Http\Requests\Admin\UserShippingPrice\StoreUserShippingPriceRequest;
use App\Http\Requests\Admin\UserShippingPrice\UpdateUserShippingPriceRequest;

class UserShippingPriceController extends Controller
{
    protected $userShippingPriceService;

    public function __construct(UserShippingPriceService $userShippingPriceService)
    {
        $this->userShippingPriceService = $userShippingPriceService;
    }

    public function index(User $user)
    {
        if (!$user || $user->created_by != getAdminIdOrCreatedBy()) {
            return back()
                ->with('Error', __('admin.not_found_data'));
        }
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

    public function edit(User $user, UserShippingPrice $userShippingPrice)
    {
        if (!$user || $user->created_by != getAdminIdOrCreatedBy()) {
            return back()
                ->with('Error', __('admin.not_found_data'));
        }
        return view('dashboard.pages.shipping_prices.edit', compact('user', 'userShippingPrice'));
    }

    public function update(UpdateUserShippingPriceRequest $request, User $user, UserShippingPrice $userShippingPrice)
    {
        if (!$user || $user->created_by != getAdminIdOrCreatedBy()) {
            return back()
                ->with('Error', __('admin.not_found_data'));
        }
        $this->userShippingPriceService->updateUserShippingPrice($request, $userShippingPrice);

        return redirect()->route('admin.user-shipping-prices.index', $user->id)
            ->with('Success', __('admin.updated_successfully'));
    }

    public function delete(UserShippingPrice $userShippingPrice)
    {
        if (!$userShippingPrice || $userShippingPrice->user->created_by != getAdminIdOrCreatedBy()) {
            return back()
                ->with('Error', __('admin.not_found_data'));
        }
        $this->userShippingPriceService->deleteUserShippingPrice($userShippingPrice);
        return redirect()->route('admin.user-shipping-prices.index', $userShippingPrice->user->id)
            ->with('Success', __('admin.deleted_successfully'));
    }
}
