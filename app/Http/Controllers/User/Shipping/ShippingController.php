<?php

namespace App\Http\Controllers\User\Shipping;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Transaction\SearchTransactionRequest;
use App\Http\Requests\User\Transaction\StoreTransactionRequest;
use App\Services\User\Shipping\ShippingService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function __construct(private ShippingService $shippingService) {}

    public function index(SearchTransactionRequest $request)
    {
        // $result = $this->shippingService->index($request);
        return view('user.pages.shippings.create');
    }

    public function create()
    {
        return view('user.pages.shippings.create');
    }

    public function store(StoreTransactionRequest $request)
    {
        $this->shippingService->store($request);
        return back()
            ->with('Success', __('admin.created_successfully'));
    }
}
