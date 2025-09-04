<?php

namespace App\Services\Admin\Transaction;

use App\Models\User;
use App\Models\Banks;
use App\Models\WalletLog;
use App\Traits\ImageTrait;
use App\Filters\BankFilter;
use App\Filters\CodeFilter;
use App\Filters\UserFilter;
use App\Models\Transaction;
use App\Traits\TranslateTrait;
use Illuminate\Pipeline\Pipeline;
use App\Filters\ActivationStatusFilter;

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
            $oldBalance = $user->wallet->balance;
            $user->wallet->update([
                'balance' => $user->wallet->balance + $transaction->amount
            ]);
            $newBalance = $user->wallet->fresh()->balance;
            $transaction->update([
                'status'      => 'accepted',
                'accepted_by' => auth('admin')->user()->id,
            ]);
            WalletLog::create([
                'user_id'    => $transaction->user_id,
                'amount'     => $transaction->amount,
                'type'       => 'deposit',
                'trans_type' => 'transaction',
                'admin_id'   => auth('admin')->user()->id,
                'description' => [
                    'ar' => __('admin.transaction_status_updated', [
                        'status'   => __("admin.{$transaction->status->value}", [], 'ar'),
                        'previous' => number_format($oldBalance, 2),
                        'current'  => number_format($newBalance, 2),
                    ], 'ar'),

                    'en' => __('admin.transaction_status_updated', [
                        'status'   => __("admin.{$transaction->status->value}", [], 'en'),
                        'previous' => number_format($oldBalance, 2),
                        'current'  => number_format($newBalance, 2),
                    ], 'en'),
                ],
            ]);
        } else {
            $transaction->update([
                'status' => 'rejected',
                'accepted_by' => auth('admin')->user()->id,
            ]);
        }
    }

    function delete($transaction)
    {
        $transaction->delete();
    }
}
