<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\Home\HomeController;
use App\Http\Controllers\Admin\Profile\ProfileController;
use App\Http\Controllers\Admin\UserSettings\AdminController;
use App\Http\Controllers\Admin\UserSettings\UserController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Admin\WebSiteSettings\RoleController;
use App\Http\Controllers\Admin\WebSiteSettings\SettingsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::controller(AuthController::class)
    ->as('admin.')
    ->prefix(LaravelLocalization::setLocale())
    ->group(function () {
        Route::get('/', 'loginForm')
            ->name('auth.loginForm')
            ->middleware('guest.admin');
        Route::post('/login', 'login')
            ->name('auth.login');
        Route::post('/logout', 'logout')
            ->name('auth.logout')
            ->middleware('auth:admin');
        Route::get('/forget-password', 'forgetPasswordForm')
            ->name('auth.forgetPasswordForm')
            ->middleware('guest.admin');
        Route::post('/forget-password', 'forgetPassword')
            ->name('auth.forgetPassword')
            ->middleware('guest.admin');
        Route::get('/reset-password', 'resetPassword')
            ->name('auth.resetPassword')
            ->middleware('guest.admin');
        Route::post('/reset-password-submit', 'resetPasswordSubmit')
            ->name('auth.resetPasswordSubmit')
            ->middleware('guest.admin');
    });

Route::group([
    'prefix' => LaravelLocalization::setLocale() . '/admin',
    'as' => 'admin.',
    'middleware' => ['auth:admin', 'active.admin', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {
    Route::get('/dashboard', [HomeController::class, 'index'])
        ->name('dashboard.index');
    Route::controller(ProfileController::class)
        ->group(function () {
            Route::get('/update-profile', 'index')
                ->name('profile.index');
            Route::post('/update-profile', 'update')
                ->name('profile.update');
        });

    Route::controller(AdminController::class)
        ->group(function () {
            Route::get('/admins', 'index')
                ->name('admins.index')
                ->middleware('has.permission:admins.view');
            Route::get('/admins-search', 'search')
                ->name('admins.search')
                ->middleware('has.permission:admins.view');
            Route::get('/admins-create', 'create')
                ->name('admins.create')
                ->middleware('has.permission:admins.create');
            Route::post('/admins-store', 'store')
                ->name('admins.store')
                ->middleware('has.permission:admins.create');
            Route::get('/admins/edit/{admin}', 'edit')
                ->name('admins.edit')
                ->middleware('has.permission:admins.update', 'not.this.admin');
            Route::post('/admins/update/{admin}', 'update')
                ->name('admins.update')
                ->middleware('has.permission:admins.update', 'not.this.admin');
            Route::post('/admins/update-status/{admin}', 'updateStatus')
                ->name('admins.updateStatus')
                ->middleware('has.permission:admins.update', 'not.this.admin');
            Route::delete('/admins/delete/{admin}', 'delete')
                ->name('admins.delete')
                ->middleware('has.permission:admins.delete', 'not.this.admin');
        });

    Route::controller(UserController::class)
        ->group(function () {
            Route::get('/users', 'index')
                ->name('users.index')
                ->middleware('has.permission:users.view');
            Route::get('/users-search', 'search')
                ->name('users.search')
                ->middleware('has.permission:users.view');
            Route::get('/users-create', 'create')
                ->name('users.create')
                ->middleware('has.permission:users.create');
            Route::post('/users-store', 'store')
                ->name('users.store')
                ->middleware('has.permission:users.create');
            Route::get('/users/edit/{user}', 'edit')
                ->name('users.edit')
                ->middleware('has.permission:users.update');
            Route::post('/users/update/{user}', 'update')
                ->name('users.update')
                ->middleware('has.permission:users.update');
            Route::post('/users/update-status/{user}', 'updateStatus')
                ->name('users.updateStatus')
                ->middleware('has.permission:users.update');
            Route::delete('/users/delete/{user}', 'delete')
                ->name('users.delete')
                ->middleware('has.permission:users.delete');
        });

    Route::controller(RoleController::class)
        ->group(function () {
            Route::get('/roles', 'index')
                ->name('roles.index')
                ->middleware('has.permission:roles.view');
            Route::get('/roles-search', 'search')
                ->name('roles.search')
                ->middleware('has.permission:roles.view');
            Route::get('/roles/create', 'create')
                ->name('roles.create')
                ->middleware('has.permission:roles.create');
            Route::post('/roles/store', 'store')
                ->name('roles.store')
                ->middleware('has.permission:roles.create');
            Route::get('/roles/edit/{role}', 'edit')
                ->name('roles.edit')
                ->middleware('has.permission:roles.update');
            Route::post('/roles/update/{role}', 'update')
                ->name('roles.update')
                ->middleware('has.permission:roles.update');
            Route::delete('/roles-update/{role}', 'delete')
                ->name('roles.delete')
                ->middleware('has.permission:roles.delete');
        });

    Route::controller(SettingsController::class)
        ->group(function () {
            Route::get('/settings', 'index')
                ->name('settings.index')
                ->middleware('has.permission:settings.update');
            Route::post('/settings-update', 'update')
                ->name('settings.update')
                ->middleware('has.permission:settings.update');
        });

    
});
