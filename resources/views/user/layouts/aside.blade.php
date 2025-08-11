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

          
        </ul>

    </nav>

    <div id="compact_submenuSidebar" class="submenu-sidebar">

        <div class="theme-brand-name">
            <a href="{{ route('user.dashboard.index') }}">{{ app('settings')['app_name_' . assetLang()]  }}</a>
        </div>

    </div>

</div>