<div class="sidebar-wrapper sidebar-theme">

    <nav id="compactSidebar">

        <div class="theme-logo">
            <a href="{{ route('admin.dashboard.index') }}">
                <img src="{{ displayImage(app('settings')['logo']) }}" class="navbar-logo" alt="logo">
            </a>
        </div>

        <ul class="menu-categories">
            <li class=" {{ isRoute(['admin.dashboard.index']) ? 'menu active' : '' }}">
                <a href="{{ route('admin.dashboard.index') }}"
                   data-active="{{ isRoute(['admin.dashboard.index']) ? 'true' : 'false' }}" class="menu-toggle">
                    <div class="base-icons">
                        <i class="fa fa-dashboard" style="color:#1b6aab;font-size:35px;margin-bottom:10px;"></i>
                        <p class="side_links_theme">{{__('admin.dashboard')}}</p>
                    </div>
                </a>
            </li>

            @if (
            auth('admin')->user()->hasAnyPermission([
            'admins.view',
            'users.view',
            'wallet_logs.view',
            ])
            )
                <li class="menu {{ isRoute([
                    'admin.admins.index',
                    'admin.admins.search',
                    'admin.admins.create',
                    'admin.admins.edit',
                    'admin.users.index',
                    'admin.users.search',
                    'admin.users.create',
                    'admin.users.edit',
                    'admin.wallet-logs.index',
                    'admin.user-shipping-prices.index',
                    'admin.user-shipping-prices.create',
                    'admin.user-shipping-prices.edit',
                ])
                    ? 'active'
                    : '' }}">
                    <a href="#users_settings" data-active="{{ isRoute([
                    'admin.admins.index',
                    'admin.admins.search',
                    'admin.admins.create',
                    'admin.admins.edit',
                    'admin.users.index',
                    'admin.users.search',
                    'admin.users.create',
                    'admin.users.edit',
                    'admin.wallet-logs.index',
                    'admin.user-shipping-prices.index',
                    'admin.user-shipping-prices.create',
                    'admin.user-shipping-prices.edit',
                ])
                    ? 'true'
                    : 'false' }}" class="menu-toggle">
                        <div class="base-menu">
                            <div class="base-icons">
                                <i class="fa fa-users" style="color:#1b6aab;font-size:35px;margin-bottom:10px;"></i>
                                <p class="side_links_theme">{{__('admin.users_settings')}}</p>
                            </div>
                        </div>
                    </a>
                </li>
            @endif

            @if (
            auth('admin')->user()->hasAnyPermission([
            'banks.view',
            'transactions.view',
            'allowed_companies.view',
            ])
            )
                <li class="menu {{ isRoute([
                    'admin.banks.index',
                    'admin.banks.search',
                    'admin.banks.create',
                    'admin.banks.edit',
                    'admin.transactions.index',
                    'admin.transactions.search',
                    'admin.allowed-companies.index',
                ])
                    ? 'active'
                    : '' }}">
                    <a href="#banks_settings" data-active="{{ isRoute([
                    'admin.banks.index',
                    'admin.banks.search',
                    'admin.banks.create',
                    'admin.banks.edit',
                    'admin.transactions.index',
                    'admin.transactions.search',
                    'admin.allowed-companies.index',
                ])
                    ? 'true'
                    : 'false' }}" class="menu-toggle">
                        <div class="base-menu">
                            <div class="base-icons">
                                <i class="fa fa-bank" style="color:#1b6aab;font-size:35px;margin-bottom:10px;"></i>
                                <p class="side_links_theme">{{__('admin.banks_settings')}}</p>
                            </div>
                        </div>
                    </a>
                </li>
            @endif

            @if (
            auth('admin')->user()->hasAnyPermission([
            'settings.view',
            'roles.view',
            'sliders.view',
            'about.view',
            'about-items.view',
            'admin_settings.update',
            'partners.view',
            'services.view',
            'testimonials.view',
            'contacts.view',
            ])
            )
                <li class="menu {{ isRoute([
                    'admin.settings.index',
                    'admin.roles.index',
                    'admin.roles.search',
                    'admin.roles.create',
                    'admin.roles.edit',
                    'admin.sliders.search',
                    'admin.sliders.create',
                    'admin.sliders.edit',
                    'admin.sliders.index',
                    'admin.about.index',
                    'admin.about.edit',
                    'admin.about.update-item',
                    'admin.admin-settings.index',
                    'admin.partners.index',
                    'admin.partners.create',
                    'admin.partners.edit',
                    'admin.services.index',
                    'admin.services.edit',
                    'admin.services.create',
                    'admin.testimonials.index',
                    'admin.testimonials.create',
                    'admin.testimonials.edit',
                    'admin.contacts.index',
                ]) ? 'active' : '' }}">
                    <a href="#settings" data-active="{{ isRoute([
                    'admin.settings.index',
                    'admin.roles.index',
                    'admin.roles.search',
                    'admin.roles.create',
                    'admin.roles.edit',
                    'admin.sliders.search',
                    'admin.sliders.create',
                    'admin.sliders.edit',
                    'admin.sliders.index',
                    'admin.about.index',
                    'admin.about.edit',
                    'admin.about.update-item',
                    'admin.admin-settings.index',
                    'admin.partners.index',
                    'admin.partners.create',
                    'admin.partners.edit',
                    'admin.services.index',
                    'admin.services.edit',
                    'admin.services.create',
                    'admin.testimonials.index',
                    'admin.testimonials.create',
                    'admin.testimonials.edit',
                    'admin.contacts.index',
                ]) ? 'true' : 'false' }}" class="menu-toggle">
                        <div class="base-menu">
                            <div class="base-icons">
                                <i class="fa fa-gear" style="color:#1b6aab;font-size:35px;margin-bottom:10px;"></i>
                                <p class="side_links_theme">{{__('admin.manage_site')}}</p>
                            </div>
                        </div>
                    </a>
                </li>
            @endif
        </ul>

    </nav>

    <div id="compact_submenuSidebar" class="submenu-sidebar">

        <div class="theme-brand-name">
            <a href="{{ route('admin.dashboard.index') }}">{{ app('settings')['app_name_' . assetLang()]  }}</a>
        </div>

        @if (
        auth('admin')->user()->hasAnyPermission([
        'admins.view',
        'users.view',
        'wallet_logs.view',
        'user_shipping_prices.view',
        ])
        )
            <div class="submenu" id="users_settings">
                <div class="category-info">
                    <h5>{{ __('admin.users_settings') }}</h5>
                </div>
                <ul class="submenu-list" data-parent-element="#users_settings">
                    @haspermission('admins.view', 'admin')
                    <li
                        class="{{ isRoute(['admin.admins.index', 'admin.admins.search', 'admin.admins.create', 'admin.admins.edit']) ? 'active' : '' }}">
                        <a href="{{ route('admin.admins.index') }}">
                            <i class="fa fa-user-shield" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                            {{ __('admin.admins') }} </a>
                    </li>
                    @endhaspermission

                    @haspermission('users.view', 'admin')
                    <li
                        class="{{ isRoute(['admin.users.index', 'admin.users.search', 'admin.users.create', 'admin.users.edit', 'admin.wallet-logs.index', 'admin.user-shipping-prices.index', 'admin.user-shipping-prices.create', 'admin.user-shipping-prices.edit']) ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}">
                            <i class="fa fa-user" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                            {{ __('admin.users') }} </a>
                    </li>
                    @endhaspermission
                </ul>
            </div>
        @endif

        @if (
        auth('admin')->user()->hasAnyPermission([
        'banks.view',
        'allowed_companies.view',
        'transactions.view',
        ])
        )
            <div class="submenu" id="banks_settings">
                <div class="category-info">
                    <h5>{{ __('admin.banks_settings') }}</h5>
                </div>
                <ul class="submenu-list" data-parent-element="#banks_settings">
                    @haspermission('banks.view', 'admin')
                    <li
                        class="{{ isRoute(['admin.banks.index', 'admin.banks.search', 'admin.banks.create', 'admin.banks.edit']) ? 'active' : '' }}">
                        <a href="{{ route('admin.banks.index') }}">
                            <i class="fa fa-bank" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                            {{ __('admin.banks') }} </a>
                    </li>
                    @endhaspermission
                    @haspermission('transactions.view', 'admin')
                    <li class="{{ isRoute(['admin.transactions.index', 'admin.transactions.search']) ? 'active' : '' }}">
                        <a href="{{ route('admin.transactions.index') }}">
                            <i class="fa fa-money-bill-trend-up" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                            {{ __('admin.recharge_requests') }} </a>
                    </li>
                    @endhaspermission
                    @haspermission('allowed_companies.view', 'admin')
                    <li class="{{ isRoute(['admin.allowed-companies.index']) ? 'active' : '' }}">
                        <a href="{{ route('admin.allowed-companies.index') }}">
                            <i class="fa fa-check" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                            {{ __('admin.allowed_companies') }} </a>
                    </li>
                    @endhaspermission
                </ul>
            </div>
        @endif

        @if (
        auth('admin')->user()->hasAnyPermission([
        'settings.update',
        'admin_settings.update',
        'roles.view',
        'sliders.view',
        'about.view',
        'about-items.view',
        'partners.view',
        'services.view',
        'testimonials.view',
        'contacts.view',
        ])
        )
            <div class="submenu" id="settings">
                <div class="category-info">
                    <h5>{{ __('admin.manage_site') }}</h5>
                </div>
                <ul class="submenu-list" data-parent-element="#settings">
                    @haspermission('roles.view', 'admin')
                    <li
                        class="{{ isRoute(['admin.roles.index', 'admin.roles.search', 'admin.roles.create', 'admin.roles.edit']) ? 'active' : '' }}">
                        <a href="{{ route('admin.roles.index') }}">
                            <i class="fa fa-shield-alt" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                            {{ __('admin.roles') }} </a>
                    </li>
                    @endhaspermission
                    @haspermission('settings.update', 'admin')
                    <li class="{{ isRoute(['admin.settings.index']) ? 'active' : '' }}">
                        <a href="{{ route('admin.settings.index') }}">
                            <i class="fa fa-gear" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                            {{ __('admin.settings') }} </a>
                    </li>
                    @endhaspermission
                    @haspermission('admin_settings.update', 'admin')
                    <li class="{{ isRoute(['admin.admin-settings.index']) ? 'active' : '' }}">
                        <a href="{{ route('admin.admin-settings.index') }}">
                            <i class="fa fa-gears" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                            {{ __('admin.admin_shipments_settings') }} </a>
                    </li>
                    @endhaspermission
                    @haspermission('sliders.view', 'admin')
                    <li
                        class="{{ isRoute(['admin.sliders.index', 'admin.sliders.search', 'admin.sliders.create', 'admin.sliders.edit']) ? 'active' : '' }}">
                        <a href="{{ route('admin.sliders.index') }}">
                            <i class="fa fa-images" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                            {{ __('admin.sliders') }} </a>
                    </li>
                    @endhaspermission
                    @haspermission('about.view', 'admin')
                    <li
                        class="{{ isRoute(['admin.about.index', 'admin.about.edit', 'admin.about.update-item']) ? 'active' : '' }}">
                        <a href="{{ route('admin.about.index') }}">
                            <i class="fa fa-info" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                            {{ __('admin.about') }} </a>
                    </li>
                    @endhaspermission
                    @haspermission('partners.view', 'admin')
                    <li
                        class="{{ isRoute(['admin.partners.index', 'admin.partners.edit', 'admin.partners.create']) ? 'active' : '' }}">
                        <a href="{{ route('admin.partners.index') }}">
                            <i class="fa fa-user-friends" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                            {{ __('admin.partners') }} </a>
                    </li>
                    @endhaspermission
                    @haspermission('services.view', 'admin')
                    <li
                        class="{{ isRoute(['admin.services.index', 'admin.services.edit', 'admin.services.create']) ? 'active' : '' }}">
                        <a href="{{ route('admin.services.index') }}">
                            <i class="fa fa-list" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                            {{ __('admin.services') }} </a>
                    </li>
                    @endhaspermission
                    @haspermission('testimonials.view', 'admin')
                    <li
                        class="{{ isRoute(['admin.testimonials.index', 'admin.testimonials.edit', 'admin.testimonials.create']) ? 'active' : '' }}">
                        <a href="{{ route('admin.testimonials.index') }}">
                            <i class="fa fa-star" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                            {{ __('admin.testimonials') }} </a>
                    </li>
                    @endhaspermission
                    @haspermission('contacts.view', 'admin')
                    <li class="{{ isRoute(['admin.contacts.index']) ? 'active' : '' }}">
                        <a href="{{ route('admin.contacts.index') }}">
                            <i class="fa fa-envelope" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                            {{ __('admin.contacts') }} </a>
                    </li>
                    @endhaspermission
                </ul>
            </div>
        @endif

    </div>

</div>
