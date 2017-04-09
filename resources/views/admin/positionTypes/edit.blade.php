@extends('layouts.admin')

@section('content-header', __('Edit').' '.$positionType->name)

@section('content')
    @include('admin.positionTypes.form')
@endsection
