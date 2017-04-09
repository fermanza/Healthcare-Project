@extends('layouts.admin')

@section('content-header', __('New Person'))

@section('content')
    @include('admin.people.form')
@endsection
