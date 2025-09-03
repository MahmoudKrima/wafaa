<?php

namespace App\Services\User\Reciever;

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
            ->where('user_id', auth()->id())
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
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate()
            ->withQueryString();
    }

    public function store($request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $reciever = Reciever::create($data);
        return $reciever;
    }

    public function update($request, Reciever $reciever)
    {
        $data = $request->validated();
        $reciever->update($data);
        return $reciever;
    }

    public function delete(Reciever $reciever)
    {
        $reciever->delete();
        return true;
    }
}
