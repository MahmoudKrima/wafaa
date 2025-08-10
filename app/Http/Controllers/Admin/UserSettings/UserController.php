<?php

namespace App\Http\Controllers\Admin\UserSettings;

use App\Models\City;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\UserSettings\UserService;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\SearchUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function index()
    {
        $users = $this->userService->index();
        $cities = City::all();
        return view('dashboard.pages.users.index', compact('users',  'cities'));
    }

    public function search(SearchUserRequest $request)
    {
        $users = $this->userService->search($request);
        $cities = City::all();
        return view('dashboard.pages.users.index', compact('users',  'cities'));
    }

    public function create()
    {
        $cities = City::all();
        return view('dashboard.pages.users.create', compact('cities'));
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
        $cities = City::all();
        return view('dashboard.pages.users.edit', compact('user', 'cities'));
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
}
