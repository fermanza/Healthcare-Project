@extends('layouts.admin')

@section('content-header', __('Edit Dashboard'))

@section('content')
    @include('admin.dashboards.form')
@endsection
