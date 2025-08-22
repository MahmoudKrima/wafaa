<?php

namespace App\Services\User\Shipping;

use App\Models\Banks;
use App\Models\Reciever;
use App\Models\Shipping;
use App\Traits\ImageTrait;
use App\Filters\CodeFilter;
use Illuminate\Pipeline\Pipeline;
use App\Filters\ActivationStatusFilter;

class ShippingService
{
    use ImageTrait;

    public function receivers()
    {
        $recievers = Reciever::where('user_id', auth()->user()->id)
            ->withAllRelations()
            ->get();
        return $recievers;
    }

    public function index($request)
    {
        $request->validated();
        $banks = $this->banksHasTransactions();
        $shippings = app(Pipeline::class)
            ->send(Shipping::query())
            ->through([
                ActivationStatusFilter::class,
                CodeFilter::class
            ])
            ->thenReturn()
            ->where('user_id', auth()->id())
            ->with('bank')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('id')
            ->paginate()
            ->withQueryString();
        $status = ShippingStatusEnum::cases();
        return compact('banks', 'shippings', 'status');
    }



    public function store($request) {}
}
