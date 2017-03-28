@extends('layouts.master')

@section('body-class', 'skin-blue sidebar-mini')

@section('body')
    <div class="wrapper">

        <header class="main-header">
            <a href="../../index2.html" class="logo">
                <span class="logo-mini">AA</span>
                <span class="logo-lg">{{ config('app.name') }}</span>
            </a>
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
                        <a href="{{ route('admin.accounts') }}">
                            <i class="fa fa-hospital-o"></i>
                            <span>Accounts</span>
                        </a>
                    </li>
                </ul>
            </section>
        </aside>

        <div class="content-wrapper">
            <section class="content-header">
                <h1>

                    @yield('content-header')

                </h1>

                @yield('breadcrumb')

            </section>
            
            <section class="content">
                <div class="box">
                    <div class="box-body">

                    @yield('content')
                
                    </div>
                </div>
            </section>
        </div>

        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> 1.0.0
            </div>
            <strong>Copyright &copy; 2017 <a href="/">{{ config('app.name') }}</a>.</strong> All rights reserved.
        </footer>

    </div>
@endsection
