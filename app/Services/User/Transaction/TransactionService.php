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
use App\Mail\NewTransactionMail;
use Illuminate\Pipeline\Pipeline;
use App\Enum\NotificationTypeEnum;
use App\Enum\TransactionStatusEnum;
use Illuminate\Support\Facades\Mail;
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
                CodeFilter::class,
                DateFromFilter::class,
                DateToFilter::class,

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

    private function notifyUser($transaction)
    {
        $message = [
            'en' => __('admin.transaction_created_notification', [
                'code' => $transaction->code,
                'status' => __("admin.{$transaction->status->value}", [], 'en'),
            ], 'en'),

            'ar' => __('admin.transaction_created_notification', [
                'code' => $transaction->code,
                'status' => __("admin.{$transaction->status->value}", [], 'ar'),
            ], 'ar'),
        ];

        auth()->user()->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => NotificationTypeEnum::TRANSACTION_CREATED->value,
            'data' => $message,
            'reciverable_type' => Admin::class,
            'reciverable_id' => auth()->user()->created_by,
        ]);
    }

    private function generateCode()
    {
        do {
            $code = strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        } while (Transaction::where('code', $code)->exists());
        return $code;
    }

    private function sendEmailWithNewTransaction($transaction)
    {
        if (env('SEND_MAIL', false)) {
            $adminId = optional(auth()->user())->created_by;
            $admin = Admin::select('email', 'name')
                ->find($adminId);
            if ($admin) {
                $adminEmail = $admin->email ?? null;
                $adminDisplayName = $admin->name ?? null;
                Mail::to($adminEmail)->send(
                    new NewTransactionMail($transaction, $adminDisplayName ?? null)
                );
            }
        }
    }

    public function store($request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['status'] = 'pending';
        $data['attachment'] = $this->uploadImage($request->file('attachment'), 'transactions');
        $data['code'] = $this->generateCode();
        $transaction = Transaction::create($data);
        $this->notifyUser($transaction);
        $this->sendEmailWithNewTransaction($transaction);
    }
}
