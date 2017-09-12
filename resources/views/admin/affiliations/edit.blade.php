@extends('layouts.admin')

@section('content-header', __('Edit Affiliation'))

@section('content')
    @include('admin.affiliations.form')
@endsection
