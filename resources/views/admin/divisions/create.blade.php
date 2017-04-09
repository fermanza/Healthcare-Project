@extends('layouts.admin')

@section('content-header', __('New Division'))

@section('content')
    @include('admin.divisions.form')
@endsection
