@extends('layouts.admin')

@section('content-header', __('New Role'))

@section('content')
    @include('admin.roles.form')
@endsection
