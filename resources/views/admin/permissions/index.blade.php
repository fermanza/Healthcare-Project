@extends('layouts.admin')

@section('content-header', __('Permissions'))

@section('content')
    <div class="table-responsive">
        <table class="table table-hover table-bordered datatable">
            <thead>
                <tr>
                    <th class="mw150 w50">@lang('Display Name')</th>
                    <th class="mw150 w50">@lang('Name')</th>
                    <th class="mw50">@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permissions as $permission)
                    <tr>
                        <td>{{ $permission->display_name }}</td>
                        <td>{{ $permission->name }}</td>
                        <td class="text-center">
                            @permission('admin.permissions.edit')
                                <a href="{{ route('admin.permissions.edit', [$permission]) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endpermission
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
