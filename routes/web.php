<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestCitiesApiController;
use App\Http\Controllers\Front\Home\HomeController;


Route::get('/', [HomeController::class, 'index'])->name('front.home');


Route::get('/test-cities-api', [TestCitiesApiController::class, 'test']);

require_once "admin.php";
require_once "user.php";
