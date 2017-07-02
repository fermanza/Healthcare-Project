@extends('layouts.admin')

@section('content-header', __('Regions'))

@section('tools')
    @permission('admin.regions.create')
        <a href="{{ route('admin.regions.create') }}" class="btn btn-sm btn-success">
            <i class="fa fa-plus"></i>
            New
        </a>
    @endpermission
@endsection

@section('content')
    <div class="table-responsive">
        <table class="table table-hover table-bordered datatable">
            <thead>
                <tr>
                    <th class="mw200 w50">@lang('Name')</th>
                    <th class="mw150">@lang('Code')</th>
                    <th class="mw100">@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($regions as $region)
                    <tr>
                        <td>{{ $region->name }}</td>
                        <td>{{ $region->code }}</td>
                        <td class="text-center">
                            @permission('admin.regions.edit')
                                <a href="{{ route('admin.regions.edit', [$region]) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endpermission
                            
                            @permission('admin.regions.destroy')
                                <a 
                                    href="javascript:;"
                                    class="btn btn-xs btn-danger deletes-record"
                                    data-action="{{ route('admin.regions.destroy', [$region]) }}"
                                    data-record="{{ $region->id }}"
                                    data-name="{{ $region->name }}"
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
