<?php

namespace App\Services\Admin\Transaction;

use App\Models\User;
use App\Models\Banks;
use App\Traits\ImageTrait;
use App\Filters\BankFilter;
use App\Filters\CodeFilter;
use App\Filters\UserFilter;
use App\Models\Transaction;
use App\Traits\TranslateTrait;
use App\Enum\TransactionTypeEnum;
use Illuminate\Pipeline\Pipeline;
use App\Filters\ActivationStatusFilter;
use App\Enum\TransactionStatusEnum;

class TransactionService
{
    use ImageTrait, TranslateTrait;

    function getAll()
    {
        return Transaction::withAllRelations()
            ->whereRelation('user', 'created_by', getAdminIdOrCreatedBy())
            ->orderBy('id', 'desc')
            ->paginate();
    }

    function getAllBanks()
    {
        return Banks::where('admin_id', getAdminIdOrCreatedBy())->get();
    }

    function getAllUsers()
    {
        return User::withAllRelations()
            ->whereHas('transactions')
            ->where('created_by', getAdminIdOrCreatedBy())
            ->get();
    }

    function search($request)
    {
        $request->validated();
        return app(Pipeline::class)
            ->send(Transaction::query())
            ->through([
                CodeFilter::class,
                ActivationStatusFilter::class,
                UserFilter::class,
                BankFilter::class,
            ])
            ->thenReturn()
            ->whereRelation('user', 'created_by', getAdminIdOrCreatedBy())
            ->orderBy('id', 'desc')
            ->paginate()
            ->withQueryString();
    }



    function updateStatus($transaction, $status)
    {
        if ($status == 'accepted') {
            $user = $transaction->user;
            $user->wallet->update([
                'balance' => $user->wallet->balance + $transaction->amount
            ]);
            $transaction->walletLogs()->create([
                'user_id' => $transaction->user_id,
                'amount' => $transaction->amount,
                'type' => TransactionTypeEnum::DEPOSIT->value,
                'description' => __('admin.transaction_status_updated', ['status' => TransactionStatusEnum::from($status)->lang()]),
            ]);
            $transaction->update(['status' => 'accepted']);
        } else {
            $transaction->update(['status' => 'rejected']);
        }
    }

    function delete($transaction)
    {
        $transaction->delete();
    }
}
