<?php

namespace App\Services\User\Profile;

use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileService
{
    use ImageTrait;

    public function updateProfile($request)
    {
        $user = Auth::guard('web')->user();
        $data = $request->validated();

        // Update basic fields
        $user->update([
            'name' => $data['name'] ?? $user->name,
            'phone' => $data['phone'] ?? $user->phone,
            'additional_phone' => $data['additional_phone'] ?? $user->additional_phone,
            'address' => $data['address'] ?? $user->address,
            'city_id' => $data['city_id'] ?? $user->city_id,
        ]);

        // Update password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $user->update([
                'password' => Hash::make($data['password'])
            ]);
        }

        return $user;
    }
}
