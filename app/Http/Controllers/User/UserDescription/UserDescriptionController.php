<?php

namespace App\Http\Controllers\User\UserDescription;

use App\Models\UserDescription;
use App\Http\Controllers\Controller;
use App\Services\User\UserDescription\UserDescriptionService;
use App\Http\Requests\User\UserDescription\StoreUserDescriptionRequest;
use App\Http\Requests\User\UserDescription\UpdateUserDescriptionRequest;


class UserDescriptionController extends Controller
{
    public function __construct(
        private UserDescriptionService $userDescriptionService,
    ) {}

    public function index()
    {
        $userDescriptions = $this->userDescriptionService->index();
        return view('user.pages.user_descriptions.index', compact('userDescriptions'));
    }

    public function create()
    {
        return view('user.pages.user_descriptions.create');
    }

    public function store(StoreUserDescriptionRequest $request)
    {
        $this->userDescriptionService->store($request);
        return redirect()
            ->route('user.user-descriptions.index')
            ->with('Success', __('admin.created_successfully'));
    }

    public function edit(UserDescription $userDescription)
    {
        return view('user.pages.user_descriptions.edit', compact('userDescription'));
    }


    public function update(UpdateUserDescriptionRequest $request, UserDescription $userDescription)
    {
        $this->userDescriptionService->update($request, $userDescription);
        return redirect()
            ->route('user.user-descriptions.index')
            ->with('Success', __('admin.updated_successfully'));
    }

    public function delete(UserDescription $userDescription)
    {
        $this->userDescriptionService->delete($userDescription);
        return redirect()
            ->route('user.user-descriptions.index')
            ->with('Success', __('admin.deleted_successfully'));
    }

    public function getUserDescriptions()
    {
        $userDescriptions = $this->userDescriptionService->getUserDescriptions();
        return response()->json($userDescriptions);
    }
}
