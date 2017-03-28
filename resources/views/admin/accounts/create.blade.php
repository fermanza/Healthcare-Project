@extends('layouts.admin')

@section('content-header', __('New Account'))

@section('content')
    @include('admin.accounts.form')
@endsection
