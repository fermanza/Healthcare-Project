@extends('layouts.admin')

@section('content-header', __('New Position Type'))

@section('content')
    @include('admin.positionTypes.form')
@endsection
