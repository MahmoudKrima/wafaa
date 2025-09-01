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
                    <div class="base-icons">
                        <i class="fa fa-dashboard" style="color:#fff;font-size:35px;margin-bottom:10px;"></i>
                        <p class="side_links_theme">{{__('admin.dashboard')}}</p>
                    </div>
                </a>
            </li>



            <li class="{{ isRoute(['user.shippings.index','user.shippings.create']) ? 'active' : '' }}">
                <a href="{{ route('user.shippings.index') }}"
                   data-active="{{ isRoute(['user.shippings.create']) ? 'true' : 'false' }}" class="menu-toggle">
                    <div class="base-icons">
                        <i class="fa fa-truck-arrow-right" style="color:#fff;font-size:35px;margin-bottom:10px;"></i>
                        <p class="side_links_theme">{{__('admin.new_shipment')}}</p>
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
                            <i class="fa fa-money-bill-trend-up" style="color:#fff;font-size:35px;margin-bottom:10px;"></i>
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
                            <i class="fa fa-shipping-fast" style="color:#fff;font-size:35px;margin-bottom:10px;"></i>
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
                        <i class="fa fa-money-bill-trend-up" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
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
                        <i class="fa fa-shipping-fast" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                        {{ __('admin.shippings') }} </a>
                </li>
            </ul>
        </div>

    </div>

</div>
