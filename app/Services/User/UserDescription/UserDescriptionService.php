<?php

namespace App\Services\User\UserDescription;

use App\Models\UserDescription;

class UserDescriptionService
{

    public function index()
    {
        return UserDescription::withAllRelations()
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate();
    }
    public function store($request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $userDescription = UserDescription::create($data);
        return $userDescription;
    }

    public function update($request, UserDescription $userDescription)
    {
        $data = $request->validated();
        $userDescription->update($data);
        return $userDescription;
    }

    public function delete(UserDescription $userDescription)
    {
        $userDescription->delete();
        return true;
    }

    public function getUserDescriptions()
    {
        return UserDescription::withAllRelations()
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->get();
    }
}
