<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\Home\HomeController;
use App\Http\Controllers\General\CronJobController;

Route::get('/', [HomeController::class, 'index'])->name('front.home');
Route::get('/terms', [HomeController::class, 'terms'])->name('front.terms');
Route::get('/policy', [HomeController::class, 'policy'])->name('front.policy');
Route::post('/contact', [HomeController::class, 'contact'])->name('front.contact.store');
Route::get('/confirm-cancel', [CronJobController::class, 'confirmCancel'])->name('front.confirm.cancel');


require_once "admin.php";
require_once "user.php";
