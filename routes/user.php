<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Auth\AuthController;
use App\Http\Controllers\User\Home\HomeController;
use App\Http\Controllers\User\Profile\ProfileController;
use App\Http\Controllers\User\Shipping\ShippingController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\User\WalletLogs\WalletLogsController;
use App\Http\Controllers\User\Transaction\TransactionController;
use App\Http\Controllers\User\Reciever\RecieverController;
use App\Http\Controllers\User\Notification\NotificationController;


Route::controller(ShippingController::class)
    ->group(function () {
        Route::get('/states', 'getStates')
            ->name('shippings.states');
        Route::get('/receivers', 'receivers')
            ->name('recievers.index');
        Route::get('/cities', 'getCities')
            ->name('shippings.cities');
        Route::get('/cities-by-state', 'getCitiesByState')
            ->name('shippings.citiesByState');
        Route::get('/wallet/balance', 'walletBalance')
            ->name('wallet.balance');
    });

Route::middleware(['web'])->group(function () {
    Route::controller(AuthController::class)
        ->as('user.')
        ->prefix(LaravelLocalization::setLocale() . '/user')
        ->group(function () {
            Route::get('/', 'loginForm')
                ->name('auth.loginForm')
                ->middleware('guest.user');
            Route::get('/login', 'loginForm')
                ->name('auth.loginForm')
                ->middleware('guest.user');
            Route::post('/login', 'login')
                ->name('auth.login');
            Route::post('/logout', 'logout')
                ->name('auth.logout')
                ->middleware('auth:web');
            Route::get('/forget-password', 'forgetPasswordForm')
                ->name('auth.forgetPasswordForm')
                ->middleware('guest.user');
            Route::post('/forget-password', 'forgetPassword')
                ->name('auth.forgetPassword')
                ->middleware('guest.user');
            Route::get('/reset-password', 'resetPassword')
                ->name('auth.resetPassword')
                ->middleware('guest.user');
            Route::post('/reset-password-submit', 'resetPasswordSubmit')
                ->name('auth.resetPasswordSubmit')
                ->middleware('guest.user');
        });
    Route::group([
        'as' => 'user.',
        'prefix' => LaravelLocalization::setLocale() . '/user',
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'auth:web']
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
        Route::controller(TransactionController::class)
            ->group(function () {
                Route::get('/transactions', 'index')
                    ->name('transactions.index');
                Route::get('/create-transaction', 'create')
                    ->name('transactions.create');
                Route::post('/create-transaction', 'store')
                    ->name('transactions.store');
                Route::get('/banks', 'banks')
                    ->name('banks.index');
            });

        Route::controller(ShippingController::class)
            ->group(function () {
                Route::get('/shippings', 'index')
                    ->name('shippings.index');
                Route::get('/shippings-export', 'export')
                    ->name('shippings.export');
                Route::get('/create-shipping', 'create')
                    ->name('shippings.create');
                Route::post('/create-shipping', 'store')
                    ->name('shippings.store');
                Route::get('/shipping-companies', 'shippingCompanies')
                    ->name('shippings.companies');
                Route::get('/shippings/{id}', 'show')
                    ->name('shippings.show');
            });

        Route::controller(WalletLogsController::class)
            ->group(function () {
                Route::get('/wallet-logs', 'index')
                    ->name('wallet-logs.index');
            });

        Route::controller(NotificationController::class)
            ->group(function () {
                Route::get('/notifications', 'index')
                    ->name('notifications.index');
            });
        Route::controller(RecieverController::class)
            ->group(function () {
                Route::get('/recievers', 'index')
                    ->name('recievers.index');
                Route::get('/recievers/create', 'create')
                    ->name('recievers.create');
                Route::post('/recievers/store', 'store')
                    ->name('recievers.store');
                Route::get('/recievers/edit/{reciever}', 'edit')
                    ->name('recievers.edit');
                Route::post('/recievers/update/{reciever}', 'update')
                    ->name('recievers.update');
                Route::delete('/recievers/delete/{reciever}', 'delete')
                    ->name('recievers.delete');
                Route::get('/recievers/search', 'search')
                    ->name('recievers.search');
            });
    });
});
