<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\Home\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('front.home');

require_once "admin.php";
require_once "user.php";
