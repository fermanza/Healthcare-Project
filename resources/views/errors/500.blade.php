@extends('layouts/master')

@section('body')
    <div class="text-center">
        <h1>@lang('Sorry, there was an error with your request')</h1>
        <p>@lang("Please contact system support.")</p>
        <div>
            <a href="/" class="btn btn-default">
                <i class="fa fa-home"></i>
            </a>
        </div>
    </div>
@endsection
