<?php

namespace App\Services\Admin\UserSettings;

use App\Models\User;
use App\Filters\CityFilter;
use App\Filters\EmailFilter;
use App\Filters\PhoneFilter;
use Illuminate\Http\Request;
use App\Traits\TranslateTrait;
use App\Filters\NameJsonFilter;
use Illuminate\Pipeline\Pipeline;

class UserService
{
    use TranslateTrait;

    public function index()
    {
        return User::withAllRelations()
            ->where('created_by', getAdminIdOrCreatedBy())
            ->orderBy('id', 'desc')
            ->paginate();
    }

    public function search(Request $request)
    {

        $request->validated();
        return app(Pipeline::class)
            ->send(User::query())
            ->through([
                NameJsonFilter::class,
                EmailFilter::class,
                PhoneFilter::class,
                CityFilter::class,
            ])
            ->thenReturn()
            ->withAllRelations()
            ->where('created_by', getAdminIdOrCreatedBy())
            ->orderBy('id', 'desc')
            ->paginate()
            ->withQueryString();
    }

    public function store($request)
    {
        $data = $request->validated();
        $data['created_by'] = getAdminIdOrCreatedBy();
        $data['name'] = $this->translate($data['name_ar'], $data['name_en']);
        $data['city_name'] = $this->translate($data['city_name_ar'], $data['city_name_en']);
        $data['state_name'] = $this->translate($data['state_name_ar'], $data['state_name_en']);
        $data['country_name'] = $this->translate($data['country_name_ar'], $data['country_name_en']);
        $data['added_by'] = auth('admin')->id();
        $user = User::create($data);
        return $user;
    }

    public function update($request, User $user)
    {
        $data = $request->validated();
        if (!isset($data['password'])) {
            unset($data['password']);
        }
        $data['name'] = $this->translate($data['name_ar'], $data['name_en']);
        $data['city_name'] = $this->translate($data['city_name_ar'], $data['city_name_en']);
        $data['state_name'] = $this->translate($data['state_name_ar'], $data['state_name_en']);
        $data['country_name'] = $this->translate($data['country_name_ar'], $data['country_name_en']);
        $user->update($data);
        return $user;
    }

    public function delete(User $user)
    {
        $user->delete();
        return true;
    }

    public function walletLogs(User $user)
    {
        return $user->walletLogs()->orderBy('id', 'desc')->paginate();
    }
}
