<?php

namespace App\Http\Controllers\Admin\Reciever;

use App\Http\Controllers\Controller;
use App\Services\Admin\Reciever\RecieverService;
use App\Http\Requests\Admin\Reciever\SearchRecieverRequest;


class RecieverController extends Controller
{
    public function __construct(
        private RecieverService $recieverService,
    ) {}

    public function index()
    {
        $recievers = $this->recieverService->index();
        return view('dashboard.pages.recievers.index', compact('recievers'));
    }

    public function search(SearchRecieverRequest $request)
    {
        $recievers = $this->recieverService->search($request);
        return view('dashboard.pages.recievers.index', compact('recievers'));
    }
}
