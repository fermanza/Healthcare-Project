@extends('layouts.admin')

@section('content-header', __('Dashboards'))

@section('tools')
    @permission('admin.affiliations.create')
        <a href="{{ route('admin.affiliations.create') }}" class="btn btn-sm btn-success">
            <i class="fa fa-plus"></i>
            @lang('New')
        </a>
    @endpermission
@endsection

@section('content')
    <div class="table-responsive mh400">
        <table class="table table-hover table-bordered datatable" data-datatable-config='{"order": [[ 0, "desc" ]]}'>
            <thead>
                <tr>
                    <th class="mw40">@lang('ID')</th>
                    <th class="mw200 w50">@lang('Name')</th>
                    <th class="mw200 w50">@lang('Display Name')</th>
                    <th class="mw50">@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($affiliations as $affiliation)
                    <tr>
                        <td>{{ $affiliation->id }}</td>
                        <td>{{ $affiliation->name }}</td>
                        <td>{{ $affiliation->displayName }}</td>
                        <td class="text-center">
                            @permission('admin.affiliations.edit')
                                <a href="{{ route('admin.affiliations.edit', [$affiliation]) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endpermission
                                
                            @permission('admin.affiliations.destroy')
                                <a 
                                    href="javascript:;"
                                    class="btn btn-xs btn-danger deletes-record"
                                    data-action="{{ route('admin.affiliations.destroy', [$affiliation]) }}"
                                    data-name="{{ $affiliation->name }}"
                                >
                                    <i class="fa fa-trash"></i>
                                </a>
                            @endpermission
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
