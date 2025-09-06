<?php

namespace App\Services\Admin\Reciever;

use App\Models\Reciever;
use App\Filters\CityFilter;
use App\Filters\EmailFilter;
use App\Filters\PhoneFilter;
use Illuminate\Http\Request;
use App\Traits\TranslateTrait;
use Illuminate\Pipeline\Pipeline;
use App\Filters\NameFilter;
use App\Models\User;

class RecieverService
{
    use TranslateTrait;

    public function index(User $user)
    {
        return Reciever::withAllRelations()
            ->where('user_id', $user->id)
            ->whereRelation('user', 'created_by', getAdminIdOrCreatedBy())
            ->orderBy('id', 'desc')
            ->paginate();
    }
    public function search(Request $request, User $user)
    {
        $request->validated();
        return app(Pipeline::class)
            ->send(Reciever::query())
            ->through([
                NameFilter::class,
                EmailFilter::class,
                PhoneFilter::class,
                CityFilter::class,
            ])
            ->thenReturn()
            ->withAllRelations()
            ->where('user_id', $user->id)
            ->whereRelation('user', 'created_by', getAdminIdOrCreatedBy())
            ->orderBy('id', 'desc')
            ->paginate()
            ->withQueryString();
    }
}
