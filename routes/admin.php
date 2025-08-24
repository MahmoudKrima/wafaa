<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\Bank\BankController;
use App\Http\Controllers\Admin\Home\HomeController;
use App\Http\Controllers\Admin\Contact\ContactController;
use App\Http\Controllers\Admin\Partner\PartnerController;
use App\Http\Controllers\Admin\Profile\ProfileController;
use App\Http\Controllers\Admin\Service\ServiceController;
use App\Http\Controllers\Admin\UserSettings\UserController;
use App\Http\Controllers\Admin\UserSettings\AdminController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Admin\WebSiteSettings\RoleController;
use App\Http\Controllers\Admin\Testimonial\TestimonialController;
use App\Http\Controllers\Admin\Transaction\TransactionController;
use App\Http\Controllers\Admin\FrontSetting\About\AboutController;
use App\Http\Controllers\Admin\WebSiteSettings\SettingsController;
use App\Http\Controllers\Admin\FrontSettings\Slider\SliderController;
use App\Http\Controllers\Admin\WebSiteSettings\AdminSettingsController;
use App\Http\Controllers\Admin\UserShippingPrice\UserShippingPriceController;
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['web'])->group(function () {
    Route::controller(AuthController::class)
        ->as('admin.')
        ->prefix(LaravelLocalization::setLocale() . '/admin')
        ->group(function () {
            Route::get('/', 'loginForm')
                ->name('auth.loginForm')
                ->middleware('guest.admin');
            Route::get('/login', 'loginForm')
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

    // Admin Protected Routes (with /admin prefix)
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
                Route::get('/wallet-logs/{user}', 'walletLogs')
                    ->name('wallet_logs.index')
                    ->middleware('has.permission:wallet_logs.view');
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

        Route::controller(BankController::class)
            ->group(function () {
                Route::get('/banks', 'index')
                    ->name('banks.index')
                    ->middleware('has.permission:banks.view');
                Route::get('/banks-search', 'search')
                    ->name('banks.search')
                    ->middleware('has.permission:banks.view');
                Route::get('/banks-create', 'create')
                    ->name('banks.create')
                    ->middleware('has.permission:banks.create');
                Route::post('/banks-store', 'store')
                    ->name('banks.store')
                    ->middleware('has.permission:banks.create');
                Route::get('/banks-edit/{bank}', 'edit')
                    ->name('banks.edit')
                    ->middleware('has.permission:banks.update');
                Route::post('/banks-update/{bank}', 'update')
                    ->name('banks.update')
                    ->middleware('has.permission:banks.update');
                Route::post('/banks-update-status/{bank}', 'updateStatus')
                    ->name('banks.updateStatus')
                    ->middleware('has.permission:banks.update');
                Route::delete('/banks-delete/{bank}', 'delete')
                    ->name('banks.delete')
                    ->middleware('has.permission:banks.delete');
            });

        Route::controller(TransactionController::class)
            ->group(function () {
                Route::get('/transactions', 'index')
                    ->name('transactions.index')
                    ->middleware('has.permission:transactions.view');
                Route::get('/transactions-search', 'search')
                    ->name('transactions.search')
                    ->middleware('has.permission:transactions.view');
                Route::post('/transactions-update-status/{transaction}', 'updateStatus')
                    ->name('transactions.updateStatus')
                    ->middleware('has.permission:transactions.update');
                Route::delete('/transactions-delete/{transaction}', 'delete')
                    ->name('transactions.delete')
                    ->middleware('has.permission:transactions.delete');
            });

        Route::controller(SliderController::class)
            ->group(function () {
                Route::get('/sliders', 'index')
                    ->name('sliders.index')
                    ->middleware('has.permission:sliders.view');
                Route::get('/sliders-search', 'search')
                    ->name('sliders.search')
                    ->middleware('has.permission:sliders.view');
                Route::get('/sliders/create', 'create')
                    ->name('sliders.create')
                    ->middleware('has.permission:sliders.create');
                Route::post('/sliders/store', 'store')
                    ->name('sliders.store')
                    ->middleware('has.permission:sliders.create');
                Route::get('/sliders/edit/{slider}', 'edit')
                    ->name('sliders.edit')
                    ->middleware('has.permission:sliders.update');
                Route::post('/sliders/update/{slider}', 'update')
                    ->name('sliders.update')
                    ->middleware('has.permission:sliders.update');
                Route::post('/sliders/update-status/{slider}', 'updateStatus')
                    ->name('sliders.updateStatus')
                    ->middleware('has.permission:sliders.update');
                Route::delete('/sliders/delete/{slider}', 'delete')
                    ->name('sliders.delete')
                    ->middleware('has.permission:sliders.delete');
            });

        Route::controller(AboutController::class)
            ->group(function () {
                Route::get('/about', 'index')
                    ->name('about.index')
                    ->middleware('has.permission:about.view');
                Route::get('/about/edit', 'edit')
                    ->name('about.edit')
                    ->middleware('has.permission:about.update');
                Route::post('/about/update-about/{about}', 'updateAbout')
                    ->name('about.update')
                    ->middleware('has.permission:about.update');
                Route::post('/about/update-item/{aboutItem}', 'updateAboutItem')
                    ->name('about.update-item')
                    ->middleware('has.permission:about-items.update');
            });

        Route::controller(UserShippingPriceController::class)
            ->group(function () {
                Route::get('/user-shipping-prices/{user}', 'index')
                    ->name('user-shipping-prices.index')
                    ->middleware('has.permission:user_shipping_prices.view');
                Route::get('/user-shipping-prices/create/{user}', 'create')
                    ->name('user-shipping-prices.create')
                    ->middleware('has.permission:user_shipping_prices.create');
                Route::post('/user-shipping-prices/store/{user}', 'store')
                    ->name('user-shipping-prices.store')
                    ->middleware('has.permission:user_shipping_prices.create');
                Route::get('/user-shipping-prices/edit/{user}/{userShippingPrice}', 'edit')
                    ->name('user-shipping-prices.edit')
                    ->middleware('has.permission:user_shipping_prices.update');
                Route::post('/user-shipping-prices/update/{user}/{userShippingPrice}', 'update')
                    ->name('user-shipping-prices.update')
                    ->middleware('has.permission:user_shipping_prices.update');
                Route::delete('/user-shipping-prices/delete/{userShippingPrice}', 'delete')
                    ->name('user-shipping-prices.delete')
                    ->middleware('has.permission:user_shipping_prices.delete');
            });

        Route::controller(AdminSettingsController::class)
            ->group(function () {
                Route::get('/admin-settings', 'index')
                    ->name('admin-settings.index')
                    ->middleware('has.permission:admin_settings.update');
                Route::post('/admin-settings/update/{adminSetting}', 'update')
                    ->name('admin-settings.update')
                    ->middleware('has.permission:admin_settings.update');
            });

        Route::controller(PartnerController::class)
            ->group(function () {
                Route::get('/partners', 'index')
                    ->name('partners.index')
                    ->middleware('has.permission:partners.view');
                Route::get('/partners/create', 'create')
                    ->name('partners.create')
                    ->middleware('has.permission:partners.create');
                Route::post('/partners/store', 'store')
                    ->name('partners.store')
                    ->middleware('has.permission:partners.create');
                Route::get('/partners/edit/{partner}', 'edit')
                    ->name('partners.edit')
                    ->middleware('has.permission:partners.update');
                Route::post('/partners/update/{partner}', 'update')
                    ->name('partners.update')
                    ->middleware('has.permission:partners.update');
                Route::post('/partners/update-status/{partner}', 'updateStatus')
                    ->name('partners.updateStatus')
                    ->middleware('has.permission:partners.update');
                Route::delete('/partners/delete/{partner}', 'delete')
                    ->name('partners.delete')
                    ->middleware('has.permission:partners.delete');
            });

        Route::controller(ServiceController::class)
            ->group(function () {
                Route::get('/services', 'index')
                    ->name('services.index')
                    ->middleware('has.permission:services.view');
                Route::get('/services/create', 'create')
                    ->name('services.create')
                    ->middleware('has.permission:services.create');
                Route::post('/services/store', 'store')
                    ->name('services.store')
                    ->middleware('has.permission:services.create');
                Route::get('/services/edit/{service}', 'edit')
                    ->name('services.edit')
                    ->middleware('has.permission:services.update');
                Route::post('/services/update/{service}', 'update')
                    ->name('services.update')
                    ->middleware('has.permission:services.update');
                Route::post('/services/update-status/{service}', 'updateStatus')
                    ->name('services.updateStatus')
                    ->middleware('has.permission:services.update');
                Route::delete('/services/delete/{service}', 'delete')
                    ->name('services.delete')
                    ->middleware('has.permission:services.delete');
            });

        Route::controller(TestimonialController::class)
            ->group(function () {
                Route::get('/testimonials', 'index')
                    ->name('testimonials.index')
                    ->middleware('has.permission:testimonials.view');
                Route::get('/testimonials/create', 'create')
                    ->name('testimonials.create')
                    ->middleware('has.permission:testimonials.create');
                Route::post('/testimonials/store', 'store')
                    ->name('testimonials.store')
                    ->middleware('has.permission:testimonials.create');
                Route::get('/testimonials/edit/{testimonial}', 'edit')
                    ->name('testimonials.edit')
                    ->middleware('has.permission:testimonials.update');
                Route::post('/testimonials/update/{testimonial}', 'update')
                    ->name('testimonials.update')
                    ->middleware('has.permission:testimonials.update');
                Route::post('/testimonials/update-status/{testimonial}', 'updateStatus')
                    ->name('testimonials.updateStatus')
                    ->middleware('has.permission:testimonials.update');
                Route::delete('/testimonials/delete/{testimonial}', 'delete')
                    ->name('testimonials.delete')
                    ->middleware('has.permission:testimonials.delete');
            });

            Route::controller(ContactController::class)
            ->group(function () {
                Route::get('/contacts', 'index')
                    ->name('contacts.index')
                    ->middleware('has.permission:contacts.view');
                Route::post('/contacts-reply', 'reply')
                    ->name('contacts.reply')
                    ->middleware('has.permission:contacts.reply');
                Route::delete('/contacts-delete/{contact}', 'delete')
                    ->name('contacts.delete')
                    ->middleware('has.permission:contacts.delete');
            });
    
    });
});
