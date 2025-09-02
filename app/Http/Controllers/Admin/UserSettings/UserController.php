<?php

namespace App\Http\Controllers\Admin\UserSettings;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Services\Admin\UserSettings\UserService;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\SearchUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Enum\TransactionTypeEnum;
use App\Enum\WalletLogTypeEnum;
use App\Http\Requests\Admin\WalletLogs\SearchWalletLogsRequest;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
    ) {}

    public function index()
    {
        $users = $this->userService->index();
        return view('dashboard.pages.users.index', compact('users'));
    }

    public function search(SearchUserRequest $request)
    {
        $users = $this->userService->search($request);
        return view('dashboard.pages.users.index', compact('users'));
    }

    public function create()
    {
        $allowedCompanies = $this->userService->allowedCompanies();
        return view('dashboard.pages.users.create', compact('allowedCompanies'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->store($request);
        return redirect()
            ->route('admin.users.index')
            ->with('Success', __('admin.created_successfully'));
    }

    public function edit(User $user)
    {
        $allowedCompanies = $this->userService->allowedCompanies();
        $userShippingMap = $user->shippingPrices()
            ->get(['company_id', 'company_name', 'local_price', 'international_price'])
            ->keyBy('company_id');
        return view('dashboard.pages.users.edit', compact('user', 'allowedCompanies', 'userShippingMap'));
    }


    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->update($request, $user);
        return redirect()
            ->route('admin.users.index')
            ->with('Success', __('admin.updated_successfully'));
    }

    public function delete(User $user)
    {
        $this->userService->delete($user);
        return redirect()
            ->route('admin.users.index')
            ->with('Success', __('admin.deleted_successfully'));
    }

    public function walletLogs(SearchWalletLogsRequest $request, User $user)
    {
        $walletLogs = $this->userService->walletLogs($request, $user);
        $types = TransactionTypeEnum::cases();
        $trans_types = WalletLogTypeEnum::cases();
        return view('dashboard.pages.users.wallet_logs', compact('user', 'walletLogs', 'types', 'trans_types'));
    }
}
