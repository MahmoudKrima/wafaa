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
                                'settings.view',
                                'roles.view',
                                'testimonials.view',
                                'faqs.view',
                                'newsletters.view',
                                'main_section.update',
                                'secondary_section.view',
                                'team.view',
                                'features.view',
                                'features.update',
                                'mission.update',
                                'our_story.update',
                                'messages.view'
                            ])
                        )
                        <li class="menu {{ isRoute([
                    'admin.settings.index',
                    'admin.roles.index',
                    'admin.roles.search',
                    'admin.roles.create',
                    'admin.roles.edit',
                    'admin.testimonials.index',
                    'admin.testimonials.create',
                    'admin.testimonials.edit',
                    'admin.faqs.index',
                    'admin.faqs.create',
                    'admin.faqs.edit',
                    'admin.newsletters.index',
                    'admin.main-section.edit',
                    'admin.secondary-section.index',
                    'admin.secondary-section.edit',
                    'admin.teams.index',
                    'admin.teams.edit',
                    'admin.teams.create',
                    'admin.contact-us.index',
                    'admin.features.edit',
                    'admin.mission.edit',
                    'admin.our-story.edit',
                    'admin.terms.index',
                    'admin.terms.create',
                    'admin.terms.edit'
                ]) ? 'active' : '' }}">
                            <a href="#settings" data-active="{{ isRoute([
                    'admin.settings.index',
                    'admin.roles.index',
                    'admin.roles.search',
                    'admin.roles.create',
                    'admin.roles.edit',
                    'admin.testimonials.index',
                    'admin.testimonials.create',
                    'admin.testimonials.edit',
                    'admin.faqs.index',
                    'admin.faqs.create',
                    'admin.faqs.edit',
                    'admin.newsletters.index',
                    'admin.main-section.edit',
                    'admin.secondary-section.index',
                    'admin.secondary-section.edit',
                    'admin.teams.index',
                    'admin.teams.edit',
                    'admin.teams.create',
                    'admin.contact-us.index',
                    'admin.features.edit',
                    'admin.mission.edit',
                    'admin.our-story.edit',
                    'admin.terms.index',
                    'admin.terms.create',
                    'admin.terms.edit'
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
                        class="{{ isRoute(['admin.users.index', 'admin.users.search', 'admin.users.create', 'admin.users.edit']) ? 'active' : '' }}">
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
                        'settings.update',
                        'roles.view',
                        'testimonials.view',
                        'faqs.view',
                        'terms.view',
                        'newsletters.view',
                        'messages.view',
                        'team.view',
                        'main_section.update',
                        'features.view',
                        'features.update',
                        'mission.update',
                        'our_story.update',
                        'secondary_section.view'
                    ])
                )
                <div class="submenu" id="settings">
                    <div class="category-info">
                        <h5>{{ __('admin.manage_site') }}</h5>
                    </div>
                    <ul class="submenu-list" data-parent-element="#settings">
                        @haspermission('team.view', 'admin')
                        <li class="{{ isRoute([
                'admin.teams.index',
                'admin.teams.create',
                'admin.teams.edit',
            ]) ? 'active' : '' }}">
                            <a href="{{ route('admin.teams.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-users">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                {{ __('admin.team') }} </a>
                        </li>
                        @endhaspermission
                        @haspermission('messages.view', 'admin')
                        <li class="{{ isRoute([
                'admin.contact-us.index',
            ]) ? 'active' : '' }}">
                            <a href="{{ route('admin.contact-us.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-mail">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                    </path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                {{ __('admin.messages') }} </a>
                        </li>
                        @endhaspermission
                        @haspermission('newsletters.view', 'admin')
                        <li class="{{ isRoute([
                'admin.newsletters.index',
            ]) ? 'active' : '' }}">
                            <a href="{{ route('admin.newsletters.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-at-sign">
                                    <circle cx="12" cy="12" r="4"></circle>
                                    <path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"></path>
                                </svg>
                                {{ __('admin.newsletters') }} </a>
                        </li>
                        @endhaspermission
                        @haspermission('faqs.view', 'admin')
                        <li class="{{ isRoute([
                'admin.faqs.index',
                'admin.faqs.create',
                'admin.faqs.edit'
            ]) ? 'active' : '' }}">
                            <a href="{{ route('admin.faqs.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-align-justify">
                                    <line x1="21" y1="10" x2="3" y2="10"></line>
                                    <line x1="21" y1="6" x2="3" y2="6"></line>
                                    <line x1="21" y1="14" x2="3" y2="14"></line>
                                    <line x1="21" y1="18" x2="3" y2="18"></line>
                                </svg>
                                {{ __('admin.faqs') }} </a>
                        </li>
                        @endhaspermission
                        @haspermission('terms.view', 'admin')
                        <li class="{{ isRoute([
                'admin.terms.index',
                'admin.terms.create',
                'admin.terms.edit'
            ]) ? 'active' : '' }}">
                            <a href="{{ route('admin.terms.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-align-justify">
                                    <line x1="21" y1="10" x2="3" y2="10"></line>
                                    <line x1="21" y1="6" x2="3" y2="6"></line>
                                    <line x1="21" y1="14" x2="3" y2="14"></line>
                                    <line x1="21" y1="18" x2="3" y2="18"></line>
                                </svg>
                                {{ __('admin.terms') }} </a>
                        </li>
                        @endhaspermission
                        @haspermission('testimonials.view', 'admin')
                        <li class="{{ isRoute([
                'admin.testimonials.index',
                'admin.testimonials.create',
                'admin.testimonials.edit'
            ]) ? 'active' : '' }}">
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
                        @haspermission('main_section.update', 'admin')
                        <li class="{{ isRoute([
                'admin.main-section.edit'
            ]) ? 'active' : '' }}">
                            <a href="{{ route('admin.main-section.edit', 1) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-edit">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                {{ __('admin.main_section') }} </a>
                        </li>
                        @endhaspermission
                        @haspermission('features.update', 'admin')
                        <li class="{{ isRoute([
                'admin.features.edit'
            ]) ? 'active' : '' }}">
                            <a href="{{ route('admin.features.edit', 1) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-edit">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                {{ __('admin.features') }} </a>
                        </li>
                        @endhaspermission
                        @haspermission('secondary_section.view', 'admin')
                        <li class="{{ isRoute([
                'admin.secondary-section.index',
                'admin.secondary-section.edit'
            ]) ? 'active' : '' }}">
                            <a href="{{ route('admin.secondary-section.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-edit">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                {{ __('admin.secondary_section') }} </a>
                        </li>
                        @endhaspermission
                        @haspermission('mission.update', 'admin')
                        <li class="{{ isRoute([
                'admin.mission.edit',
            ]) ? 'active' : '' }}">
                            <a href="{{ route('admin.mission.edit') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-crosshair">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="22" y1="12" x2="18" y2="12"></line>
                                    <line x1="6" y1="12" x2="2" y2="12"></line>
                                    <line x1="12" y1="6" x2="12" y2="2"></line>
                                    <line x1="12" y1="22" x2="12" y2="18"></line>
                                </svg>
                                {{ __('admin.mission') }} </a>
                        </li>
                        @endhaspermission
                        @haspermission('our_story.update', 'admin')
                        <li class="{{ isRoute([
                'admin.our-story.edit',
            ]) ? 'active' : '' }}">
                            <a href="{{ route('admin.our-story.edit') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-clipboard">
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                                </svg>
                                {{ __('admin.our_story') }} </a>
                        </li>
                        @endhaspermission
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
                    </ul>
                </div>
        @endif

    </div>

</div>