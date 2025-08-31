<?php

namespace App\Http\Controllers\User\Profile;

use App\Traits\ImageTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Profile\UpdateProfileRequest;
use App\Services\User\Profile\ProfileService;

class ProfileController extends Controller
{
    use ImageTrait;

    public function __construct(private ProfileService $profileService) {}

    public function index()
    {
        return view('user.pages.profile.index');
    }

    public function update(UpdateProfileRequest $request)
    {
        $this->profileService->updateProfile($request);
        return back()
            ->with("Success", __('user.profile_updated_successfully'));
    }
}
