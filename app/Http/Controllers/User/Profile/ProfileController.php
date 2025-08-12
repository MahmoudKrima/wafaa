<?php

namespace App\Http\Controllers\User\Profile;

use Throwable;
use App\Models\City;
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
        $cities = City::orderBy('name')->get();
        return view('user.pages.profile.index', compact('cities'));
    }

    public function update(UpdateProfileRequest $request)
    {
        try {
            $this->profileService->updateProfile($request);
            return back()
                ->with("Success", __('user.profile_updated_successfully'));
        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->with("Error", __('user.profile_update_failed'));
        }
    }
}
