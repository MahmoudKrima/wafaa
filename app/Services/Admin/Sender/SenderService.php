<?php

namespace App\Services\Admin\Sender;

use App\Models\Sender;
use App\Filters\EmailFilter;
use App\Filters\PhoneFilter;
use Illuminate\Http\Request;
use App\Traits\TranslateTrait;
use Illuminate\Pipeline\Pipeline;
use App\Filters\NameFilter;
use App\Models\User;

class SenderService
{
    use TranslateTrait;

    public function index(User $user)
    {
        return Sender::withAllRelations()
            ->where('user_id', $user->id)
            ->whereRelation('user', 'created_by', getAdminIdOrCreatedBy())
            ->orderBy('id', 'desc')
            ->paginate();
    }
    public function search(Request $request, User $user)
    {
        $request->validated();
        return app(Pipeline::class)
            ->send(Sender::query())
            ->through([
                NameFilter::class,
                EmailFilter::class,
                PhoneFilter::class,
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
