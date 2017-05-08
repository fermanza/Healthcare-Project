@extends('layouts.admin')

@section('content-header', __('Edit').' '.$contractLog->id)

@section('content')
    @include('admin.contractLogs.form')
@endsection
