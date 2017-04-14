@extends('layouts.admin')

@section('content-header', __('Edit').' '.$region->name)

@section('content')
    @include('admin.regions.form')
@endsection
