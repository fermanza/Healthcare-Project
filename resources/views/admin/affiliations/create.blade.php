@extends('layouts.admin')

@section('content-header', __('New Affiliation'))

@section('content')
    @include('admin.affiliations.form')
@endsection
