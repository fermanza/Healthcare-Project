@extends('layouts.admin')

@section('content-header', __('New Employee'))

@section('content')
    @include('admin.employees.form')
@endsection
