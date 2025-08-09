<?php

namespace App\Http\Controllers\Admin\Home;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('dashboard.pages.home.index');
    }
}
