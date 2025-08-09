<?php

namespace App\Services\Admin\UserSettings;

use App\Models\User;
use App\Enum\ActivationStatusEnum;
use App\Filters\ActivationStatusFilter;
use App\Filters\EmailFilter;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;

class UserService
{
    public function index()
    {
        return User::paginate(10);
    }

    public function search(Request $request)
    {
        return app(Pipeline::class)
            ->send(User::query())
            ->through([
                EmailFilter::class,
                ActivationStatusFilter::class,
            ])
            ->thenReturn()
            ->paginate(10);
    }

    public function store($request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'status' => $request->status,
        ]);

        return $user;
    }

    public function update($request, User $user)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);
        return $user;
    }

    public function updateStatus(Request $request, User $user)
    {
        $user->update([
            'status' => $request->status === 'active' ? ActivationStatusEnum::ACTIVE : ActivationStatusEnum::INACTIVE
        ]);
        return $user;
    }

    public function delete(User $user)
    {
        $user->delete();
        return true;
    }
}