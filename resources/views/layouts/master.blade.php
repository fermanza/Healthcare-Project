<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @stack('metas')
        
        <title>{{ config('app.name') }}</title>

        <!-- Styles -->
        <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
        @stack('styles')
        @stack('links')

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="@yield('body-class')">
        <div>
            @yield('body')
        </div>

        <form id="delete-form" action="" method="POST" data-message="@lang('Are you sure you want to delete')">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
        </form>
        
        <!-- Scripts -->
        <script src="{{ mix('/js/manifest.js') }}"></script>
        <script src="{{ mix('/js/vendor.js') }}"></script>
        <script src="{{ mix('/js/app.js') }}"></script>
        @include('common.flash-message')
        @stack('scripts')
    </body>
</html>