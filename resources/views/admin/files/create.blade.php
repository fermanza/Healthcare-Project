@extends('layouts.admin')

@section('content-header', __('New File'))

@section('content')
    @include('admin.files.form')
@endsection
