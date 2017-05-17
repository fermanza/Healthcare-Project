@extends('layouts.master')

@section('body-class', 'hold-transition login-page')

@section('body')
    <div class="login-box">
        <div class="login-box-body">
            
            <div class="login-logo">
                <img src="{{ asset('img/app-logo-sm.png') }}" alt="Logo small" />
            </div>

            @yield('content')

        </div>
    </div>
@endsection
