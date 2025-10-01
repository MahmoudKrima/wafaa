<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Auth\AuthController;
use App\Http\Controllers\User\Home\HomeController;
use App\Http\Controllers\User\Sender\SenderController;
use App\Http\Controllers\User\Profile\ProfileController;
use App\Http\Controllers\User\Reciever\RecieverController;
use App\Http\Controllers\User\Shipping\ShippingController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\User\WalletLogs\WalletLogsController;
use App\Http\Controllers\User\Transaction\TransactionController;
use App\Http\Controllers\User\Notification\NotificationController;
use App\Http\Controllers\User\UserDescription\UserDescriptionController;


        Route::controller(ShippingController::class)
            ->group(function () {
                Route::get('/states', 'getStates')
                    ->name('shippings.states');
                Route::get('/receivers', 'receivers')
                    ->name('recievers.index');
                Route::get('/receivers-by-company/{shippingCompanyId}', 'receiversByCompany')
                    ->name('shippings.receiversByCompany');
                Route::get('/senders-by-company/{shippingCompanyId}', 'sendersByCompany')
                    ->name('shippings.sendersByCompany');
                Route::get('/cities', 'getCities')
                    ->name('shippings.cities');
                Route::get('/cities-by-state', 'getCitiesByState')
                    ->name('shippings.citiesByState');
                Route::get('/wallet/balance', 'walletBalance')
                    ->name('wallet.balance');
                Route::get('/cities-by-company-and-country/{shippingCompanyId}', 'getCitiesByCompanyAndCountry')
                    ->name('shippings.citiesByCompanyAndCountry');
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
                ->middleware(['auth:web', 'active.user']);
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
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'auth:web', 'active.user']
    ], function () {

        Route::controller(HomeController::class)
            ->group(function () {
                Route::get('/dashboard', 'index')
                    ->name('dashboard.index');
                Route::get('/contacts', 'contacts')
                    ->name('contacts.index');
            });

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
                Route::post('/shippings-print', 'printWaybills')
                    ->name('shippings.printWaybill');
                Route::get('/create-shipping', 'create')
                    ->name('shippings.create');
                Route::post('/create-shipping', 'store')
                    ->name('shippings.store');
                Route::get('/shipping-companies', 'shippingCompanies')
                    ->name('shippings.companies');
                Route::get('/shippings/{id}', 'show')
                    ->name('shippings.show');
                Route::get('/shippings/delete/{id}', 'delete')
                    ->name('shippings.delete');
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
                Route::delete('/notifications/delete/{notification}', 'delete')
                    ->name('notifications.delete');
                Route::get('/notifications/delete-all', 'deleteAll')
                    ->name('notifications.deleteAll');
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
                Route::get('/recievers/getCitiesByCompanyAndCountry/{shippingCompanyId}', 'getCitiesByCompanyAndCountry')
                    ->name('recievers.getCitiesByCompanyAndCountry');
            });

        Route::controller(SenderController::class)
            ->group(function () {
                Route::get('/senders', 'index')
                    ->name('senders.index');
                Route::get('/senders/create', 'create')
                    ->name('senders.create');
                Route::post('/senders/store', 'store')
                    ->name('senders.store');
                Route::get('/senders/edit/{sender}', 'edit')
                    ->name('senders.edit');
                Route::post('/senders/update/{sender}', 'update')
                    ->name('senders.update');
                Route::delete('/senders/delete/{sender}', 'delete')
                    ->name('senders.delete');
                Route::get('/senders/search', 'search')
                    ->name('senders.search');
                Route::get('/senders/getSenders', 'getSenders')
                    ->name('senders.getSenders');
                Route::get('/senders/getCitiesByCompanyAndCountry/{shippingCompanyId}', 'getCitiesByCompanyAndCountry')
                    ->name('senders.getCitiesByCompanyAndCountry');
                Route::get('/senders/{sender}', 'show')
                    ->name('senders.show');
            });

        Route::controller(UserDescriptionController::class)
            ->group(function () {
                Route::get('/user-descriptions', 'index')
                    ->name('user-descriptions.index');
                Route::get('/user-descriptions/create', 'create')
                    ->name('user-descriptions.create');
                Route::post('/user-descriptions/store', 'store')
                    ->name('user-descriptions.store');
                Route::get('/user-descriptions/edit/{userDescription}', 'edit')
                    ->name('user-descriptions.edit');
                Route::post('/user-descriptions/update/{userDescription}', 'update')
                    ->name('user-descriptions.update');
                Route::delete('/user-descriptions/delete/{userDescription}', 'delete')
                    ->name('user-descriptions.delete');
                Route::get('/user-descriptions/getUserDescriptions', 'getUserDescriptions')
                    ->name('user-descriptions.getUserDescriptions');
            });
    });
});
