@extends('layouts.admin')

@section('content-header', __('Edit').' '.$person->name)

@section('content')
    @include('admin.people.form')
@endsection
