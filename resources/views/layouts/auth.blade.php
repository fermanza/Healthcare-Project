@extends('layouts.master')

@section('body-class', 'hold-transition login-page')

@section('body')
    <div class="login-box">
        <div class="login-logo">
            <img src="{{ asset('img/emcare-logo-sm.png') }}" alt="Logo small" />
        </div>
        <div class="login-box-body">
            
            @yield('content')

        </div>
    </div>
@endsection
