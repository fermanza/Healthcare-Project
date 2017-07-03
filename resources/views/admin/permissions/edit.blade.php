@extends('layouts.admin')

@section('content-header', __('Edit').' '.$permission->display_name)

@section('content')
    @include('admin.permissions.form')
@endsection
