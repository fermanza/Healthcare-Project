@extends('layouts.admin')

@section('content-header', __('New Region'))

@section('content')
    @include('admin.regions.form')
@endsection
