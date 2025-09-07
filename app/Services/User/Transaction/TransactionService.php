<?php

namespace App\Services\User\Transaction;

use App\Models\Admin;
use App\Models\Banks;
use App\Traits\ImageTrait;
use App\Filters\CodeFilter;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Filters\DateToFilter;
use App\Filters\DateFromFilter;
use Illuminate\Pipeline\Pipeline;
use App\Enum\NotificationTypeEnum;
use App\Enum\TransactionStatusEnum;
use App\Filters\ActivationStatusFilter;
use App\Filters\BankFilter;

class TransactionService
{
    use ImageTrait;

    private function banksHasTransactions()
    {
        return Banks::where('admin_id', auth()->user()->created_by)
            ->whereHas('transactions', function ($query) {
                $query->where('user_id', auth()->id());
            })
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
                CodeFilter::class,
                DateFromFilter::class,
                DateToFilter::class,
                BankFilter::class,

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
        $transaction = Transaction::create($data);
        $message = [
            'en' => __('admin.transaction_created_notification', [
                'code'   => $transaction->code,
                'status' => __("admin.{$transaction->status->value}", [], 'en'),
            ], 'en'),

            'ar' => __('admin.transaction_created_notification', [
                'code'   => $transaction->code,
                'status' => __("admin.{$transaction->status->value}", [], 'ar'),
            ], 'ar'),
        ];


        auth()->user()->notifications()->create([
            'id'               => (string) Str::uuid(),
            'type'             => NotificationTypeEnum::TRANSACTION_CREATED->value,
            'data'             => $message,
            'reciverable_type' => Admin::class,
            'reciverable_id'   => auth()->user()->created_by,
        ]);
    }
}
