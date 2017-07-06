@extends('layouts.admin')

@section('content-header', __('Groups'))

@section('tools')
    @permission('admin.groups.create')
        <a href="{{ route('admin.groups.create') }}" class="btn btn-sm btn-success">
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
                    <th class="mw200 w50">@lang('Operating Unit')</th>
                    <th class="mw150">@lang('Code')</th>
                    <th class="mw100">@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groups as $group)
                    <tr>
                        <td>{{ $group->name }}</td>
                        <td>{{ $group->region->name }}</td>
                        <td>{{ $group->code }}</td>
                        <td class="text-center">
                            @permission('admin.groups.edit')
                                <a href="{{ route('admin.groups.edit', [$group]) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endpermission
                            
                            @permission('admin.groups.destroy')
                                <a 
                                    href="javascript:;"
                                    class="btn btn-xs btn-danger deletes-record"
                                    data-action="{{ route('admin.groups.destroy', [$group]) }}"
                                    data-record="{{ $group->id }}"
                                    data-name="{{ $group->name }}"
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
