@extends('layouts.admin')

@section('content-header', __('Edit').' '.$group->name)

@section('content')
    @include('admin.groups.form')
@endsection
