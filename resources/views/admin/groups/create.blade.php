@extends('layouts.admin')

@section('content-header', __('New Group'))

@section('content')
    @include('admin.groups.form')
@endsection
