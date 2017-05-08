@extends('layouts.admin')

@section('content-header', __('New Contract Log'))

@section('content')
    @include('admin.contractLogs.form')
@endsection
