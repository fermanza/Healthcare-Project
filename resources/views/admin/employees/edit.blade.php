@extends('layouts.admin')

@section('content-header', __('Edit').' '.$employee->fullName())

@section('content')
    @include('admin.employees.form')
@endsection
