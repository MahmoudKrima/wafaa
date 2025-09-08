<?php

namespace App\Http\Controllers\Admin\Bank;

use App\Enum\ActivationStatusEnum;
use App\Http\Controllers\Controller;
use App\Services\Admin\Bank\BankService;
use App\Http\Requests\Admin\Bank\StoreBankRequest;
use App\Http\Requests\Admin\Bank\SearchBankRequest;
use App\Http\Requests\Admin\Bank\UpdateBankRequest;
use App\Models\Banks;

class BankController extends Controller
{
    public function __construct(private BankService $bankService)
    {
    }

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
        if (!$bank || $bank->admin_id != getAdminIdOrCreatedBy()) {
            return back()
                ->with('Error', __('admin.not_found_data'));
        }
        $status = ActivationStatusEnum::cases();
        return view('dashboard.pages.bank.edit', compact('status', 'bank'));
    }

    public function update(UpdateBankRequest $request, Banks $bank)
    {
        if (!$bank || $bank->admin_id != getAdminIdOrCreatedBy()) {
            return back()
                ->with('Error', __('admin.not_found_data'));
        }
        $this->bankService->updateBank($request, $bank);
        return back()
            ->with("Success", __('admin.updated_successfully'));
    }

    public function updateStatus(Banks $bank)
    {
        if (!$bank || $bank->admin_id != getAdminIdOrCreatedBy()) {
            return back()
                ->with('Error', __('admin.not_found_data'));
        }
        $this->bankService->updateBankStatus($bank);
        return back()
            ->with("Success", __('admin.updated_successfully'));
    }

    public function delete(Banks $bank)
    {
        if (!$bank || $bank->admin_id != getAdminIdOrCreatedBy()) {
            return back()
                ->with('Error', __('admin.not_found_data'));
        }
        $this->bankService->deleteBank($bank);
        return back()
            ->with("Success", __('admin.deleted_successfully'));
    }
}
