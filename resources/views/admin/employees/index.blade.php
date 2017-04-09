@extends('layouts.admin')

@section('content-header', __('Employees'))

@section('tools')
    <a href="{{ route('admin.employees.create') }}" class="btn btn-sm btn-success">
        <i class="fa fa-plus"></i>
        New
    </a>
@endsection

@section('content')
    <div class="table-responsive">
        <table class="table table-hover table-bordered datatable">
            <thead>
                <tr>
                    <th class="mw200 w100">@lang('Full Name')</th>
                    <th class="mw150">@lang('Type')</th>
                    <th class="mw100">@lang('Is Full Time')</th>
                    <th class="mw100">@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                    <tr>
                        <td>{{ $employee->fullName() }}</td>
                        <td>{{ $employee->type }}</td>
                        <td>{{ $employee->is_full_time ? __('Yes') : __('No') }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.employees.edit', [$employee]) }}" class="btn btn-xs btn-primary">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a 
                                href="javascript:;"
                                class="btn btn-xs btn-danger deletes-record"
                                data-action="{{ route('admin.employees.destroy', [$employee]) }}"
                                data-record="{{ $employee->id }}"
                                data-name="{{ $employee->fullName() }}"
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
