@extends('layouts.master')

@section('body-class', 'hold-transition login-page')

@section('body')
    <div class="login-box">
        <div class="login-logo">
            {{ config('app.name') }}
        </div>
        <div class="login-box-body">
            
            @yield('content')

        </div>
    </div>
@endsection
