@extends('layouts.admin')

@section('content-header', __('New Practice'))

@section('content')
    @include('admin.practices.form')
@endsection
