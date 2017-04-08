@extends('layouts.admin')

@section('content-header', __('Edit').' '.$file->name)

@section('content')
    @include('admin.files.form')
@endsection
