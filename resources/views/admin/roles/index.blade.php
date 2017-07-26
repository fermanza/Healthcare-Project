@extends('layouts.admin')

@section('content-header', __('Roles'))

@section('tools')
    @permission('admin.roles.create')
        <a href="{{ route('admin.roles.create') }}" class="btn btn-sm btn-success">
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
                    <th class="mw110">@lang('Display Name')</th>
                    <th class="mw110">@lang('Name')</th>
                    <th class="mw200 w100">@lang('Description')</th>
                    <th class="mw50">@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->display_name }}</td>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->description }}</td>
                        <td class="text-center">
                            @permission('admin.roles.edit')
                                <a href="{{ route('admin.roles.edit', [$role]) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endpermission
                                
                            @permission('admin.roles.destroy')
                                <a 
                                    href="javascript:;"
                                    class="btn btn-xs btn-danger deletes-record"
                                    data-action="{{ route('admin.roles.destroy', [$role]) }}"
                                    data-record="{{ $role->id }}"
                                    data-name="{{ $role->display_name }}"
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
