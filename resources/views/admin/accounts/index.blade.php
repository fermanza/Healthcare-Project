@extends('layouts.admin')

@section('content-header', __('Accounts'))

@section('content')
    <div class="table-responsive">
        <table class="table table-hover table-bordered datatable">
            <thead>
                <tr>
                    <th>@lang('Name')</th>
                    <th>@lang('Site Code')</th>
                    <th>@lang('City')</th>
                    <th>@lang('State')</th>
                    <th>@lang('Start Date')</th>
                    <th>@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accounts as $account)
                    <tr>
                        <td>{{ $account->name }}</td>
                        <td>{{ $account->site_code }}</td>
                        <td>{{ $account->city }}</td>
                        <td>{{ $account->state }}</td>
                        <td>{{ $account->start_date }}</td>
                        <td class="text-center">
                            <a href="javascript:;" class="btn btn-xs btn-primary">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a href="javascript:;" class="btn btn-xs btn-danger">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
