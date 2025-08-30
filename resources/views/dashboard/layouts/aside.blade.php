<div class="sidebar-wrapper sidebar-theme">

    <nav id="compactSidebar">

        <div class="theme-logo">
            <a href="{{ route('admin.dashboard.index') }}">
                <img src="{{ displayImage(app('settings')['logo']) }}" class="navbar-logo" alt="logo">
            </a>
        </div>

        <ul class="menu-categories">
            <li class=" {{ isRoute(['admin.dashboard.index']) ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard.index') }}"
                    data-active="{{ isRoute(['admin.dashboard.index']) ? 'true' : 'false' }}" class="menu-toggle">
                    <div class="">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-home">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        </div>
                    </div>
                </a>
                <div class="tooltip"><span>{{ __('admin.dashboard') }}</span></div>
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
                    'admin.wallet_logs.index',
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
                    'admin.wallet_logs.index',
                    'admin.user-shipping-prices.index',
                    'admin.user-shipping-prices.create',
                    'admin.user-shipping-prices.edit',
                ])
                    ? 'true'
                    : 'false' }}" class="menu-toggle">
                    <div class="base-menu">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-users">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                    </div>
                </a>
                <div class="tooltip"><span>{{ __('admin.users_settings') }}</span></div>
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-users">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                    </div>
                </a>
                <div class="tooltip"><span>{{ __('admin.banks_settings') }}</span></div>
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-settings">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path
                                    d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </a>
                <div class="tooltip"><span>{{ __('admin.manage_site') }}</span></div>
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-users">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        {{ __('admin.admins') }} </a>
                </li>
                @endhaspermission

                @haspermission('users.view', 'admin')
                <li
                    class="{{ isRoute(['admin.users.index', 'admin.users.search', 'admin.users.create', 'admin.users.edit', 'admin.wallet_logs.index', 'admin.user-shipping-prices.index', 'admin.user-shipping-prices.create', 'admin.user-shipping-prices.edit']) ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-user">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-users">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        {{ __('admin.banks') }} </a>
                </li>
                @endhaspermission
                @haspermission('transactions.view', 'admin')
                <li class="{{ isRoute(['admin.transactions.index', 'admin.transactions.search']) ? 'active' : '' }}">
                    <a href="{{ route('admin.transactions.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-users">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        {{ __('admin.transactions') }} </a>
                </li>
                @endhaspermission
                @haspermission('allowed_companies.view', 'admin')
                <li class="{{ isRoute(['admin.allowed-companies.index']) ? 'active' : '' }}">
                    <a href="{{ route('admin.allowed-companies.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-users">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-shield">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        {{ __('admin.roles') }} </a>
                </li>
                @endhaspermission
                @haspermission('settings.update', 'admin')
                <li class="{{ isRoute(['admin.settings.index']) ? 'active' : '' }}">
                    <a href="{{ route('admin.settings.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-settings">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path
                                d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                            </path>
                        </svg>
                        {{ __('admin.settings') }} </a>
                </li>
                @endhaspermission
                @haspermission('admin_settings.update', 'admin')
                <li class="{{ isRoute(['admin.admin-settings.index']) ? 'active' : '' }}">
                    <a href="{{ route('admin.admin-settings.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-settings">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path
                                d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                            </path>
                        </svg>
                        {{ __('admin.admin_settings') }} </a>
                </li>
                @endhaspermission
                @haspermission('sliders.view', 'admin')
                <li
                    class="{{ isRoute(['admin.sliders.index', 'admin.sliders.search', 'admin.sliders.create', 'admin.sliders.edit']) ? 'active' : '' }}">
                    <a href="{{ route('admin.sliders.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-shield">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        {{ __('admin.sliders') }} </a>
                </li>
                @endhaspermission
                @haspermission('about.view', 'admin')
                <li
                    class="{{ isRoute(['admin.about.index', 'admin.about.edit', 'admin.about.update-item']) ? 'active' : '' }}">
                    <a href="{{ route('admin.about.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-shield">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        {{ __('admin.about') }} </a>
                </li>
                @endhaspermission
                @haspermission('partners.view', 'admin')
                <li
                    class="{{ isRoute(['admin.partners.index', 'admin.partners.edit', 'admin.partners.create']) ? 'active' : '' }}">
                    <a href="{{ route('admin.partners.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-bookmark">
                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                        </svg>
                        {{ __('admin.partners') }} </a>
                </li>
                @endhaspermission
                @haspermission('services.view', 'admin')
                <li
                    class="{{ isRoute(['admin.services.index', 'admin.services.edit', 'admin.services.create']) ? 'active' : '' }}">
                    <a href="{{ route('admin.services.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-shopping-bag">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                        {{ __('admin.services') }} </a>
                </li>
                @endhaspermission
                @haspermission('testimonials.view', 'admin')
                <li
                    class="{{ isRoute(['admin.testimonials.index', 'admin.testimonials.edit', 'admin.testimonials.create']) ? 'active' : '' }}">
                    <a href="{{ route('admin.testimonials.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-edit">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        {{ __('admin.testimonials') }} </a>
                </li>
                @endhaspermission
                @haspermission('contacts.view', 'admin')
                <li class="{{ isRoute(['admin.contacts.index']) ? 'active' : '' }}">
                    <a href="{{ route('admin.contacts.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-message-square">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                        {{ __('admin.contacts') }} </a>
                </li>
                @endhaspermission
            </ul>
        </div>
        @endif

    </div>

</div>