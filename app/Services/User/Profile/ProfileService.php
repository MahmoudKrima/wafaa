<?php

namespace App\Services\User\Profile;

use App\Traits\ImageTrait;
use App\Traits\TranslateTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    use ImageTrait, TranslateTrait;

    public function updateProfile($request)
    {
        $user = Auth::guard('web')->user();
        $data = $request->validated();
        $data['name'] = $this->translate($data['name_ar'], $data['name_en']);
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ];

        if (isset($data['additional_phone'])) {
            $updateData['additional_phone'] = $data['additional_phone'];
        }
        $user->update($updateData);
        if (isset($data['password']) && !empty($data['password'])) {
            $user->update([
                'password' => Hash::make($data['password'])
            ]);
        }
        return $user;
    }
}
