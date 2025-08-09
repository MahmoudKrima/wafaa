<?php

namespace App\Http\Controllers\Admin\UserSettings;

use App\Models\User;
use Illuminate\Http\Request;
use App\Enum\ActivationStatusEnum;
use App\Http\Controllers\Controller;
use App\Services\Admin\UserSettings\UserService;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function index()
    {
        $users = $this->userService->index();
        $status = ActivationStatusEnum::cases();

        return view('dashboard.pages.users.index', compact('users', 'status'));
    }

    public function search(Request $request)
    {
        $users = $this->userService->search($request);
        $status = ActivationStatusEnum::cases();

        return view('dashboard.pages.users.index', compact('users', 'status'));
    }

    public function create()
    {
        return view('dashboard.pages.users.create');
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
        return view('dashboard.pages.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->update($request, $user);
        return redirect()
            ->route('admin.users.index')
            ->with('Success', __('admin.updated_successfully'));
    }

    public function updateStatus(Request $request, User $user)
    {
        $this->userService->updateStatus($request, $user);
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
}
