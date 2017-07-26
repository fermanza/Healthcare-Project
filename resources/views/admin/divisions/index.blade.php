@extends('layouts.admin')

@section('content-header', __('Divisions'))

@section('tools')
    @permission('admin.divisions.create')
        <a href="{{ route('admin.divisions.create') }}" class="btn btn-sm btn-success">
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
                    <th class="mw110 w100">@lang('Name')</th>
                    <th class="mw40">@lang('Code')</th>
                    <th class="mw30">@lang('Is JV')</th>
                    <th class="mw100">@lang('Group')</th>
                    <th class="mw50">@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($divisions as $division)
                    <tr>
                        <td>{{ $division->name }}</td>
                        <td>{{ $division->code }}</td>
                        <td>{{ $division->isJV ? __('Yes') : __('No') }}</td>
                        <td>{{ $division->group->name }}</td>
                        <td class="text-center">
                            @permission('admin.divisions.edit')
                                <a href="{{ route('admin.divisions.edit', [$division]) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endpermission
                            
                            @permission('admin.divisions.destroy')
                                <a 
                                    href="javascript:;"
                                    class="btn btn-xs btn-danger deletes-record"
                                    data-action="{{ route('admin.divisions.destroy', [$division]) }}"
                                    data-record="{{ $division->id }}"
                                    data-name="{{ $division->name }}"
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
