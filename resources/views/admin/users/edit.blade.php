@extends('layouts.admin')

@section('content-header', __('Edit').' '.$user->name)

@section('content')
    @include('admin.users.form')
@endsection
