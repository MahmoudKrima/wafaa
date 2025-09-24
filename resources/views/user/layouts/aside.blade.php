<div class="sidebar-wrapper sidebar-theme">

    <nav id="compactSidebar">

        <div class="theme-logo">
            <a href="{{ route('user.dashboard.index') }}">
                <img src="{{ displayImage(app('settings')['logo']) }}" class="navbar-logo" alt="logo">
            </a>
        </div>

        <ul class="menu-categories">

            <li class="{{ isRoute(['user.dashboard.index']) ? 'active' : '' }}">
                <a href="{{ route('user.dashboard.index') }}" class="menu-toggle">
                    <div class="base-icons">
                        <i class="fa fa-dashboard" style="color:#1b6aab;font-size:35px;margin-bottom:10px;"></i>
                        <p class="side_links_theme">{{__('admin.dashboard')}}</p>
                    </div>
                </a>
            </li>


            <li class="{{ isRoute(['user.shippings.create']) ? 'active' : '' }}">
                <a href="{{ route('user.shippings.create') }}"
                    data-active="{{ isRoute(['user.shippings.create']) ? 'true' : 'false' }}" class="menu-toggle">
                    <div class="base-icons">
                        <i class="fa fa-cart-flatbed" style="color:#1b6aab;font-size:35px;margin-bottom:10px;"></i>
                        <p class="side_links_theme">{{__('admin.new_shipment')}}</p>
                    </div>
                </a>
            </li>

            <li class="{{ isRoute(['user.shippings.index']) ? 'active' : '' }}">
                <a href="{{ route('user.shippings.index') }}"
                    data-active="{{ isRoute(['user.shippings.index']) ? 'true' : 'false' }}" class="menu-toggle">
                    <div class="base-icons">
                        <i class="fa fa fa-shipping-fast" style="color:#1b6aab;font-size:35px;margin-bottom:10px;"></i>
                        <p class="side_links_theme">{{__('admin.shippings')}}</p>
                    </div>
                </a>
            </li>

            <!-- <li class="{{ isRoute(['user.recievers.index','user.recievers.create','user.recievers.edit','user.recievers.search']) ? 'active' : '' }}">
                <a href="{{ route('user.recievers.index') }}"
                    data-active="{{ isRoute(['user.recievers.index','user.recievers.create','user.recievers.edit','user.recievers.search']) ? 'true' : 'false' }}" class="menu-toggle">
                    <div class="base-icons">
                        <i class="fa fa-users" style="color:#1b6aab;font-size:35px;margin-bottom:10px;"></i>
                        <p class="side_links_theme">{{__('admin.recievers')}}</p>
                    </div>
                </a>
            </li> -->

            <li class="menu {{ isRoute([
    'user.senders.index',
    'user.senders.create',
    'user.senders.edit',
    'user.senders.search',
   'user.recievers.index',
   'user.recievers.create',
   'user.recievers.edit',
   'user.recievers.search',
   'user.user-descriptions.index',
   'user.user-descriptions.create',
   'user.user-descriptions.edit',
   'user.user-descriptions.search'

])
    ? 'active'
    : '' }}">
                <a href="#shippings_settings" data-active="{{ isRoute([
    'user.senders.index',
    'user.senders.create',
    'user.senders.edit',
    'user.senders.search',
   'user.recievers.index',
   'user.recievers.create',
   'user.recievers.edit',
   'user.recievers.search',
   'user.user-descriptions.index',
   'user.user-descriptions.create',
   'user.user-descriptions.edit',
   'user.user-descriptions.search'
])
    ? 'true'
    : 'false' }}" class="menu-toggle">
                    <div class="base-menu">
                        <div class="base-icons">
                            <i class="fa fa-arrows-split-up-and-left" style="color:#1b6aab;font-size:35px;margin-bottom:10px;"></i>
                            <p class="side_links_theme">{{__('admin.shipping_settings_center')}}</p>
                        </div>
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


        </ul>

    </nav>

    <div id="compact_submenuSidebar" class="submenu-sidebar">

        <div class="submenu" id="shippings_settings">
            <div class="category-info">
                <h5>{{ __('admin.shipping_settings_center') }}</h5>
            </div>
            <ul class="submenu-list" data-parent-element="#shippings_settings">
                <li class="{{ isRoute(['user.senders.index']) ? 'active' : '' }}" style="margin-bottom:5px;">
                    <a href="{{ route('user.senders.index') }}">
                        <i class="fa fa-users" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                        {{ __('admin.senders') }} </a>
                </li>
                <li class="{{ isRoute(['user.recievers.index']) ? 'active' : '' }}" style="margin-bottom:5px;">
                    <a href="{{ route('user.recievers.index') }}">
                        <i class="fa fa-users-cog" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                        {{ __('admin.recievers') }} </a>
                </li>
                <li class="{{ isRoute(['user.user-descriptions.index']) ? 'active' : '' }}" style="margin-bottom:5px;">
                    <a href="{{ route('user.user-descriptions.index') }}">
                        <i class="fa fa-info-circle" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                        {{ __('admin.shipping_descriptions') }} </a>
                </li>
            </ul>
        </div>


    </div>

    <div id="compact_submenuSidebar" class="submenu-sidebar">

        <div class="submenu" id="transactions_settings">
            <div class="category-info">
                <h5>{{ __('admin.user_transactions') }}</h5>
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
                        <i class="fa fa-arrow-right-arrow-left" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                        {{ __('admin.bank_transfer_transactions') }} </a>
                </li>
                <li class="{{ isRoute(['user.wallet-logs.index']) ? 'active' : '' }}" style="margin-bottom:5px;">
                    <a href="{{ route('user.wallet-logs.index') }}">
                        <i class="fa fa-money-bill-trend-up" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                        {{ __('admin.wallet_logs') }} </a>
                </li>
                <li class="{{ isRoute(['user.notifications.index']) ? 'active' : '' }}" style="margin-bottom:5px;">
                    <a href="{{ route('user.notifications.index') }}">
                        <i class="fa fa-bell" style="color:#fe9400;font-size:15px;margin:0 5px;"></i>
                        {{ __('admin.notifications') }} </a>
                </li>
            </ul>
        </div>


    </div>

</div>