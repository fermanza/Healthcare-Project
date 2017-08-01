@extends('layouts.admin')

@section('content-header', __('Users'))

@section('tools')
    @permission('admin.users.create')
        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-success">
            <i class="fa fa-plus"></i>
            @lang('New')
        </a>
    @endpermission
@endsection

@section('content')
    <div class="table-responsive">
        <table class="table table-hover table-bordered datatable">
            <thead>
                <tr>
                    <th class="mw150 w50">@lang('Name')</th>
                    <th class="mw150 w50">@lang('E-mail')</th>
                    <th class="mw110">@lang('Employee')</th>
                    <th class="mw110">@lang('Role')</th>
                    <th class="mw50">@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->employee ? $user->employee->fullName() : '' }}</td>
                        <td>{{ $user->roles->implode('display_name', ', ') }}</td>
                        <td class="text-center">
                            @permission('admin.users.edit')
                                <a href="{{ route('admin.users.edit', [$user]) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endpermission
                                
                            @permission('admin.users.destroy')
                                <a 
                                    href="javascript:;"
                                    class="btn btn-xs btn-danger deletes-record"
                                    data-action="{{ route('admin.users.destroy', [$user]) }}"
                                    data-record="{{ $user->id }}"
                                    data-name="{{ $user->name }}"
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
