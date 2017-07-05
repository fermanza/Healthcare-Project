@extends('layouts.admin')

@section('content-header', __('Employees'))

@section('tools')
    @permission('admin.employees.create')
        <a href="{{ route('admin.employees.create') }}" class="btn btn-sm btn-success">
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
                    <th class="mw200 w100">@lang('Full Name')</th>
                    <th class="mw150">@lang('Type')</th>
                    <th class="mw100">@lang('Status')</th>
                    <th class="mw100">@lang('Position Type')</th>
                    <th class="mw150">@lang('Manager')</th>
                    <th class="mw100">@lang('ED Percent')</th>
                    <th class="mw100">@lang('IPS Percent')</th>
                    <th class="mw100">@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                    <tr>
                        <td>{{ $employee->fullName() }}</td>
                        <td>{{ $employee->employeeType }}</td>
                        <td>{{ $employee->status->name }}</td>
                        <td>{{ $employee->positionType ? $employee->positionType->name : '' }}</td>
                        <td>{{ $employee->manager ? $employee->manager->fullName() : '' }}</td>
                        <td>{{ number_format($employee->EDPercent, 1) }}</td>
                        <td>{{ number_format($employee->IPSPercent, 1) }}</td>
                        <td class="text-center">
                            @permission('admin.employees.edit')
                                <a href="{{ route('admin.employees.edit', [$employee]) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endpermission
                                
                            @permission('admin.employees.destroy')
                                <a 
                                    href="javascript:;"
                                    class="btn btn-xs btn-danger deletes-record"
                                    data-action="{{ route('admin.employees.destroy', [$employee]) }}"
                                    data-record="{{ $employee->id }}"
                                    data-name="{{ $employee->fullName() }}"
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
