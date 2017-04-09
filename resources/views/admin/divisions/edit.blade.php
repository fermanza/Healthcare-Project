@extends('layouts.admin')

@section('content-header', __('Edit').' '.$division->name)

@section('content')
    @include('admin.divisions.form')
@endsection
