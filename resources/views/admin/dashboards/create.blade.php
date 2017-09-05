@extends('layouts.admin')

@section('content-header', __('New Dashboard'))

@section('content')
    @include('admin.dashboards.form')
@endsection
