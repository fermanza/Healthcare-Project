@extends('layouts.admin')

@section('content-header', __('New Permission'))

@section('content')
    @include('admin.permissions.form')
@endsection
