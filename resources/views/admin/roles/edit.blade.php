@extends('layouts.admin')

@section('content-header', __('Edit').' '.$role->display_name)

@section('content')
    @include('admin.roles.form')
@endsection
