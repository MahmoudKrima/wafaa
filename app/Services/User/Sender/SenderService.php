<?php

namespace App\Services\User\Sender;

use App\Models\Sender;
use App\Filters\EmailFilter;
use App\Filters\PhoneFilter;
use Illuminate\Http\Request;
use App\Traits\TranslateTrait;
use Illuminate\Pipeline\Pipeline;
use App\Filters\NameFilter;

class SenderService
{
    use TranslateTrait;

    public function index()
    {
        return Sender::withAllRelations()
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate();
    }
    public function search(Request $request)
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
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate()
            ->withQueryString();
    }

    public function store($request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $sender = Sender::create($data);
        return $sender;
    }

    public function update($request, Sender $sender)
    {
        $data = $request->validated();
        $sender->update($data);
        return $sender;
    }

    public function delete(Sender $sender)
    {
        $sender->delete();
        return true;
    }

    public function getSenders()
    {
        return Sender::withAllRelations()
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->get();
    }
}
