<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestCitiesApiController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/test-cities-api', [TestCitiesApiController::class, 'test']);

require_once "admin.php";
