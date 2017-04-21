@extends('layouts.admin')

@section('content-header-class', $account->hasEnded() ? 'bg-danger' : '')

@section('content-header', __('Edit').' '.$account->name)

@section('tools')
    @if ($account->hasEnded())
        <strong>@lang('Account has ended.')</strong>
    @endif
@endsection

@section('content')
    @include('admin.accounts.form')
@endsection
