<div class="sidebar-wrapper sidebar-theme">

    <nav id="compactSidebar">

        <div class="theme-logo">
            <a href="{{ route('user.dashboard.index') }}">
                <img src="{{ displayImage(app('settings')['logo']) }}" class="navbar-logo" alt="logo">
            </a>
        </div>

        <ul class="menu-categories">

            <li class=" {{ isRoute(['user.dashboard.index']) ? 'menu active' : '' }}">
                <a href="{{ route('user.dashboard.index') }}" class="menu-toggle">
                    <div class="base-icons">
                        <i class="fa fa-dashboard" style="color:#1b6aab;font-size:35px;margin-bottom:10px;"></i>
                        <p class="side_links_theme">{{__('admin.dashboard')}}</p>
                    </div>
                </a>
            </li>


            <li class=" {{ isRoute(['user.shippings.create']) ? 'menu active' : '' }}">
                <a href="{{ route('user.shippings.create') }}"
                    data-active="{{ isRoute(['user.shippings.create']) ? 'true' : 'false' }}" class="menu-toggle">
                    <div class="base-icons">
                        <i class="fa fa-truck-arrow-right" style="color:#1b6aab;font-size:35px;margin-bottom:10px;"></i>
                        <p class="side_links_theme">{{__('admin.new_shipment')}}</p>
                    </div>
                </a>
            </li>

            <li class=" {{ isRoute(['user.recievers.index','user.recievers.create','user.recievers.edit','user.recievers.search']) ? 'menu active' : '' }}">
                <a href="{{ route('user.recievers.index') }}"
                    data-active="{{ isRoute(['user.recievers.index','user.recievers.create','user.recievers.edit','user.recievers.search']) ? 'true' : 'false' }}" class="menu-toggle">
                    <div class="base-icons">
                        <i class="fa fa-users" style="color:#1b6aab;font-size:35px;margin-bottom:10px;"></i>
                        <p class="side_links_theme">{{__('admin.recievers')}}</p>
                    </div>
                </a>
            </li>

            <li class="menu {{ isRoute([
    'user.transactions.index',
    'user.transactions.create',
    'user.wallet-logs.index',
    'user.banks.index',
    'user.notifications.index',
    'user.contacts.index',
])
    ? 'active'
    : '' }}">
                <a href="#transactions_settings" data-active="{{ isRoute([
    'user.transactions.index',
    'user.transactions.create',
    'user.wallet-logs.index',
    'user.banks.index',
    'user.notifications.index',
    'user.contacts.index',
])
    ? 'true'
    : 'false' }}" class="menu-toggle">
                    <div class="base-menu">
                        <div class="base-icons">
                            <i class="fa fa-money-bill-trend-up" style="color:#1b6aab;font-size:35px;margin-bottom:10px;"></i>
                            <p class="side_links_theme">{{__('admin.user_transactions')}}</p>
                        </div>
                    </div>
                </a>
            </li>

            <li class="menu {{ isRoute(['user.shippings.index']) ? 'active' : '' }}">
                <a href="#shippings_settings" data-active="{{ isRoute(['user.shippings.index',])? 'true' : 'false' }}" class="menu-toggle">
                    <div class="base-menu">
                        <div class="base-icons">
                            <i class="fa fa-shipping-fast" style="color:#1b6aab;font-size:35px;margin-bottom:10px;"></i>
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
                <li class="{{ isRoute(['user.banks.index']) ? 'active' : '' }}" style="margin-bottom:5px;">
                    <a href="{{ route('user.banks.index') }}">
                        <i class="fa fa-bank" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                        {{ __('admin.banks') }} </a>
                </li>
                <li class="{{ isRoute(['user.transactions.create']) ? 'active' : '' }}" style="margin-bottom:5px;">
                    <a href="{{ route('user.transactions.create') }}">
                        <i class="fa fa-money-check-alt" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                        {{ __('admin.recharge_request') }} </a>
                </li>
                <li class="{{ isRoute(['user.transactions.index']) ? 'active' : '' }}" style="margin-bottom:5px;">
                    <a href="{{ route('user.transactions.index') }}">
                        <i class="fa fa-arrows-alt-h" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                        {{ __('admin.bank_transfer_transactions') }} </a>
                </li>
                <li class="{{ isRoute(['user.wallet-logs.index']) ? 'active' : '' }}" style="margin-bottom:5px;">
                    <a href="{{ route('user.wallet-logs.index') }}">
                        <i class="fa fa-money-bill-trend-up" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                        {{ __('admin.wallet_logs') }} </a>
                </li>
                <li class="{{ isRoute(['user.notifications.index']) ? 'active' : '' }}">
                    <a href="{{ route('user.notifications.index') }}">
                        <i class="fa fa-money-bill-trend-up" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                        {{ __('admin.notifications') }} </a>
                </li>
                <li class="{{ isRoute(['user.contacts.index']) ? 'active' : '' }}">
                    <a href="{{ route('user.contacts.index') }}">
                        <i class="fa fa-money-bill-trend-up" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                        {{ __('admin.contacts_details') }} </a>
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