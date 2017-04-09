@extends('layouts.admin')

@section('content-header', __('Edit').' '.$practice->name)

@section('content')
    @include('admin.practices.form')
@endsection
