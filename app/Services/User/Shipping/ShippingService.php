<?php

namespace App\Services\User\Shipping;

use App\Models\Banks;
use App\Traits\ImageTrait;
use App\Filters\CodeFilter;
use App\Models\Shipping;
use Illuminate\Pipeline\Pipeline;
use App\Enum\TransactionStatusEnum;
use App\Filters\ActivationStatusFilter;

class ShippingService
{
    use ImageTrait;

    private function banksHasTransactions()
    {
        return Banks::where('admin_id', auth()->user()->created_by)
            ->whereHas('shippings', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->Active()
            ->get();
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

    

    public function store($request)
    {
        
    }
}
