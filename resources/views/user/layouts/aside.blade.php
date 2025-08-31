<div class="sidebar-wrapper sidebar-theme">

    <nav id="compactSidebar">

        <div class="theme-logo">
            <a href="{{ route('user.dashboard.index') }}">
                <img src="{{ displayImage(app('settings')['logo']) }}" class="navbar-logo" alt="logo">
            </a>
        </div>

        <ul class="menu-categories">

            <li class=" {{ isRoute(['user.dashboard.index']) ? 'active' : '' }}">
                <a href="{{ route('user.dashboard.index') }}"
                   data-active="{{ isRoute(['user.dashboard.index']) ? 'true' : 'false' }}" class="menu-toggle">
                    <div class="menu ">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-home">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <p class="side_links_theme">{{__('admin.dashboard')}}</p>
                        </div>
                    </div>
                </a>
            </li>


            <li class="menu {{ isRoute(['user.shippings.index']) ? 'active' : '' }}">
                <a href="{{ route('user.shippings.index') }}"
                   data-active="{{ isRoute(['user.shippings.index']) ? 'true' : 'false' }}" class="menu-toggle">
                    <div class="">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-home">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <p class="side_links_theme">{{__('admin.new_shipment')}}</p>
                        </div>
                    </div>
                </a>
            </li>






            <li class="menu {{ isRoute([
    'user.transactions.index',
    'user.transactions.create',
])
    ? 'active'
    : '' }}">
                <a href="#transactions_settings" data-active="{{ isRoute([
    'user.transactions.index',
    'user.transactions.create',
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
                            <p class="side_links_theme">{{__('admin.transactions')}}</p>
                        </div>
                    </div>
                </a>
            </li>

            <li class="menu {{ isRoute([
    'user.shippings.index',
    'user.shippings.create',
])
    ? 'active'
    : '' }}">
                <a href="#shippings_settings" data-active="{{ isRoute([
    'user.shippings.index',
    'user.shippings.create',
])
    ? 'true'
    : 'false' }}" class="menu-toggle">
                    <div class="base-menu">
                        <div class="base-icons">
                            <i class="fa fa-shipping-fast" style="color:#fff;font-size:18px;"></i>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-truck">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <p class="side_links_theme">{{__('admin.shippings')}}</p>
                        </div>
                    </div>
                </a>
            </li>
        </ul>

    </nav>

    <div id="compact_submenuSidebar" class="submenu-sidebar">

        <div class="theme-brand-name">
            <a href="{{ route('user.dashboard.index') }}">{{ app('settings')['app_name_' . assetLang()]  }}</a>
        </div>


        <div class="submenu" id="transactions_settings">
            <div class="category-info">
                <h5>{{ __('admin.transactions') }}</h5>
            </div>
            <ul class="submenu-list" data-parent-element="#transactions_settings">
                <li class="{{ isRoute(['user.transactions.index', 'user.transactions.create']) ? 'active' : '' }}">
                    <a href="{{ route('user.transactions.index') }}">
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
            </ul>
        </div>

        <div class="submenu" id="shippings_settings">
            <div class="category-info">
                <h5>{{ __('admin.shippings') }}</h5>
            </div>
            <ul class="submenu-list" data-parent-element="#shippings_settings">
                <li class="{{ isRoute(['user.shippings.index', 'user.shippings.create']) ? 'active' : '' }}">
                    <a href="{{ route('user.shippings.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="feather feather-users">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        {{ __('admin.shippings') }} </a>
                </li>
            </ul>
        </div>

    </div>

</div>
