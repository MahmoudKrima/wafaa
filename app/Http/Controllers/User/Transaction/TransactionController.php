<?php

namespace App\Http\Controllers\User\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Transaction\SearchTransactionRequest;
use App\Http\Requests\User\Transaction\StoreTransactionRequest;
use App\Services\User\Transaction\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(private TransactionService $transactionService) {}

    public function index(SearchTransactionRequest $request)
    {
        $result = $this->transactionService->index($request);
        return view('user.pages.transactions.index', compact('result'));
    }

    public function create()
    {
        $banks = $this->transactionService->allActiveBanks();
        return view('user.pages.transactions.create', compact('banks'));
    }

    public function store(StoreTransactionRequest $request)
    {
        $this->transactionService->store($request);
        return back()
            ->with('Success', __('admin.created_successfully'));
    }

    public function banks()
    {
        $banks = $this->transactionService->allActiveBanks();
        return view('user.pages.transactions.banks', compact('banks'));
    }
}
