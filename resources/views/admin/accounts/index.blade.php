@extends('layouts.admin')

@section('content-header', __('Accounts'))

@section('tools')
    <a href="{{ route('admin.accounts.create') }}" class="btn btn-sm btn-success">
        <i class="fa fa-plus"></i>
        New
    </a>
@endsection

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
                    <tr class="{{ $account->isRecentlyCreated() ? 'success' : '' }}">
                        <td>{{ $account->name }}</td>
                        <td>{{ $account->site_code }}</td>
                        <td>{{ $account->city }}</td>
                        <td>{{ $account->state }}</td>
                        <td>{{ $account->start_date }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.accounts.edit', [$account]) }}" class="btn btn-xs btn-primary">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a 
                                href="javascript:;"
                                class="btn btn-xs btn-danger deletes-record"
                                data-action="{{ route('admin.accounts.destroy', [$account]) }}"
                                data-record="{{ $account->id }}"
                                data-name="{{ $account->name }}"
                            >
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
