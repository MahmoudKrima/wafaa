<?php

namespace App\Http\Controllers\User\Profile;

use Throwable;
use App\Models\User;
use App\Models\Country;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\Profile\UpdateProfileRequest;
use App\Services\User\Profile\ProfileService;
use Illuminate\Support\Facades\Storage;

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
