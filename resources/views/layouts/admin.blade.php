@extends('layouts.master')

@section('body-class')
    skin-blue-light sidebar-mini {{ session('sidebar-collapsed') ? 'sidebar-collapse' : '' }}
@endsection

@section('body')
    <div class="wrapper">

        <header class="main-header">
            <span class="logo">
                <span class="logo-mini">
                    <img src="{{ asset('img/app-logo-fav.png') }}" alt="Logo small" width="41" />
                </span>
                <span class="logo-lg">
                    <img src="{{ asset('img/app-logo-sm.png') }}" alt="Logo small" width="200" />
                </span>
            </span>
            <nav class="navbar navbar-static-top">
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li class="{{ route_starts_with('admin.settings.credentials.edit') }}">
                            <a href="{{ route('admin.settings.credentials.edit') }}">
                                <i class="fa fa-gear"></i>
                                @lang('Settings')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}">
                                <i class="fa fa-sign-out"></i>
                                @lang('Log out')
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <aside class="main-sidebar">
            <section class="sidebar">
                <ul class="sidebar-menu">

                    <li class="{{ route_starts_with('admin.dashboard') }}">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                            <span>@lang('Dashboard')</span>
                        </a>
                    </li>

                    @permission('admin.users.index')
                        <li class="{{ route_starts_with('admin.users') }}">
                            <a href="{{ route('admin.users.index') }}">
                                <i class="fa fa-user"></i>
                                <span>@lang('Users')</span>
                            </a>
                        </li>
                    @endpermission

                    @permission('admin.roles.index')
                        <li class="{{ route_starts_with('admin.roles') }}">
                            <a href="{{ route('admin.roles.index') }}">
                                <i class="fa fa-users"></i>
                                <span>@lang('Roles')</span>
                            </a>
                        </li>
                    @endpermission

                    @permission('admin.permissions.index')
                        <li class="{{ route_starts_with('admin.permissions') }}">
                            <a href="{{ route('admin.permissions.index') }}">
                                <i class="fa fa-key"></i>
                                <span>@lang('Permissions')</span>
                            </a>
                        </li>
                    @endpermission

                    @permission('admin.accounts.index')
                        <li class="{{ route_starts_with('admin.accounts') }}">
                            <a href="{{ route('admin.accounts.index') }}">
                                <i class="fa fa-hospital-o"></i>
                                <span>@lang('Accounts')</span>
                            </a>
                        </li>
                    @endpermission

                    @permission('admin.files.index')
                        <li class="{{ route_starts_with('admin.files') }}">
                            <a href="{{ route('admin.files.index') }}">
                                <i class="fa fa-upload"></i>
                                <span>@lang('Files')</span>
                            </a>
                        </li>
                    @endpermission
                        
                    @permission('admin.employees.index')
                        <li class="{{ route_starts_with('admin.employees') }}">
                            <a href="{{ route('admin.employees.index') }}">
                                <i class="fa fa-id-card-o"></i>
                                <span>@lang('Employees')</span>
                            </a>
                        </li>
                    @endpermission
                        
                    @permission('admin.positionTypes.index')
                        <li class="{{ route_starts_with('admin.positionTypes') }}">
                            <a href="{{ route('admin.positionTypes.index') }}">
                                <i class="fa fa-id-badge"></i>
                                <span>@lang('Position Types')</span>
                            </a>
                        </li>
                    @endpermission
                        
                    @permission('admin.people.index')
                        <li class="{{ route_starts_with('admin.people') }}">
                            <a href="{{ route('admin.people.index') }}">
                                <i class="fa fa-male"></i>
                                <span>@lang('People')</span>
                            </a>
                        </li>
                    @endpermission
                        
                    @permission('admin.practices.index')
                        <li class="{{ route_starts_with('admin.practices') }}">
                            <a href="{{ route('admin.practices.index') }}">
                                <i class="fa fa-tag"></i>
                                <span>@lang('Service Lines')</span>
                            </a>
                        </li>
                    @endpermission
                        
                    @permission('admin.groups.index')
                        <li class="{{ route_starts_with('admin.groups') }}">
                            <a href="{{ route('admin.groups.index') }}">
                                <i class="fa fa-flag-o"></i>
                                <span>@lang('Groups')</span>
                            </a>
                        </li>
                    @endpermission
                        
                    @permission('admin.regions.index')
                        <li class="{{ route_starts_with('admin.regions') }}">
                            <a href="{{ route('admin.regions.index') }}">
                                <i class="fa fa-globe"></i>
                                <span>@lang('Operating Units')</span>
                            </a>
                        </li>
                    @endpermission
                        
                    @permission('admin.divisions.index')
                        <li class="{{ route_starts_with('admin.divisions') }}">
                            <a href="{{ route('admin.divisions.index') }}">
                                <i class="fa fa-map-marker"></i>
                                <span>@lang('Alliance OU Divisions')</span>
                            </a>
                        </li>
                    @endpermission
                        
                    @permission('admin.contractLogs.index')
                        <li class="{{ route_starts_with('admin.contractLogs') }}">
                            <a href="{{ route('admin.contractLogs.index') }}">
                                <i class="fa fa-history"></i>
                                <span>@lang('Contract Logs')</span>
                            </a>
                        </li>
                    @endpermission

                </ul>
            </section>
        </aside>

        <div class="content-wrapper">
            <section class="content">
                <div class="box">
                    <div class="box-header with-border @yield('content-header-class')">
                        <h1 class="box-title">

                            @yield('content-header')

                        </h1>

                        <div class="box-tools pull-right">

                            @yield('tools')

                        </div>
                    </div>
                    <div class="box-body">
                        @yield('content')
                    </div>
                </div>
            </section>
        </div>

        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>@lang('Version')</b> 1.0.0
            </div>
            <strong>@lang('Copyright') &copy; 2017 <a href="/">{{ config('app.name') }}</a>.</strong> @lang('All rights reserved').
        </footer>

    </div>
@endsection
