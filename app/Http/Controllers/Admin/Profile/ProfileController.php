<?php

namespace App\Http\Controllers\Admin\Profile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\Profile\ProfileService;
use App\Http\Requests\Admin\Profile\UpdateProfileRequest;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profileService)
    {
    }

    public function index()
    {
        return view('dashboard.pages.profile.index');
    }

    public function update(UpdateProfileRequest $request)
    {
        $this->profileService->updateProfile($request);
        return back()
            ->with("Success", __('admin.updated_successfully'));
    }
}
