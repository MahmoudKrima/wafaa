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

class RecieverService
{
    use TranslateTrait;

    public function index()
    {
        return Reciever::withAllRelations()
            ->whereRelation('user', 'created_by', getAdminIdOrCreatedBy())
            ->orderBy('id', 'desc')
            ->paginate();
    }
    public function search(Request $request)
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
            ->whereRelation('user', 'created_by', getAdminIdOrCreatedBy())
            ->orderBy('id', 'desc')
            ->paginate()
            ->withQueryString();
    }
    
}
