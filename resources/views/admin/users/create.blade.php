@extends('layouts.admin')

@section('content-header', __('New User'))

@section('content')
    @include('admin.users.form')
@endsection
