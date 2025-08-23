<?php

namespace App\Http\Controllers\Admin\UserSettings;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\UserSettings\UserService;
use App\Services\Admin\UserSettings\StateService;
use App\Services\Admin\UserSettings\CityService;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\SearchUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private StateService $stateService,
        private CityService $cityService
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
        $states = $this->stateService->getStates();
        return view('dashboard.pages.users.create', compact('states'));
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
        $cities = [];
        $states = $this->stateService->getStates();
        if ($user->state_id) {
            $cities = $this->cityService->getCitiesByState($user->state_id);
        }
        return view('dashboard.pages.users.edit', compact('user', 'cities', 'states'));
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

    public function getCitiesByState(Request $request)
    {
        $stateId = $request->input('state_id');
        if (!$stateId) {
            return response()->json([]);
        }

        $cities = $this->cityService->getCitiesByState($stateId);
        return response()->json($cities);
    }

    public function walletLogs(User $user)
    {
        $walletLogs = $this->userService->walletLogs($user);
        return view('dashboard.pages.users.wallet-logs', compact('user', 'walletLogs'));
    }
}
