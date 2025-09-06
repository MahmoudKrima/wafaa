<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\Home\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('front.home');
Route::get('/terms', [HomeController::class, 'terms'])->name('front.terms');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('front.privacy');
Route::post('/contact', [HomeController::class, 'contact'])->name('front.contact.store');


require_once "admin.php";
require_once "user.php";
