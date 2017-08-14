@extends('layouts.admin')

@section('content-header', __('Service Lines'))

@section('tools')
    @permission('admin.practices.create')
        <a href="{{ route('admin.practices.create') }}" class="btn btn-sm btn-success">
            <i class="fa fa-plus"></i>
            @lang('New')
        </a>
    @endpermission
@endsection

@section('content')
    <div class="table-responsive mh400">
        <table class="table table-hover table-bordered datatable">
            <thead>
                <tr>
                    <th class="mw100 w100">@lang('Name')</th>
                    <th class="mw40">@lang('Code')</th>
                    <th class="mw50">@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($practices as $practice)
                    <tr>
                        <td>{{ $practice->name }}</td>
                        <td>{{ $practice->code }}</td>
                        <td class="text-center">
                            @permission('admin.practices.edit')
                                <a href="{{ route('admin.practices.edit', [$practice]) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endpermission
                            
                            @permission('admin.practices.destroy')
                                <a 
                                    href="javascript:;"
                                    class="btn btn-xs btn-danger deletes-record"
                                    data-action="{{ route('admin.practices.destroy', [$practice]) }}"
                                    data-record="{{ $practice->id }}"
                                    data-name="{{ $practice->name }}"
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
