@extends('layouts.admin')

@section('content-header', __('Edit').' '.$account->name)

@section('content')
    @include('admin.accounts.form')
@endsection
