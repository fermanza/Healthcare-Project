@extends('layouts.master')

@section('body-class')
    skin-blue-light sidebar-mini {{ session('sidebar-collapsed') ? 'sidebar-collapse' : '' }}
@endsection

@section('body')
    <div class="wrapper">

        <header class="main-header">
            <span class="logo">
                <span class="logo-mini">Em</span>
                <span class="logo-lg">
                    <img src="{{ asset('img/emcare-logo-sm.png') }}" alt="Logo small" width="150" />
                </span>
            </span>
            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="{{ route('logout') }}">
                                <i class="fa fa-sign-out"></i>
                                Log out
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <aside class="main-sidebar">
            <section class="sidebar">
                <ul class="sidebar-menu">
                    <li class="{{ route_starts_with('admin.accounts') }}">
                        <a href="{{ route('admin.accounts.index') }}">
                            <i class="fa fa-hospital-o"></i>
                            <span>@lang('Accounts')</span>
                        </a>
                    </li>
                    <li class="{{ route_starts_with('admin.files') }}">
                        <a href="{{ route('admin.files.index') }}">
                            <i class="fa fa-upload"></i>
                            <span>@lang('Files')</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <i class="fa fa-id-card-o"></i>
                            <span>@lang('Employees')</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <i class="fa fa-id-badge"></i>
                            <span>@lang('Position Types')</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <i class="fa fa-users"></i>
                            <span>@lang('People')</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <i class="fa fa-map-marker"></i>
                            <span>@lang('Divisions')</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <i class="fa fa-tag"></i>
                            <span>@lang('Practices')</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <i class="fa fa-flag-o"></i>
                            <span>@lang('Groups')</span>
                        </a>
                    </li>
                </ul>
            </section>
        </aside>

        <div class="content-wrapper">
            <section class="content">
                <div class="box">
                    <div class="box-header with-border">
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
