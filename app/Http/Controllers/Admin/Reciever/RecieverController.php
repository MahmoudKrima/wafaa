<?php

namespace App\Http\Controllers\Admin\Reciever;

use App\Http\Controllers\Controller;
use App\Services\Admin\Reciever\RecieverService;
use App\Http\Requests\Admin\Reciever\SearchRecieverRequest;
use App\Models\User;


class RecieverController extends Controller
{
    public function __construct(
        private RecieverService $recieverService,
    ) {}

    public function index(User $user)
    {
        $recievers = $this->recieverService->index($user);
        return view('dashboard.pages.recievers.index', compact('recievers', 'user'));
    }

    public function search(SearchRecieverRequest $request, User $user)
    {
        $recievers = $this->recieverService->search($request, $user);
        return view('dashboard.pages.recievers.index', compact('recievers', 'user'));
    }
}
