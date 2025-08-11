<?php

namespace App\Http\Controllers\Admin\Bank;

use App\Models\Bank;
use App\Enum\PlanTransactionEnum;
use App\Enum\ActivationStatusEnum;
use App\Http\Controllers\Controller;
use App\Services\Admin\Bank\BankService;
use App\Http\Requests\Admin\Bank\StoreBankRequest;
use App\Http\Requests\Admin\Bank\SearchBankRequest;
use App\Http\Requests\Admin\Bank\UpdateBankRequest;
use App\Http\Requests\Admin\Bank\SearchPlanTransactionRequest;
use App\Models\Banks;

class BankController extends Controller
{
    public function __construct(private BankService $bankService) {}

    public function index()
    {
        $banks = $this->bankService->getAll();
        $status = ActivationStatusEnum::cases();
        return view('dashboard.pages.bank.index', compact('banks', 'status'));
    }

    public function search(SearchBankRequest $request)
    {
        $banks = $this->bankService->searchBank($request);
        $status = ActivationStatusEnum::cases();
        return view('dashboard.pages.bank.index', compact('banks', 'status'));
    }

    // public function showTransactions(Bank $bank)
    // {
    //     $planTransactions = $this->bankService->showBankTransactions($bank);
    //     $plans = $this->bankService->getPlans($bank);
    //     $providers = $this->bankService->getProviders($bank);
    //     $status = PlanTransactionEnum::cases();
    //     return view('dashboard.pages.bank.show_transaction', compact('plans', 'providers', 'status', 'bank', 'planTransactions'));
    // }

    // public function searchTransactions(SearchPlanTransactionRequest $request, Bank $bank)
    // {
    //     $planTransactions = $this->bankService->searchBankTransactions($request, $bank);
    //     $plans = $this->bankService->getPlans($bank);
    //     $providers = $this->bankService->getProviders($bank);
    //     $status = PlanTransactionEnum::cases();
    //     return view('dashboard.pages.bank.show_transaction', compact('plans', 'providers', 'status', 'bank', 'planTransactions'));
    // }

    public function create()
    {
        $status = ActivationStatusEnum::cases();
        return view('dashboard.pages.bank.create', compact('status'));
    }

    public function store(StoreBankRequest $request)
    {
        $this->bankService->storeBank($request);
        return back()
            ->with("Success", __('admin.created_successfully'));
    }

    public function edit(Banks $bank)
    {
        $status = ActivationStatusEnum::cases();
        return view('dashboard.pages.bank.edit', compact('status', 'bank'));
    }

    public function update(UpdateBankRequest $request, Banks $bank)
    {
        $this->bankService->updateBank($request, $bank);
        return back()
            ->with("Success", __('admin.updated_successfully'));
    }

    public function updateStatus(Banks $bank)
    {
        $this->bankService->updateBankStatus($bank);
        return back()
            ->with("Success", __('admin.updated_successfully'));
    }

    public function delete(Banks $bank)
    {
        $this->bankService->deleteBank($bank);
        return back()
            ->with("Success", __('admin.deleted_successfully'));
    }
}
