<?php

namespace App\Services\Admin\Profile;

use App\Traits\ImageTrait;

class ProfileService
{
    use ImageTrait;

    function updateProfile($request)
    {
        $data = $request->validated();
        if (!isset($data['password'])) {
            unset($data['password']);
        }
        $data['image'] = ImageTrait::updateImage(auth('admin')->user()->image, 'admin/profile', 'image');
        auth('admin')->user()
            ->update($data);
    }
}
