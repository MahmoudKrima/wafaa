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
            ])
            )
            <li class="menu {{ isRoute([
                    'admin.banks.index',
                    'admin.banks.search',
                    'admin.banks.create',
                    'admin.banks.edit',
                    'admin.transactions.index',
                    'admin.transactions.search',
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
                    class="{{ isRoute(['admin.users.index', 'admin.users.search', 'admin.users.create', 'admin.users.edit', 'admin.wallet_logs.index']) ? 'active' : '' }}">
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
                <li
                    class="{{ isRoute(['admin.transactions.index', 'admin.transactions.search']) ? 'active' : '' }}">
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
            </ul>
        </div>
        @endif

        @if (
        auth('admin')->user()->hasAnyPermission([
        'settings.update',
        'roles.view',
        'sliders.view',
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
                    class="{{ isRoute(['admin.about.index','admin.about.edit','admin.about.update-item']) ? 'active' : '' }}">
                    <a href="{{ route('admin.about.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-shield">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        {{ __('admin.about') }} </a>
                </li>
                @endhaspermission
            </ul>
        </div>
        @endif

    </div>

</div>