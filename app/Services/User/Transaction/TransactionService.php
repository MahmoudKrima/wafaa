<?php

namespace App\Services\User\Transaction;

use App\Models\Banks;
use App\Traits\ImageTrait;
use App\Filters\CodeFilter;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Pipeline\Pipeline;
use App\Enum\TransactionStatusEnum;
use App\Filters\ActivationStatusFilter;

class TransactionService
{
    use ImageTrait;

    private function banksHasTransactions()
    {
        return Banks::where('admin_id', auth()->user()->created_by)
            ->whereHas('transactions', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->Active()
            ->get();
    }

    public function index($request)
    {
        $request->validated();
        $banks = $this->banksHasTransactions();
        $transactions = app(Pipeline::class)
            ->send(Transaction::query())
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
        $status = TransactionStatusEnum::cases();
        return compact('banks', 'transactions', 'status');
    }

    public function allActiveBanks()
    {
        return Banks::where('admin_id', auth()->user()->created_by)
            ->Active()
            ->get();
    }

    public function store($request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['status'] = 'pending';
        $data['attachment'] = $this->uploadImage($request->file('attachment'), 'transactions');
        do {
            $data['code'] = strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        } while (Transaction::where('code', $data['code'])->exists());
        Transaction::create($data);
    }
}
