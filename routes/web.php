<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\Home\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('front.home');
Route::post('/contact', [HomeController::class, 'contact'])->name('front.contact.store');

Route::get('/test-cities-api', [TestCitiesApiController::class, 'test']);

require_once "admin.php";
require_once "user.php";
