@extends('layouts/master')

@section('body')
    <div class="text-center">
        <h1>@lang('Unauthorized')</h1>
        <p>@lang("You don't have enough permissions to view this page")</p>
        <div>
            <a href="/" class="btn btn-default">
                <i class="fa fa-home"></i>
            </a>
        </div>
    </div>
@endsection
