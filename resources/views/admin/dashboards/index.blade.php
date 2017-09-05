@extends('layouts.admin')

@section('content-header', __('Dashboards'))

@section('tools')
    @permission('admin.dashboards.create')
        <a href="{{ route('admin.dashboards.create') }}" class="btn btn-sm btn-success">
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
                    <th class="mw200 w50">@lang('Description')</th>
                    <th class="mw200 w50">@lang('URL')</th>
                    <th class="mw50">@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dashboards as $dashboard)
                    <tr>
                        <td>{{ $dashboard->id }}</td>
                        <td>{{ $dashboard->name }}</td>
                        <td>{{ $dashboard->description }}</td>
                        <td>{{ $dashboard->url }}</td>
                        <td class="text-center">
                            @permission('admin.dashboards.edit')
                                <a href="{{ route('admin.dashboards.edit', [$dashboard]) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endpermission
                                
                            @permission('admin.dashboards.destroy')
                                <a 
                                    href="javascript:;"
                                    class="btn btn-xs btn-danger deletes-record"
                                    data-action="{{ route('admin.dashboards.destroy', [$dashboard]) }}"
                                    data-name="{{ $dashboard->name }}"
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
