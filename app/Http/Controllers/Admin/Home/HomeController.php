<?php

namespace App\Http\Controllers\Admin\Home;

use App\Http\Controllers\Controller;
use App\Services\Admin\Home\HomeService;

class HomeController extends Controller
{


    public function __construct(private HomeService $homeService)
    {
    }

    public function index()
    {
        $stats = $this->homeService->dashboardStats();
        $usersStatistics = $this->homeService->usersStatistics();
        $transactionsStatistics = $this->homeService->transactionsStatistics();
        $messagesStatistics = $this->homeService->messagesStatistics();
        $globalStatistics = $this->homeService->globalStatistics();
        return view('dashboard.pages.home.index', compact('stats', 'usersStatistics', 'transactionsStatistics', 'messagesStatistics', 'globalStatistics'));
    }
}
