<?php

namespace App\Http\Controllers\Admin\Transaction;

use App\Models\Transaction;
use App\Enum\TransactionStatusEnum;
use App\Http\Controllers\Controller;
use App\Services\Admin\Transaction\TransactionService;
use App\Http\Requests\Admin\Transaction\SearchTransactionRequest;

class TransactionController extends Controller
{
    public function __construct(private TransactionService $transactionService) {}

    public function index()
    {
        $transactions = $this->transactionService->getAll();
        $banks = $this->transactionService->getAllBanks();
        $status = TransactionStatusEnum::cases();
        $users = $this->transactionService->getAllUsers();
        return view('dashboard.pages.transaction.index', compact('transactions', 'banks', 'status', 'users'));
    }

    public function search(SearchTransactionRequest $request)
    {
        $transactions = $this->transactionService->search($request);
        $banks = $this->transactionService->getAllBanks();
        $status = TransactionStatusEnum::cases();
        $users = $this->transactionService->getAllUsers();
        return view('dashboard.pages.transaction.index', compact('transactions', 'banks', 'status', 'users'));
    }


    public function updateStatus(Transaction $transaction)
    {
        $status = request('status');
        
        if (!in_array($status, TransactionStatusEnum::vals())) {
            return back()->with('Error', __('admin.invalid_status'));
        }
        
        $this->transactionService->updateStatus($transaction, $status);
        return back()
            ->with("Success", __('admin.updated_successfully'));
    }

    public function delete(Transaction $transaction)
    {
        $this->transactionService->delete($transaction);
        return back()
            ->with("Success", __('admin.deleted_successfully'));
    }
}
